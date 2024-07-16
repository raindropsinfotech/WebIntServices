<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Services\BookingServiceResolver;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingServiceResolver;

    public function __construct(BookingServiceResolver $bookingServiceResolver)
    {
        $this->bookingServiceResolver = $bookingServiceResolver;
    }

    protected function getService($apiName)
    {
        return $this->bookingServiceResolver->resolve($apiName);
    }

    public function checkProduct(Request $request)
    {
        $parameters = $request->all();
        $service = $this->getService($parameters['apiName']);
        $connection = $service->checkConnection($parameters);

        if ($connection != true)
            return response()->json($connection);

        $orderItem =   OrderItem::where('Id', $parameters['oid'])->first();
        $externalProduct =  $service->getExternalProduct($orderItem);
        return response()->json($externalProduct);
    }

    public function hasTimeSelection(Request $request)
    {
        $parameters = $request->all();
        $service = $this->getService($parameters['apiName']);
        $timeSelection = $service->hasTimeSelection($parameters);
        return response()->json($timeSelection);
    }

    public function selectTime(Request $request)
    {
        $parameters = $request->all();
        $service = $this->getService($parameters['apiName']);
        $timeSelection = $service->getTimeSlots($parameters);
        return response()->json($timeSelection);
    }

    public function checkAvailability(Request $request)
    {
        $parameters = $request->all();
        $service = $this->getService($parameters['apiName']);
        $availability = $service->checkAvailability($parameters);
        return response()->json($availability);
    }

    public function getPrice(Request $request)
    {
        $parameters = $request->all();
        $service = $this->getService($parameters['apiName']);
        $price = $service->getPrice($parameters);
        return response()->json($price);
    }

    public function book(Request $request)
    {
        $parameters = $request->all();
        $service = $this->getService($parameters['apiName']);
        $booking = $service->book($parameters);
        return response()->json($booking);
    }
}
