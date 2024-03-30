<?php

namespace App\Nova\Actions;

use App\Mail\TestEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProcessManually extends Action
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
        if ($models->count() > 1) {
            return Action::danger('Please run this action on only one OrderItem resource.');
        }

        // identify the external connection
        // proceed only if external connection is active
        // find external conneciton mapping
        // check if external connection mapping has MailSettings
        // proceed if MailSettings are active
        // Find active TicketAsAttachment template for given external connection
        // throw error if template is empty
        // if all set pass the orderItem, template and smtp setting along with attachment to send email

        if (is_null($fields->files))
            return Action::danger('Please select at least 1 file to proceed.');

        $orderItem = $models->first();

        if (!isset($orderItem))
            return Action::danger('OrderItem is null');

        \Log::info('orderItem set');

        // \Log::info($orderItem);

        // \Log::info($orderItem->order);

        $order = $orderItem->order;

        if (!isset($order) || is_null($order))
            return Action::danger('Order is null');

        \Log::info('Order set');
        //\Log::info($order);
        //\Log::info($order->externalConnection());
        $ecid = $order->external_connection_id;

        // \Log::info($ecid);

        if (is_null($ecid) || $ecid == 0)
            return Action::danger('ExternalConnectionId not set for the order(' . $order->ShopOrderNumber . ').');

        $ecm = \App\Models\ExternalConnectionMapping::where('external_connection_id', $ecid)->first();
        if (is_null($ecm))
            return Action::danger('No external connection mapping found for ecid(' . $ecid . ').');



        $mailSettings  = $ecm?->mailSetting;
        if (is_null($mailSettings))
            return Action::danger('MailSettings are null or inactive. Please check and try again.');

        $template = \App\Models\EmailTemplate::where('EmailTemplateType', 1)->where('external_connection_id', $ecid)->where('IsActive', 1)->first();

        // \Log::info($template);

        if (is_null($template))
            return Action::danger('TicketAsAttachment template for external_connection(' . $ecid . ') is either null or inactive or empty. Please check and try again.');


        // $selectedFiles = $fields->files;

        $email = $order->CustomerEmail;
        if (is_null($email))
            return Action::danger("Email not set on order(" . $order->ShopOrderNumber . ")");

        try {

            \Log::info('sending email...');
            // Attempt to send the email
            $subject = $template->Subject;
            str_replace('{Order_Number}', $order->ShopOrderNumber, $subject);
            str_replace('{Service}', $orderItem->product->FullName, $subject);

            // replace placeholders in template
            $content = $template->content;
            str_replace('{Order_Number}', $order->ShopOrderNumber, $content);
            str_replace('{Service}', $orderItem->product->FullName, $content);

            config([
                'mail.driver' => 'smtp',
                'mail.host' => $mailSettings->Host,
                'mail.port' => $mailSettings->Port,
                'mail.username' => $mailSettings->Username,
                'mail.password' => $mailSettings->Password,
                'mail.'
            ]);

            \Log::info('configuration set.');

            Mail::to($email)->send(new TestEmail($fields->files, $content, $subject, $mailSettings->FromEmail, $order->externalConnection->name));

            // If no exception is thrown, the email was sent successfully
            echo "Email sent successfully.";

            $orderItem->IsProcessed = true;
            $orderItem->save();

            return Action::message("Files sent to " . $orderItem->order->CustomerEmail . ' successfully.');
        } catch (\Exception $e) {
            // If an exception is caught, there was an error sending the email
            echo "Error sending email: " . $e->getMessage();
            \Log::error($e->getMessage());
            return Action::danger("Error sending email: " . $e->getMessage());
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            File::make('Files'),
        ];
    }
}
