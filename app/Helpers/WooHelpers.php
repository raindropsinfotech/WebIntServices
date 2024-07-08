<?php

namespace App\Helpers;

use DateTime;

class WooHelpers
{
    public static function processWooNotification(\APP\Models\Notification $notification)
    {
        if ($notification->source != 'woo')
            return;

        $payloadArray = json_decode($notification->payload);

        if (!isset($payloadArray->id) || !isset($payloadArray->number) || !isset($payloadArray->status)) {
            $notification->status = 'processed_error';
            $notification->result = 'No order info found.';
            $notification->save();
            return;
        }


        // enable if we want to check the status == processing , comment to import all notification orders
        // if (($payloadArray->status != 'processing')) {
        //     $notification->status = 'processed_error';
        //     $notification->result = 'Order (' . $payloadArray->number . ') cannot be imported. Error: status = ' . $payloadArray->status;
        //     $notification->save();
        //     return;
        // }

        // if (!isset($payloadArray->transaction_id)) {
        //     $notification->status = 'processed_error';
        //     $notification->result = 'Order (' . $payloadArray->number . ') cannot be imported. Error: transaction_id is not set. ';
        //     $notification->save();
        //     return;
        // }

        // enable if we want to check the status == processing , comment to import all notification orders
        // if (($payloadArray->status != 'processing')) {
        //     $notification->status = 'processed_error';
        //     $notification->result = 'Order (' . $payloadArray->number . ') cannot be imported. Error: status = ' . $payloadArray->status;
        //     $notification->save();
        //     return;
        // }

        $external_connection = \App\Models\ExternalConnection::where('is_active', true)->where('connection_type', 'woo')->first();
        if (!isset($external_connection)) {
            $notification->status = 'processed_error';
            $notification->result = 'No active external connection found for Woo ';
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
        // $storeId = $credentials->Username;
        // $token = $credentials->Password;


        // $ecwidResponseData = $this->getEcwidOrder($storeId, $token, $payloadArray->data->orderId);

        // $notification->result = $ecwidResponseData;
        // $notification->save();

        // $ecwidResponse = json_decode($ecwidResponseData);
        // $payloadArray->transaction_id
        $orderTime = new DateTime($payloadArray->date_created);
        $order = \App\Helpers\OrderHelpers::getOrder($external_connection->id, $payloadArray->number, $payloadArray->total, $orderTime, $payloadArray->billing->email, $payloadArray->billing->first_name . ' ' . $payloadArray->billing->last_name, $payloadArray->billing->phone, isset($payloadArray->transaction_id) ? $payloadArray->transaction_id : null);
        if (!isset($order)) {
            $notification->status = 'processed_error';
            $notification->result = 'Unable to create order.';
            $notification->save();
            return;
        }

        $notification->order_id = $order->Id;
        $notification->save();


        foreach ($payloadArray->line_items as $item) {
            if (isset($item->bundled_items) && count($item->bundled_items) > 0)
                continue;

            $productId = $item->sku;
            $external_product = \App\Models\ExternalProduct::where('external_connection_id', $external_connection->id)->where('external_product_id', $productId)->first();

            if (!isset($external_product)) {
                $notification->status = 'processed_error';
                $notification->result = 'No Product found with external_product_id(' . $productId . ') and external_connection(' . $external_connection->name . ').';
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
                if (isset($item) && isset($item->meta_data)) {
                    $dateElement = array_filter($item->meta_data, function ($obj) {
                        return $obj->key == 'Tour Date';
                    });
                }

                if (isset($dateElement) && count($dateElement) > 0) {
                    $serviceDate = date("y-m-d h:i:s", strtotime(head($dateElement)->value));
                }

                \App\Helpers\OrderHelpers::addOrdeItem($order->Id, $product->Id, $item->id, $serviceDate, $item->quantity);
            }
        }


        $notification->status = 'processed_ok';
        $notification->result = "order created with Id " . $order->Id;

        $notification->save();
    }
}
