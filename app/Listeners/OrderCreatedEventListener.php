<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
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
        //
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
        $updater = new UpdateOrderStatusOnShop();
        $updater->setOrderStatus($event->order, 'PROCESSING');
    }
}
