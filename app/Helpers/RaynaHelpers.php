<?php

namespace App\Helpers;

use App\Models\ApiLog;
use App\Models\Communication;
use App\Models\ExternalConnection;
use App\Models\ExternalProduct;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\ActionResponse;

class RaynaHelpers
{

    public static function ProcessOrderItems(Collection $models, $user)
    {
        $externalConnection = ExternalConnection::where('connection_type', 'rayna')->where('is_active', true)->first();
        if (!isset($externalConnection) || is_null($externalConnection))
            return  ActionResponse::danger('ExternalConnection(Rayna) is not available or inactive.');


        foreach ($models as $orderItem) {
            RaynaHelpers::ProcessOrderItem($externalConnection, $orderItem, $user);
        }

        return ActionResponse::message('Request processed. Please check ApiLogs for results.');
    }
    public static function ProcessOrderItem(ExternalConnection $externalConnection, OrderItem $item, $user)
    {
        try {

            $extProduct = $item->product->externalProducts->where('external_connection_id', $externalConnection->id)->where('is_active', true)->first();
            if (is_null($extProduct)) {
                $comm1 = new Communication();
                $comm1->action = "Rayna API booking request";
                $comm1->description = "Error: missing 'external_product'";
                $item->communications()->save($comm1);
                return;
            }

            $additionalData = json_decode($extProduct->additional_data, true);
            if (is_null($additionalData)) {
                $comm1 = new Communication();
                $comm1->action = "Rayna API booking request";
                $comm1->description = "Error: missing 'additional_data'";

                $item->communications()->save($comm1);
            }

            $apiLog = new ApiLog();
            $apiLog->loggable()->associate($item);
            $apiLog->user = $user->name;
            $apiLog->method = "test";
            $apiLog->path = "google.com";
            $apiLog->request_body = "request_body";
            $apiLog->response_body = "response_body";
            $apiLog->status_code = 404;
            $apiLog->ip_Address = "127.0.0.1";

            // set other attributes
            $apiLog->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
