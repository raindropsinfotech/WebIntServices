<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Models\EcwidHelper;
use App\Nova\Actions\UpdateOrderStatusOnShop;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderCreatedEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        \Log::info("OrderCreatedEventListener invoked.");
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        \Log::info("OrderCreatedEventListener raised for order '" . $event?->order?->ShopOrderNumber . "'.");

        // whenever this event raises
        // 1. Change Shop Order status to Processing
        // 2. Check Order Payment status and update accordingly.

        EcwidHelper::setOrderStatus($event->order, 'PROCESSING');
    }
}
