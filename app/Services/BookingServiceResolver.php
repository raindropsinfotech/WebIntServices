<?php

namespace App\Services;

use App\Contracts\BookingServiceInterface;
use InvalidArgumentException;


class BookingServiceResolver
{
    protected $services;
    public function __construct()
    {
        $this->services = [
            'rayna' => new RaynaBookingService(),
            // here future API will be added with corresponsing implementations.
        ];
    }

    public function resolve($apiName): BookingServiceInterface
    {
        if (!isset($this->services[$apiName])) {
            throw new InvalidArgumentException("No service found for API : {$apiName}");
        }

        return $this->services[$apiName];
    }
}
