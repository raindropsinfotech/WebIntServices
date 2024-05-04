<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NotificationProcessed;
use App\Nova\Metrics\OrderPaid;
use App\Nova\Metrics\OrderProcessed;
use App\Nova\Metrics\PendingOrderItems;
use App\Nova\Metrics\TotalCustomers;
use App\Nova\Metrics\TotalOrders;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            NotificationProcessed::make(),
            PendingOrderItems::make(),
            OrderProcessed::make(),
            OrderPaid::make(),
            TotalOrders::make(),
            TotalCustomers::make(),
        ];
    }
}
