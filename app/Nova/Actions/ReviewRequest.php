<?php

namespace App\Nova\Actions;

use App\Helpers\EmailHelpers;
use App\Models\Communication;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ReviewRequest extends Action
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

        $order = $models->first();

        if (!isset($order))
            return Action::danger('Order is null');

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

        $template = \App\Models\EmailTemplate::where('EmailTemplateType', 41)->where('external_connection_id', $ecid)->where('IsActive', 1)->first();
        if (is_null($template))
            return Action::danger('ReviewRequest template for external_connection(' . $ecid . ') is either null or inactive or empty. Please check and try again.');


        // $selectedFiles = $fields->files;

        $email = $order->CustomerEmail;
        if (is_null($email))
            return Action::danger("Email not set on order(" . $order->ShopOrderNumber . ")");

        // try {

        \Log::info('sending email...');
        // Attempt to send the email
        $subject = $template->Subject;
        $subject = str_replace('{Order_Number}', $order->ShopOrderNumber, $subject);

        // replace placeholders in template
        $content = $template->Content;
        $content = str_replace('{Order_Number}', $order->ShopOrderNumber, $content);
        $content = str_replace('{Customer_First_Name}', $order->CustomerName, $content);

        $emailSendResult = EmailHelpers::sendEmail($mailSettings, $subject, $content, $email, $order->externalConnection->name, $fields->files);

        if ($emailSendResult[0] == true) {
            echo "Email sent successfully.";



            $comm = new Communication();
            $comm->action = "Review request sent";
            $comm->description = "Review email send by " . Auth::user()?->name;

            $order->communications()->save($comm);


            return Action::message("Review email sent to " . $email . ' successfully.');
        } else {

            \Log::error($emailSendResult[1]);
            return Action::danger($emailSendResult[1]);
        }



        return Action::danger("Function coming soon!");
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
}
