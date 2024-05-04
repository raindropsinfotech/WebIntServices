<?php

namespace App\Nova\Metrics;

use App\Models\OrderItem;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class PendingOrderItems extends Partition
{

    public function name()
    {
        return "Today's Pending Order Items";
    }
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        // get all order items where service date is today
        // group it by processed
        // show count of not processed and processed

        $query = OrderItem::query()
            ->whereDate('ServiceDateTime', today());

        return $this->count($request, $query, 'IsProcessed')
            ->colors([
                0 => 'red',
                1 => 'green'
            ])
            ->label(fn ($value) => match ($value) {
                0 => 'Not Processed',
                1 => 'Processed',
            });
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'todays-pending-order-items';
    }
}
