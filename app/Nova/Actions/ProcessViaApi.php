<?php

namespace App\Nova\Actions;


// use Laravel\Nova\Nova;
use App\Jobs\ProcessTourTimeSlot;
use App\Helpers\FTPHelpers;
use App\Helpers\RaynaHelpers;
use App\Helpers\DispatchCustomAction;
use App\Models\ExternalConnection;
use App\Models\ExternalConnectionMapping;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Actions\TourTimeSlot;
use Illuminate\Support\Facades\Bus;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Actions\DispatchAction;
use App\Services\BookingServiceResolver;

use function PHPUnit\Framework\isNull;

class ProcessViaApi extends Action
{
    use InteractsWithQueue, Queueable;
    // public $name = 'process-via-api';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        // checking for the authentication
        $user = Auth::user();
        if (is_null($user))
            return ActionResponse::danger('User is not authorized');

        // run this action only for 1 resource. @todo: in fuiture we have to use multi slect, may be for the automation. - VD, IB - 23.05.2024
        if ($models->count() > 1)
            return Action::danger('Please run this action on only one OrderItem resource.');

        // Selected api provider name

        $provider = $fields->provider;

        if (is_null($provider))
            return Action::danger('Please select a provider.');

        // resource or object which we use to process via API
        $orderItem = $models->first();
        if (!isset($orderItem))
            return Action::danger('OrderItem is null');


        $connectionType = null;
        switch ($provider) {
            default:
                return ActionResponse::message('Function not available.');
            case 'rayna':
                $connectionType = "rayna";
        }

        $url = '/booking/checkProduct?apiName=' . $connectionType . '&oid=' . $orderItem->Id;

        return Action::redirect($url);
        // we check for the extenal connection is available and active.
        $externalConnection = ExternalConnection::where('connection_type', $connectionType)->where('is_active', true)->first();

        if (!isset($externalConnection) || is_null($externalConnection))
            return  ActionResponse::danger('ExternalConnection(' . $connectionType . ') is not available or inactive.');

        // we try to find the    external_product object, if not found or not active then returns an error message and also

        // log it to the communication or api logs.
        //Date: 24th may

        $extProduct = $orderItem->product->externalProducts->where('external_connection_id', $externalConnection->id)->where('is_active', true)->first();

        if (is_null($extProduct)) {
            return ActionResponse::danger("Error: missing 'external_product'");
        }

        // here we will store all external api product configuration on additional_data, for example: product id, tour id, city id, options, agreed price, etc....
        $additionalData = json_decode($extProduct->additional_data, true);
        if (is_null($additionalData))
            return ActionResponse::danger("Error: missing 'additional_data'");

        // VD -- :D
        // @todo: credentials to connect with API
        $ecmapping = ExternalConnectionMapping::where('external_connection_id', $externalConnection->id)->first(); // find with $external externalConnection
        $providerCredentials = $ecmapping->shopCredential;

        if (!isset($providerCredentials) || !$providerCredentials->Active)
            return  ActionResponse::danger('Credentials are not available or inactive.');
        //$providerCredentials = $ecmapping->shopCredential() // adgain check if credentials are active, if not return error or log error

        // @todo:

        if ($provider == 2) {
            $raynaProviders = array();
            $raynaTimeSlotAvailabilityArr = array();
            $raynaDatasArr = array();

            $raynaProviders = RaynaHelpers::ProcessOrderItem($providerCredentials, $externalConnection, $orderItem, $user);
            $raynaDatasArr['providerCredentials'] = $providerCredentials;
            $raynaDatasArr['externalConnection'] = $externalConnection;
            $raynaDatasArr['orderItems'] = $orderItem;
            // To check if rayna api results has empty or not
            if (!empty($raynaProviders)) {
                if ($raynaProviders['timeslots']['count'] > 0) {
                    foreach ($raynaProviders['timeslots']['result'] as $RTSAKey => $raynaTimeSlotAvail) {
                        $raynaTimeSlotAvailabilityArr[$RTSAKey] = $raynaTimeSlotAvail;
                        $raynaTimeSlotAvailabilityArr[$RTSAKey]['orderItemsId'] = $orderItem->Id;
                        $raynaTimeSlotAvailabilityArr[$RTSAKey]['orderId'] = $orderItem->OrderId;
                        $raynaTimeSlotAvailabilityArr[$RTSAKey]['productId'] = $orderItem->ProductId;
                        $raynaTimeSlotAvailabilityArr[$RTSAKey]['externalId'] = $extProduct->id;
                        $raynaTimeSlotAvailabilityArr[$RTSAKey]['externalProductId'] = $extProduct->external_product_id;
                        $raynaTimeSlotAvailabilityArr[$RTSAKey]['orderItems'] = $orderItem;
                    }

                    // \Log::info('ProcessViaApi action:', ['timeSlots' => $raynaTimeSlotAvailabilityArr]);

                    return Action::modal('tour-time-slot-modal', [
                        'endpoint' => route('ryna_store_update_ext_pro_atr_api'),
                        'message' => 'Please select a time slot for the tour.',
                        'timeSlots' => $raynaTimeSlotAvailabilityArr, // Example data, replace with actual data
                        'raynaDatas' => $raynaDatasArr,
                        'redirect_url' => 'http://192.168.1.21:8005/resources/order-items/' . $orderItem->Id

                    ]);
                } else {
                    return  ActionResponse::danger('Opps! There are no timeslote available for Service date.');
                }
            } else {
                return  ActionResponse::danger('Opps! Something went wrong please try again.');
            }
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Provider')->options(Product::$orderProcessingTypes),
        ];
    }
}
