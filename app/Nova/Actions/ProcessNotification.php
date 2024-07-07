<?php

namespace App\Nova\Actions;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;


class ProcessNotification extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {

        $totalNotification = count($models);
        $processedNotifications = 0;
        foreach ($models as $notification) {

            $this->processNotification($notification);

            $processedNotifications++;
        }

        return Action::message('Notification(s) processed: ' . $processedNotifications . ' / ' . $totalNotification);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }

    public function processNotification(\APP\Models\Notification $notification)
    {
        if ($notification->source === 'bokun') {
            // process bokun notification
            $this->processBokunNotification($notification);
        }

        if ($notification->source == 'ecwid') {
            // process ecwid notification
            $this->processEcwidNotification($notification);
        }
        if ($notification->source == 'woo') {
            // process ecwid notification
            \App\Helpers\WooHelpers::processWooNotification($notification);
        }
    }

    public function processBokunNotification(\APP\Models\Notification $notification)
    {
        if ($notification->source != 'bokun')
            return;

        $payloadArray = json_decode($notification->payload);

        if (!isset($payloadArray->seller) || !isset($payloadArray->seller->id)) {
            $notification->status = 'processed_error';
            $notification->result = 'No seller info found.';
            $notification->save();

            return;
        }
        $sellerId = $payloadArray->seller->id;
        if (!isset($sellerId)) {

            $notification->status = 'processed_error';
            $notification->result = 'No seller Id found on the payload' . json_encode($payloadArray->seller->id);
            $notification->save();
            return;
        }

        // check if external_connection for bokun
        $external_connection = \App\Models\ExternalConnection::where('connection_type', 'bokun')->where('external_id', $sellerId)->first();

        if ($external_connection == null) {
            $notification->status = 'processed_error';
            $notification->result = 'No external_connection found for seller ' . $sellerId;
            $notification->save();
            return;
        }

        $customerName =  $payloadArray->customer->firstName . ' ' . $payloadArray->customer->lastName;
        $payment_reference = null;
        if ($payloadArray->bookingChannel->type == "OTA" && $payloadArray->bookingChannel->title == "Viator.com") {
            // booking is from viator
            $payment_reference = $payloadArray?->customerPayments[0]?->activeCustomerInvoiceId;
        } else {
            // booking is from Bokun
            $payment_reference = $payloadArray?->customerPayments[0]?->authorizationCode;
        }
        $order = \App\Helpers\OrderHelpers::GetOrder($external_connection->id, $payloadArray->confirmationCode, $payloadArray->totalPaid, date("Y-m-d H:i:s", $payloadArray->creationDate / 1000), $payloadArray->customer->email, $customerName, $payloadArray->customer->phoneNumber, $payment_reference);

        if (!isset($order)) {
            $notification->status = 'processed_error';
            $notification->result = 'Unable to create order.';
            $notification->save();
            return;
        }

        $notification->order_id = $order->Id;
        $notification->save();

        if (!isset($payloadArray->activityBookings)) {
            $notification->status = 'processed_error';
            $notification->result = 'No activityBookings found for order ' . $$payloadArray->confirmationCode;
            $notification->save();
            return;
        }
        foreach ($payloadArray->activityBookings as $activityBooking) {
            $external_product = \App\Models\ExternalProduct::where('external_connection_id', $external_connection->id)->where('external_product_id', $activityBooking->rateId)->first();

            if (!isset($external_product)) {
                $notification->status = 'processed_error';
                $notification->result = 'No Product found with external_product_id(' . $activityBooking->rateId . ') and external_connection(' . $external_connection->name . ').';
                $notification->save();
                return;
            }
            if (!$external_product->is_active) {
                $notification->status = 'processed_error';
                $notification->result = 'ExternalProduct with external_product_id(' . $external_product->external_product_id . ') and external_connection(' . $external_connection->name . ') is not active.';
                $notification->save();
                return;
            }
            $products = $external_product->products()->get();

            if (!isset($products)) {
                $notification->status = 'processed_error';
                $notification->result = 'No Product attached with external_product_id(' . $external_product->external_product_id . ') and external_connection(' . $external_connection->name . ').';
                $notification->save();
                return;
            }


            foreach ($products as $product) {

                $this->addOrdeItem($order->Id, $product->Id, $activityBooking->barcode->value, date("y-m-d h:i:s", $activityBooking->date / 1000), $activityBooking->totalParticipants);
            }
        }
        $notification->status = 'processed_ok';
        $notification->result = "order created with Id " . $order->Id;

        $notification->save();
    }

    public function processEcwidNotification(\APP\Models\Notification $notification)
    {
        if ($notification->source != 'ecwid')
            return;

        $payloadArray = json_decode($notification->payload);

        if (!isset($payloadArray->data) || !isset($payloadArray->storeId) || !isset($payloadArray->data->orderId)) {
            $notification->status = 'processed_error';
            $notification->result = 'No order or store info found.';
            $notification->save();
            return;
        }

        $store_id = $payloadArray->storeId;

        if (($payloadArray->eventType != 'order.created')) {
            $notification->status = 'processed_error';
            $notification->result = 'Order (' . $payloadArray->data->orderId . ') cannot be imported. Error: ecwid-eventType = ' . $payloadArray->eventType;
            $notification->save();
            return;
        }




        if ($payloadArray->data->newPaymentStatus != 'PAID') {
            $notification->status = 'processed_error';
            $notification->result = 'Order (' . $payloadArray->data->orderId . ') cannot be imported. Error: ecwid-paymentStatus = ' . $payloadArray->data->newPaymentStatus;
            $notification->save();
            return;
        }

        if ($payloadArray->data->newFulfillmentStatus != 'AWAITING_PROCESSING') {
            $notification->status = 'processed_error';
            $notification->result = 'Order (' . $payloadArray->data->orderId . ') cannot be imported. Error: ecwid-newFulfillmentStatus = ' . $payloadArray->data->newFulfillmentStatus;
            $notification->save();
            return;
        }

        $external_connection = \App\Models\ExternalConnection::where('is_active', true)->where('connection_type', 'ecwid')->where('external_id', $payloadArray->storeId)->first();
        if (!isset($external_connection)) {
            $notification->status = 'processed_error';
            $notification->result = 'No active external connection found for Ecwid (' . $payloadArray->storeId  . ')';
            $notification->save();
            return;
        }

        $credentilasMapping = \App\Models\ExternalConnectionMapping::where('external_connection_id', $external_connection->id)->first();
        if (!isset($credentilasMapping)) {
            $notification->status = 'processed_error';
            $notification->result = 'No  external connection - credentila mapping found for External_Connection (' . $external_connection->name . ')';
            $notification->save();
            return;
        }

        $notification->result = 'ExternalConnection-Credential mapping found!';
        $notification->save();

        $credentials = \App\Models\Credential::where('Id', $credentilasMapping->shop_credential_id)->first();

        if (!isset($credentials)) {
            $notification->status = 'processed_error';
            $notification->result = 'No  credentials found.';
            $notification->save();
            return;
        }
        $storeId = $credentials->Username;
        $token = $credentials->Password;


        $ecwidResponseData = $this->getEcwidOrder($storeId, $token, $payloadArray->data->orderId);

        $notification->result = $ecwidResponseData;
        $notification->save();

        $ecwidResponse = json_decode($ecwidResponseData);

        $order = \App\Helpers\OrderHelpers::getOrder($external_connection->id, $ecwidResponse->id, $ecwidResponse->total, date("y-m-d h:i:s", $ecwidResponse->createTimestamp), $ecwidResponse->email, $ecwidResponse->billingPerson->name, $ecwidResponse->billingPerson->phone, isset($ecwidResponse->externalTransactionId) ? $ecwidResponse->externalTransactionId : null);
        if (!isset($order)) {
            $notification->status = 'processed_error';
            $notification->result = 'Unable to create order.';
            $notification->save();
            return;
        }

        $notification->order_id = $order->Id;
        $notification->save();


        foreach ($ecwidResponse->items as $item) {
            $external_product = \App\Models\ExternalProduct::where('external_connection_id', $external_connection->id)->where('external_product_id', $item->sku)->first();

            if (!isset($external_product)) {
                $notification->status = 'processed_error';
                $notification->result = 'No Product found with external_product_id(' . $item->sku . ') and external_connection(' . $external_connection->name . ').';
                $notification->save();
                return;
            }

            if (!$external_product->is_active) {
                $notification->status = 'processed_error';
                $notification->result = 'ExternalProduct with external_product_id(' . $external_product->external_product_id . ') and external_connection(' . $external_connection->name . ') is not active.';
                $notification->save();
                return;
            }

            $products = $external_product->products()->get();

            if (!isset($products)) {
                $notification->status = 'processed_error';
                $notification->result = 'No Product attached with external_product_id(' . $external_product->external_product_id . ') and external_connection(' . $external_connection->name . ').';
                $notification->save();
                return;
            }


            foreach ($products as $product) {

                $serviceDate = date("y-m-d h:i:s", strtotime('+2 days'));
                if (isset($item) && isset($item->selectedOptions)) {
                    $dateElement = array_filter($item->selectedOptions, function ($obj) {
                        return $obj->type == 'DATE';
                    });
                }

                if (isset($dateElement) && count($dateElement) > 0) {
                    $serviceDate = date("y-m-d h:i:s", strtotime(head($dateElement)->value));
                }

                $this->addOrdeItem($order->Id, $product->Id, $item->id, $serviceDate, $item->quantity);
            }
        }


        $notification->status = 'processed_ok';
        $notification->result = "order created with Id " . $order->Id;

        $notification->save();
    }



    private function getDateFromTimestamp($timestamp)
    {

        $timestamp_ms = $timestamp; // Timestamp in milliseconds
        $timestamp_s = $timestamp_ms / 1000; // Convert milliseconds to seconds

        // Convert timestamp to human-readable date
        $date = date("Y-m-d H:i:s", $timestamp_s);

        echo $date; // Output: 2025-10-08 00:00:00

        return $date;
    }

    private function getEcwidOrder($storeId, $token, $orderId)
    {

        $client = new \GuzzleHttp\Client();
        $url = "https://app.ecwid.com/api/v3/{$storeId}/orders/{$orderId}";
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
        ]);


        return $response->getBody();
    }
}
