<?php

namespace App\Helpers;

use App\Models\Communication;
use Illuminate\Support\Facades\Auth;

class EcwidHelpers
{
    public static function setOrderStatus(\App\Models\Order $order, $status = "DELIVERED")
    {
        if ($order->externalConnection->connection_type != 'ecwid')
            return;

        $comm = new Communication();
        $comm->action = 'webshop order status update';

        $credentialMapping = \App\Models\ExternalConnectionMapping::where('external_connection_id', $order->externalConnection->id)->first();
        if (is_null($credentialMapping)) {
            $comm->description = "Missing ExternalConnectionMapping for external_connection " . $order->externalConnection->name;
            $order->communications()->save($comm);
            return;
        }

        // \Log::info($credentialMapping);
        $credentials = $credentialMapping->shopCredential;

        if (!isset($credentials) || !$credentials->Active) {
            $comm->description = "Missing or inactive ShopCredentials for external_connection " . $order->externalConnection->name;
            $order->communications()->save($comm);
            return;
        }


        $storeId = $credentials->Username;
        $token = $credentials->Password;
        $request = [
            "fulfillmentStatus" => $status
        ];
        $shopOrderNumber = $order->ShopOrderNumber;
        // $shopOrderNumber = 'G2D21106720';


        $client = new \GuzzleHttp\Client();
        $url = "https://app.ecwid.com/api/v3/{$storeId}/orders/{$shopOrderNumber}";
        $response = $client->request('PUT', $url, [
            'body' => json_encode($request),
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
        ]);


        if ($response->getStatusCode() == 200)
            $comm->description = "OrderStatus '" . $status . "' sent to shop by " . Auth::user()?->name . ' for ' . $order->ShopOrderNumber;
        else
            $comm->description = "Error: OrderStatus '" . $status . "' sent to shop by " . Auth::user()?->name . ' for ' . $order->ShopOrderNumber;

        $order->communications()->save($comm);
    }
}
