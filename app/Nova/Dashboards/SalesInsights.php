<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\TotalCustomers;
use App\Nova\Metrics\TotalOrders;
use Laravel\Nova\Dashboard;

class SalesInsights extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            TotalOrders::make(),
            TotalCustomers::make(),
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'sales-insights';
    }
}
