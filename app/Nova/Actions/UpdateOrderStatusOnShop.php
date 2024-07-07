<?php

namespace App\Nova\Actions;

use App\Helpers\EcwidHelpers as HelpersEcwidHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
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

        $status = $fields->status;
        if (!isset($status))
            return Action::danger("Select one status.");

        $succs = 0;
        foreach ($models as $order) {
            // if ($order->Status != 2)
            //     continue;

            HelpersEcwidHelper::setOrderStatus($order, $status);
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
        return [
            Select::make('status')->options([
                'AWAITING_PROCESSING' => 'AWAITING_PROCESSING',
                'PROCESSING' => 'PROCESSING',
                'CUSTOM_FULFILLMENT_STATUS_1' => 'CONFIRMED',
                'DELIVERED' => 'DELIVERED',
                'OUT_FOR_DELIVERY' => 'OUT_FOR_DELIVERY',
                'WILL_NOT_DELIVER' => 'WILL_NOT_DELIVER',
                'RETURNED' => 'RETURNED'
            ])
        ];
    }
}
