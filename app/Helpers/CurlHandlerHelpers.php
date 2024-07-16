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
use Laravel\Nova\Actions\Action;
use GuzzleHttp\Client;

class CurlHandlerHelpers
{
    /*
    * Description : Rayana Api get TOUR OPTIONS from api using provider
    * Date : 06-07-2024
    */
    public static function getTourOptions(Credential $raynaCredentials, ExternalConnection $externalConnection, $provider, $countryId = null, $cityId = null, $tourId = null, $contractId = null, $travelDate = null, $noOfAdult = 0, $noOfChild = 0, $noOfInfant = 0)
    {
        $request = [

        ];
        // return self::SetupAPI('Tour/touroption', 'POST', $request);
        try {
            $token = 'Bearer ' .$raynaCredentials->Password;
            $client = new \GuzzleHttp\Client();
            $url = $raynaCredentials->BaseUrl."api/Tour/touroption";
            $request = [
                "tourId" => $tourId,
                "contractId" => $contractId,
                "travelDate" => $travelDate,
                "noOfAdult" => $noOfAdult,
                "noOfChild" => $noOfChild,
                "noOfInfant" => $noOfInfant
            ];

            $response = $client->request('POST', $url, [
                'body' => json_encode($request),
                'headers' => [
                    // 'Authorization' => $token,
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkNWU4YWZhMC1mNGJhLTQ2NWUtYTAzOS1mZGJiYzMxZWZlZGUiLCJVc2VySWQiOiIzNzU0NSIsIlVzZXJUeXBlIjoiQWdlbnQiLCJQYXJlbnRJRCI6IjAiLCJFbWFpbElEIjoidHJhdmVsZ2F0ZXhAcmF5bmF0b3Vycy5jb20iLCJpc3MiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20iLCJhdWQiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20ifQ.i6GaRt-RVSlJXKPz7ZVx-axAPLW_hkl7usI_Dw8vP5w',
                ],
            ]);

            if ($response->getStatusCode() == 200)
                return json_decode($response->getBody(), true);
            else
                return ActionResponse::danger('Please try again later.');

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getTourTimeSlots(Credential $raynaCredentials, ExternalConnection $externalConnection, $provider, $countryId = null, $cityId = null, $tourId = null, $contractId = null, $travelDate = null, $noOfAdult = 0, $noOfChild = 0, $tourOptionId = null, $transferId = null )
    {
        try {
            $token = $raynaCredentials->Password;
            $client = new \GuzzleHttp\Client();
            $url = "http://sandbox.raynatours.com/api/Tour/timeslot";
            $request = [
                "tourId" => $tourId,
                "tourOptionId" => $tourOptionId,
                "transferId" => $transferId,
                "travelDate" => $travelDate,
                "adult" => $noOfAdult,
                "child" => $noOfChild,
                "contractId" => $contractId
            ];
            $response = $client->request('POST', $url, [
                'body' => json_encode($request),
                'headers' => [
                    // 'Authorization' => 'Bearer ' . $token,
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkNWU4YWZhMC1mNGJhLTQ2NWUtYTAzOS1mZGJiYzMxZWZlZGUiLCJVc2VySWQiOiIzNzU0NSIsIlVzZXJUeXBlIjoiQWdlbnQiLCJQYXJlbnRJRCI6IjAiLCJFbWFpbElEIjoidHJhdmVsZ2F0ZXhAcmF5bmF0b3Vycy5jb20iLCJpc3MiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20iLCJhdWQiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20ifQ.i6GaRt-RVSlJXKPz7ZVx-axAPLW_hkl7usI_Dw8vP5w',
                ],
            ]);

            if ($response->getStatusCode() == 200)
                return json_decode($response->getBody(), true);
            else
                return ActionResponse::danger('Please try again later.');

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getTourTimeSlotsAvailability(Credential $raynaCredentials, ExternalConnection $externalConnection, $provider, $tourId = null, $tourOptionId = null, $transferId = null, $travelDate = null, $noOfAdult = 0, $noOfChild = 0, $noOfInfant = 0, $contractId = null )
    {
        try {
            $token = $raynaCredentials->Password;
            $client = new \GuzzleHttp\Client();
            $url = "http://sandbox.raynatours.com/api/Tour/availability";
            $request = [
                "tourId" => $tourId,
                "tourOptionId" => $tourOptionId,
                "transferId" => $transferId,
                "travelDate" => $travelDate,
                "adult" => $noOfAdult,
                "child" => $noOfChild,
                "contractId" => $contractId
            ];
            $response = $client->request('POST', $url, [
                'body' => json_encode($request),
                'headers' => [
                    // 'Authorization' => 'Bearer ' . $token,
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkNWU4YWZhMC1mNGJhLTQ2NWUtYTAzOS1mZGJiYzMxZWZlZGUiLCJVc2VySWQiOiIzNzU0NSIsIlVzZXJUeXBlIjoiQWdlbnQiLCJQYXJlbnRJRCI6IjAiLCJFbWFpbElEIjoidHJhdmVsZ2F0ZXhAcmF5bmF0b3Vycy5jb20iLCJpc3MiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20iLCJhdWQiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20ifQ.i6GaRt-RVSlJXKPz7ZVx-axAPLW_hkl7usI_Dw8vP5w',
                ],
            ]);

            if ($response->getStatusCode() == 200)
                return json_decode($response->getBody(), true);
            else
                return ActionResponse::danger('Please try again later.');

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function raynaBooking( $raynaCredentials, $externalConnection, $request_body = null) {
        try {
            $token = $raynaCredentials['Password'];
            $client = new \GuzzleHttp\Client();
            $url = "http://sandbox.raynatours.com/api/Booking/bookings";
            $response = $client->request('POST', $url, [
                'body' => json_encode($request_body),
                'headers' => [
                    // 'Authorization' => 'Bearer ' . $token,
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkNWU4YWZhMC1mNGJhLTQ2NWUtYTAzOS1mZGJiYzMxZWZlZGUiLCJVc2VySWQiOiIzNzU0NSIsIlVzZXJUeXBlIjoiQWdlbnQiLCJQYXJlbnRJRCI6IjAiLCJFbWFpbElEIjoidHJhdmVsZ2F0ZXhAcmF5bmF0b3Vycy5jb20iLCJpc3MiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20iLCJhdWQiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20ifQ.i6GaRt-RVSlJXKPz7ZVx-axAPLW_hkl7usI_Dw8vP5w',
                ],
            ]);
            dd(json_decode($response->getBody(), true));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function SetupAPI( Credential $raynaCredentials, ExternalConnection $externalConnection, $provider, $apiroutename, $apimethod, $request_body = null )
    {
        // Setup common var
        $client = new \GuzzleHttp\Client();
        $BaseUrl = $raynaCredentials->BaseUrl.'api/Tour';
        // $token = 'Bearer '.$raynaCredentials->Password;
        $token = 'Bearer eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiJkNWU4YWZhMC1mNGJhLTQ2NWUtYTAzOS1mZGJiYzMxZWZlZGUiLCJVc2VySWQiOiIzNzU0NSIsIlVzZXJUeXBlIjoiQWdlbnQiLCJQYXJlbnRJRCI6IjAiLCJFbWFpbElEIjoidHJhdmVsZ2F0ZXhAcmF5bmF0b3Vycy5jb20iLCJpc3MiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20iLCJhdWQiOiJodHRwOi8vcmF5bmFhcGkucmF5bmF0b3Vycy5jb20ifQ.i6GaRt-RVSlJXKPz7ZVx-axAPLW_hkl7usI_Dw8vP5w';

        // To check request body is null or empty
        if( is_null($request_body) || empty($request_body) )
            return [
                'statuscode' => 200,
                'error' => 1,
                'errormessage' => 'Oops! please check your request body.'
            ];

        // Api call by provider
        switch ($provider) {
            default:
                return ActionResponse::message('Function not available.');
                break;
            case 2:
                switch ($apiroutename) {
                    case 'touroption':
                        try {
                            $response = $client->request('POST', $BaseUrl.'/'.$apiroutename, [
                                'body' => json_encode($request_body),
                                'headers' => [
                                    'Authorization' => $token,
                                    'accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                ],
                            ]);
                            if ($response->getStatusCode() == 200) {
                                return json_decode($response->getBody(), true);
                            } else {
                                return json_decode([
                                    'statuscode' => 200,
                                    'error' => 1,
                                    'errormessage' => 'Oops! there are no touroptions for selected tour.'
                                ]);
                            }
                        } catch ( \Throwable $th ) {
                            return json_decode([
                                'statuscode' => 200,
                                'error' => 1,
                                'errormessage' => $th
                            ]);
                            // throw $th;
                        }
                        break;

                    case 'timeslot':
                        try {
                            dd($request_body);
                            $response = $client->request('POST', $BaseUrl.'/'.$apiroutename, [
                                'body' => json_encode($request_body),
                                'headers' => [
                                    'Authorization' => $token,
                                    'accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                ],
                            ]);
                            if ($response->getStatusCode() == 200) {
                                echo "<pre>"; dd($response->getBody()); echo "</pre>";
                                return json_decode($response->getBody(), true);
                            } else {
                                return ActionResponse::danger('Please try again later.');
                            }
                        } catch ( \Throwable $th ) {
                            throw $th;
                        }
                        break;
                    default:
                        // code...
                        break;
                }
                break;
        }
    }
}