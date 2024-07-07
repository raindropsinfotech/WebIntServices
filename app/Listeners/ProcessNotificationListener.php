<?php

namespace App\Listeners;

use App\Events\NotificationStored;
use App\Nova\Actions\ProcessNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationStored $event): void
    {
        \Log::info('ProcessNotificationListener.handle()');
        $notification = $event->notification;

        $notificationProcessor = new ProcessNotification();
        $notificationProcessor->processNotification($notification);
        // if ($notification->source == "bokun") {
        //     $notificationProcessor->processBokunNotification($notification);
        // }
        // if ($notification->source == "ecwid") {
        //     $notificationProcessor->processEcwidNotification($notification);
        // }
    }
}
