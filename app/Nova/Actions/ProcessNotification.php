<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

use function PHPUnit\Framework\isNull;

class ProcessNotification extends Action
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

        $totalNotification = count($models);
        $processedNotifications = 0;
        foreach ($models as $notification) {
            if ($notification->source == 'bokun') {
                // process bokun notification
            }

            if ($notification->source == 'ecwid') {
                // process ecwid notification
            }

            $processedNotifications++;
        }

        return Action::message('Notification(s) processed: ' . $processedNotifications . ' / ' . $totalNotification);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }

    private function processBokunNotification(\APP\Models\Notification $notification)
    {
        if ($notification->source != 'bokun')
            return;



        $payload = $notification->payload;
        $sellerId = $payload->seller->id;
        if (isNull($sellerId) || $sellerId == 0) {

            $notification->status = 'processed_error';
            $notification->result = 'No seller Id found on the payload';
            $notification->save();
            return;
        }

        // check if external_connection for bokun
        $external_connection = \APP\Models\ExternalConnection::where('external_id', $sellerId)->first();

        if ($external_connection == null) {
            $notification->status = 'processed_error';
            $notification->result = 'No external_connection found for seller ' . $sellerId;
            $notification->save();
            return;
        }

        $order = \APP\Models\Order::where('ShopOrderNumber', $payload->confirmationCode);
        if ($order == null)
            $order = new \App\Models\Order();
    }

    private function processEcwidNotification(\APP\Models\Notification $notification)
    {
    }
}
