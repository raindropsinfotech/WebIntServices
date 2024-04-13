<?php

namespace App\Helpers;

class StripeHelpers
{
    public static function checkPayment(\App\Models\Order $order): bool
    {
        // check payment reference
        // check extenal connection
        // check payment provider
        // check payment

        if (is_null($order->PaymentReference))
            return false;

        if ($order->external_connection_id == 0)
            return false;

        $ecm = \App\Models\ExternalConnectionMapping::where('external_connection_id', $order->external_connection_id)->first();
        if (is_null($ecm))
            return false;

        \Log::alert('checking payment provider credentials');

        $paymentCredentials = \App\Models\Credential::find($ecm->payment_provider_credential_id);
        if (is_null($paymentCredentials) || is_null($paymentCredentials->Password))
            return false;

        \Log::alert('initializing stripe');
        $stripe = new \Stripe\StripeClient($paymentCredentials->Password);



        if (str_starts_with($order->PaymentReference, 'ch_')) {
            \Log::alert('checking stripe charge...');
            return StripeHelpers::checkPaymentByChargeId($stripe, $order->PaymentReference, $order);
        }

        // if paymentIntent then user this method
        \Log::alert('checking payment intent...');
        if (str_starts_with($order->PaymentReference, 'pi_')) {
            $pi = $stripe->paymentIntents->retrieve($order->PaymentReference);

            \Log::info($pi);
            if (is_null($pi))
                return false;


            \Log::info('Order total = ' . $order->OrderTotal);

            if ($order->OrderTotal * 100 == $pi->amount) {
                \Log::info('Amount confirmed');
            }

            if ($pi->status == "succeeded") {
                \Log::info('status confirmed');
            }

            if (!is_null($pi->latest_charge)) {
                \Log::info('charge info available.' .  $pi->latest_charge);
            }

            if (($order->OrderTotal * 100 == $pi->amount)
                && $pi->status == "succeeded"
                && !is_null($pi->latest_charge)
            ) {
                // search charge by latest_charge
                return StripeHelpers::checkPaymentByChargeId($stripe, $pi->latest_charge, $order);
            }
        }

        return false;
    }

    public static function checkPaymentByChargeId($stripe, $paymentReference, $order): bool
    {
        \Log::alert('checking stripe charge ' . $paymentReference);
        $ch = $stripe->charges->retrieve($paymentReference);
        if (is_null($ch))
            return false;

        \Log::info($ch);

        \Log::info('OrderTotal = ' . $order->OrderTotal * 100);
        if ($order->OrderTotal * 100 == $ch->amount_captured) {
            \Log::info('Amount confirmed');
        }

        if ($ch->status === "succeeded") {
            \Log::info('status confirmed');
        }

        if (!is_null($ch->payment_method_details->card->three_d_secure)) {
            \Log::info('three_d_secure segment available.');
        }

        if (!is_null($ch->payment_method_details->card->three_d_secure)) {
            \Log::info('result  = ' . $ch->payment_method_details->card->three_d_secure->result);
        }
        if (!(($order->OrderTotal * 100 == $ch->amount_captured)
            && $ch->status === "succeeded"
            && !is_null($ch->payment_method_details)
            && !is_null($ch->payment_method_details->card)
        )) {

            return false;
        }

        // if apple_pay payment
        if (!is_null($ch->payment_method_details->card->wallet) &&  $ch->payment_method_details->card->wallet->type == "apple_pay") {
            $order->PaymentStatus = 2;
            $order->save();

            return true;
        }

        // if 3d_secure_card payment
        if (
            !is_null($ch->payment_method_details->card->three_d_secure)
            &&  $ch->payment_method_details->card->three_d_secure->result == "authenticated"
        ) {
            $order->PaymentStatus = 2;
            $order->save();

            return true;
        }

        return false;
    }
}
