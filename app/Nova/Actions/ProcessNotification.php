<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

use function Laravel\Prompts\text;
use function PHPUnit\Framework\isNull;

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
            if ($notification->source === 'bokun') {
                // process bokun notification
                $this->processBokunNotification($notification);
            }

            if ($notification->source == 'ecwid') {
                // process ecwid notification
                $this->processEcwidNotification($notification);
            }

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

    private function processBokunNotification(\APP\Models\Notification $notification)
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


        $order = \App\Models\Order::where('ShopOrderNumber', $payloadArray->confirmationCode)
            ->where('external_connection_id', $external_connection->id)->first();
        if ($order == null)
            $order = new \App\Models\Order();

        $order->CustomerName = $payloadArray->customer->firstName . ' ' . $payloadArray->customer->firstName;
        $order->CustomerEmail = $payloadArray->customer->email;
        // $order->CustomerPhone = $payloadArray->customer->phoneNumber;
        $order->ShopOrderNumber = $payloadArray->confirmationCode;
        $order->OrderDateTime = date("Y-m-d H:i:s", $payloadArray->creationDate / 1000); //$this->getDateFromTimestamp($payloadArray->creationDate);
        $order->OrderTotal = $payloadArray->totalPrice;
        $order->external_connection_id = $external_connection->id;
        $order->save();

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
            $products = $external_product->products()->get();

            if (!isset($products)) {
                $notification->status = 'processed_error';
                $notification->result = 'No Product attached with external_product_id(' . $external_product->external_product_id . ') and external_connection(' . $external_connection->name . ').';
                $notification->save();
                return;
            }

            //$notification->status = 'processed_error';
            //            $notification->result = 'Products count = ' . $products->count();
            //            $notification->save();
            //
            //            $text ="";
            //            foreach ($products->get() as $product){
            //                $text .= $product->Name;
            //            }
            //            $notification->result = $text;
            //            $notification->save();
            //            return;

            foreach ($products as $product) {

                $orderitem = \App\Models\OrderItem::where('OrderId', $order->Id)
                    ->where('ProductId', $product->Id)
                    ->where('ExrenalId', $activityBooking->barcode->value)
                    ->first();

                if ($orderitem == null)
                    $orderitem = new \app\models\orderitem();

                $orderitem->ProductId = $product->Id;
                $orderitem->servicedatetime = date("y-m-d h:i:s", $activityBooking->date / 1000);
                $orderitem->Adults = $activityBooking->totalParticipants;
                $orderitem->ExrenalId = $activityBooking->barcode->value;
                $orderitem->OrderId = $order->Id;

                $orderitem->save();
            }
        }
        $notification->status = 'processed_ok';
        $notification->result = "order created with Id " . $order->Id;

        $notification->save();
    }

    private function processEcwidNotification(\APP\Models\Notification $notification)
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


        if (($payloadArray->eventType != 'order.created') || $payloadArray->data->newPaymentStatus != 'PAID' || $payloadArray->data->newFulfillmentStatus != 'AWAITING_PROCESSING') {
            $notification->status = 'ingnored';
            $notification->result = 'Ignored because system only process PAID orders.';
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

        $credentilasMapping = \App\Models\CredentialExternalConnectionMapping::where('external_connection_id', $external_connection->id)->first();
        if (!isset($credentilasMapping)) {
            $notification->status = 'processed_error';
            $notification->result = 'No  external connection - credentila mapping found for External_Connection (' . $external_connection->name . ')';
            $notification->save();
            return;
        }

        $notification->result = 'ExternalConnection-Credential mapping found!';
        $notification->save();

        $credentials = $credentilasMapping->credential;

        if (!isset($credentials)) {
            $notification->status = 'processed_error';
            $notification->result = 'No  credentials found.';
            $notification->save();
            return;
        }
        $storeId = $credentials->Username;
        $token = $credentials->Password;


        $ecwidResponse = $this->getEcwidOrder($storeId, $token, $payloadArray->data->orderId);

        $notification->result = $ecwidResponse;
        $notification->save();
        return;

        // TODO: check order payments
        // TODO: check if order items are configured or not
        // TODO: importn order items in system
        // fetch order from ecwid
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
