<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NotificationProcessed;
use App\Nova\Metrics\OrderProcessed;
use Laravel\Nova\Cards\Help;
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
            OrderProcessed::make(),
        ];
    }
}
