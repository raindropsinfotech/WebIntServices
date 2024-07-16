<?php

namespace  App\Contracts;

use App\Models\OrderItem;

interface BookingServiceInterface
{

    // to check the connection
    public function checkConnection($parameters);

    // This function will be used to check the configuration and setup of the product.
    public function getExternalProduct(OrderItem $orderItem);

    /* This function will be used to get the price info about the product. */
    public function getPrice($parameters);

    /* This function will be used to check if product has time selection*/
    public function hasTimeSelection($parameters);

    // This function will be called only when product has time selection configured.
    public function getTimeSlots($parameters);

    // This function will check the availibility of the service based on the number of guests, date and time(if required)
    public function checkAvailability($parameters);

    // This function will book the service, and send tickets to the customer.
    public function book($parameters);

    // This function will be used to cancel a booking.
    public function cancel($parameters);
}
