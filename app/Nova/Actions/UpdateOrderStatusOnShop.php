<?php

namespace App\Nova\Actions;

use App\Models\Communication;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class UpdateOrderStatusOnShop extends Action
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
        $succs = 0;
        foreach ($models as $order) {
            if ($order->Status != 2)
                continue;

            $this->setOrderStatus($order);
            $succs++;
        }

        return Action::message('Order update request sent for ' . $succs . '/' . $models->count() . ' orders.');
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

    public function setOrderStatus(\App\Models\Order $order)
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
            "fulfillmentStatus" => "DELIVERED"
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
            $comm->description = "OrderStatus sent to shop by " . Auth::user()?->name . ' for ' . $order->ShopOrderNumber;
        else
            $comm->description = "Error: OrderStatus sent to shop by " . Auth::user()?->name . ' for ' . $order->ShopOrderNumber;

        $order->communications()->save($comm);
    }
}
