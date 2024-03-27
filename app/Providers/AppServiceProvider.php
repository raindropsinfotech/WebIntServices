<?php

namespace App\Providers;

use App\Events\NotificationStored;
use App\Listeners\ProcessNotificationListener;
use App\Models\User;
use App\Observers\UserObserver;
use Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Observable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(NotificationStored::class, ProcessNotificationListener::class,);
    }
}
