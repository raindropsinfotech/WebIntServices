<?php

namespace App\Services;


use App\Contracts\BookingServiceInterface;
use App\Models\ExternalConnection;
use App\Models\OrderItem;

class RaynaBookingService implements BookingServiceInterface
{
    protected $externalConnection;
    public function __construct()
    {
        $this->externalConnection = ExternalConnection::where('connection_type', 'rayna')->where('is_active', true)->first();
    }
    public function checkConnection($parameters)
    {
        return isset($this->externalConnection) && !is_null($this->externalConnection) && $this->externalConnection->is_active;
    }

    public function getExternalProduct(OrderItem $orderItem)
    {
        return $orderItem->product->externalProducts->where('external_connection_id', $this->externalConnection->id)->where('is_active', true)->first();
    }

    public function getPrice($parameters)
    {
    }

    public function hasTimeSelection($parameters)
    {
    }

    public function getTimeSlots($parameters)
    {
    }

    public function checkAvailability($parameters)
    {
    }

    public function book($parameters)
    {
    }

    public function cancel($parameters)
    {
    }
}
