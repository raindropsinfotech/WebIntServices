<?php

namespace App\Helpers;

use App\Models\ApiLog;
use App\Models\Communication;
use App\Models\ExternalConnection;
use App\Models\ExternalProduct;
use App\Models\OrderItem;
use App\Models\Credential;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\ActionResponse;
use App\Helpers\CurlHandlerHelpers;
use App\Nova\Actions\TourTimeSlot;

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

    //@todo:
    public static function ProcessOrderItem(Credential $raynaCredentials, ExternalConnection $externalConnection, OrderItem $item, $user)
    {
        try {

            $extProduct = $item->product->externalProducts->where('external_connection_id', $externalConnection->id)->where('is_active', true)->first();
            if (is_null($extProduct)) {
                $comm1 = new Communication();
                $comm1->action = "Rayna API booking request";
                $comm1->description = "Error: missing 'external_product'";
                $item->communications()->save($comm1);
                return false;
            }

            $additionalData = json_decode($extProduct->additional_data, true);
            if (is_null($additionalData)) {
                $comm1 = new Communication();
                $comm1->action = "Rayna API booking request";
                $comm1->description = "Error: missing 'additional_data'";
                $item->communications()->save($comm1);
                return false;
            }


            $extProductId = $extProduct->external_product_id;
            $tourOptions = array();
            $toureOptionDate = date('m-d-Y', strtotime($item->ServiceDateTime));
            $toureSlotDate = date('Y-m-d', strtotime($item->ServiceDateTime));
            // @todo: Get Touroptions
            $getTourOptions = CurlHandlerHelpers::getTourOptions($raynaCredentials, $externalConnection, 2, null, null, $extProductId, 300, $toureOptionDate, $item->Adults, $item->Children, 0);
            // Api Log
            $TourOptionsRequest = [
                "tourId" => $extProductId,
                "contractId" => 300,
                "travelDate" => $toureOptionDate,
                "noOfAdult" => $item->Adults,
                "noOfChild" => $item->Children,
                "noOfInfant" => 0
            ];

            // Insert into log table
            $apiLog = new ApiLog();
            $apiLog->loggable()->associate($item);
            $apiLog->user = $user->name;
            $apiLog->method = "Rayna API booking Tour Options";
            $apiLog->path = $raynaCredentials->BaseUrl."api/Tour/touroption";
            $apiLog->request_body = json_encode($TourOptionsRequest);
            $apiLog->response_body = json_encode($getTourOptions);
            $apiLog->status_code = 200;
            $apiLog->ip_Address = $_SERVER['REMOTE_ADDR'];
            $apiLog->save();

            if( !empty($getTourOptions) ) {
                foreach( $getTourOptions['result'] as $k => $tourOption ) {
                    $tourOptions['timeslots'] = CurlHandlerHelpers::getTourTimeSlots($raynaCredentials, $externalConnection, 2, null, null, $extProductId, 300, $toureSlotDate, $item->Adults, $item->Children, $tourOption['tourOptionId'], $tourOption['transferId']);


                    // $TourTimeSlotRequest = [
                    //     "tourId" => $extProductId,
                    //     "tourOptionId" => $tourOption['tourOptionId'],
                    //     "transferId" => $tourOption['transferId'],
                    //     "travelDate" => $toureSlotDate,
                    //     "adult" => $item->Adults,
                    //     "child" => $item->Children,
                    //     "contractId" => 300
                    // ];

                    // $tourTimeSlotsapiLog = new ApiLog();
                    // $tourTimeSlotsapiLog->loggable()->associate($item);
                    // $tourTimeSlotsapiLog->user = $user->name;
                    // $tourTimeSlotsapiLog->method = "Rayna API booking Tour Timeslots";
                    // $tourTimeSlotsapiLog->path = $raynaCredentials->BaseUrl."api/Tour/timeslot";
                    // $tourTimeSlotsapiLog->request_body = json_encode($TourTimeSlotRequest);
                    // $tourTimeSlotsapiLog->response_body = json_encode($tourOptions['timeslots']);
                    // $tourTimeSlotsapiLog->status_code = 200;
                    // $tourTimeSlotsapiLog->ip_Address = $_SERVER['REMOTE_ADDR'];
                    // $tourTimeSlotsapiLog->save();

                    // @todo: Loop for TourTimeSlot and Get Timeslot availability

                    if( !empty( $tourOptions['timeslots'] ) ) {
                        if( $tourOptions['timeslots']['count'] > 0 ) {
                            foreach ($tourOptions['timeslots']['result'] as $optKey => $timeslot) {
                                $tourOptions['timeslots']['result'][$optKey]['tourOptions'] = $tourOption;
                                if( $timeslot['available'] > 0 || !empty( $timeslot['available'] ) || $timeslot['available'] != 0) {
                                    $availabilityApiRes = CurlHandlerHelpers::getTourTimeSlotsAvailability( $raynaCredentials, $externalConnection, 2, $tourOption['tourId'], $timeslot['tourOptionId'], $tourOption['transferId'], $toureSlotDate, $item->Adults, $item->Children, 0, 300 );
                                    $tourOptions['timeslots']['result'][$optKey]['availability'] = $availabilityApiRes['result'];
                                }
                            }
                        }
                    }
                }
                return $tourOptions;
            } else {
                return ActionResponse::message('Oops! Try again later.');
            }
            // @todo: connect with api and do the booking process - VD
            // $apiLog = new ApiLog();
            // $apiLog->loggable()->associate($item);
            // $apiLog->user = $user->name;
            // $apiLog->method = "test";
            // $apiLog->path = "google.com";
            // $apiLog->request_body = "request_body";
            // $apiLog->response_body = "response_body";
            // $apiLog->status_code = 404;
            // $apiLog->ip_Address = "127.0.0.1";

            // set other attributes
            // $apiLog->ip_Address = $_SERVER['REMOTE_ADDR'];
            // $apiLog->save();
        } catch (\Throwable $th) {
            throw $th;
        }
        return ActionResponse::message('Good to go Ok!');
    }
}
