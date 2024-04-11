<?php

namespace App\Nova\Metrics;

use App\Models\Order;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class OrderProcessed extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Order::class, 'status')
            ->colors([
                0 => 'orange',
                1 => 'orange',
                2 => 'green',
                3 => 'black'
            ])
            ->label(fn ($value) => match ($value) {
                0 => 'New',
                1 => 'PartiallyCompleted',
                2 => 'Completed',
                3 => 'Cancelled'
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
        return 'order-processed';
    }
}
