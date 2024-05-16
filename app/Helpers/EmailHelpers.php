<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use App\Mail\ManualEmail;



class EmailHelpers
{
    /**
     *This method will try to send the email with mailsettings provided and returns an array of [success, message]
     */
    public static function sendEmail($mailSettings, $subject, $content, $email, $from, $files = null): array
    {
        try {

            config([
                'mail.driver' => 'smtp',
                'mail.host' => $mailSettings->Host,
                'mail.port' => $mailSettings->Port,
                'mail.username' => $mailSettings->Username,
                'mail.password' => $mailSettings->Password,
                'mail.'
            ]);

            // \Log::info($content);
            \Log::info('configuration set.');

            Mail::to($email)->send(new ManualEmail($content, $subject, $from, $mailSettings, $files));

            // If no exception is thrown, the email was sent successfully
            echo "Email sent successfully.";

            return [true, "Email sent successfully."];
        } catch (\Exception $e) {
            // If an exception is caught, there was an error sending the email

            return [false, "Error sending email: " . $e->getMessage()];
        }
    }
}
