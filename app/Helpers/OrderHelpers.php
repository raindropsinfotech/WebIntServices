<?php

namespace App\Helpers;

use App\Events\OrderCreatedEvent;

class OrderHelpers
{
    public static function getOrder($external_connection_id, $shopOrderNumber,  $orderTotal, $orderDate, $customerEmail, $customerName, $customerPhone, $payment_reference)
    {
        // first check order number
        // if external connection is not attached then attach external connection.
        $order = \App\Models\Order::where('ShopOrderNumber', $shopOrderNumber)->first();
        $is_new = false;
        // ->where('external_connection_id', $external_connection_id)->first();
        if ($order == null) {
            $order = new \App\Models\Order();
            $is_new = true;
        }

        $order->CustomerName = $customerName;
        $order->CustomerEmail = $customerEmail;
        // $order->CustomerPhone = $payloadArray->customer->phoneNumber;
        $order->ShopOrderNumber = $shopOrderNumber;
        $order->OrderDateTime = $orderDate; //$this->getDateFromTimestamp($payloadArray->creationDate);
        $order->OrderTotal = $orderTotal;
        $order->external_connection_id = $external_connection_id;
        $order->PaymentReference = $payment_reference;
        $order->save();

        if ($is_new)
            event(new OrderCreatedEvent($order));

        return $order;
    }

    public static function addOrdeItem($orderId, $productId, $shopOrderItemRference,  $serviceDate, $adultsCount, $childCount = 0)
    {
        $orderitem = \App\Models\OrderItem::where('OrderId', $orderId)
            ->where('ProductId', $productId)
            ->where('ExrenalId', $shopOrderItemRference)
            ->first();

        if ($orderitem == null)
            $orderitem = new \app\models\orderitem();

        $orderitem->ProductId = $productId;
        $orderitem->servicedatetime = $serviceDate;
        $orderitem->Adults = $adultsCount;
        $orderitem->Children = $childCount;

        $orderitem->ExrenalId = $shopOrderItemRference;
        $orderitem->OrderId = $orderId;

        $orderitem->save();
    }
}
