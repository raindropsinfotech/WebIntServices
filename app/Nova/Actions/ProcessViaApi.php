<?php

namespace App\Nova\Actions;

use App\Helpers\FTPHelpers;
use App\Helpers\RaynaHelpers;
use App\Models\ExternalConnection;
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

use function PHPUnit\Framework\isNull;

class ProcessViaApi extends Action
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
        $user = Auth::user();
        if (is_null($user))
            return ActionResponse::danger('User is not authorized');

        if ($models->count() > 1)
            return Action::danger('Please run this action on only one OrderItem resource.');


        $provider = $fields->provider;

        if (is_null($provider))
            return Action::danger('Please select a provider.');


        $orderItem = $models->first();
        if (!isset($orderItem))
            return Action::danger('OrderItem is null');


        $connectionType = null;
        switch ($provider) {
            default:
                return ActionResponse::message('Function not available.');
            case 2:
                $connectionType = "rayna";
        }

        $externalConnection = ExternalConnection::where('connection_type', $connectionType)->where('is_active', true)->first();
        if (!isset($externalConnection) || is_null($externalConnection))
            return  ActionResponse::danger('ExternalConnection(' . $connectionType . ') is not available or inactive.');

        $extProduct = $$orderItem->product->externalProducts->where('external_connection_id', $externalConnection->id)->where('is_active', true)->first();
        if (is_null($extProduct)) {
            return ActionResponse::danger("Error: missing 'external_product'");
        }

        $additionalData = json_decode($extProduct->additional_data, true);
        if (is_null($additionalData))
            return ActionResponse::danger("Error: missing 'additional_data'");
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
            Select::make('provider')->options(Product::$orderProcessingTypes),
        ];
    }
}
