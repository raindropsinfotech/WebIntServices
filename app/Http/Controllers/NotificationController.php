<?php

namespace App\Http\Controllers;

use App\Events\NotificationStored;
use App\Models\Notification;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        try {

            $settings = \App\Models\Setting::where('Active', true)->first();
            if (is_null($settings) || $settings->EnableNotifications == false)
                return response()->json(['message' => 'Not allowed'], 405);

            // $env_key = env('API_KEY', 'KZDjVDcpouleeir2bXovWSh4tv5RFK3y');
            $request->validate([
                'source' => 'required|string',
                'key' => 'required|string'
            ]);

            $key = $request->input('key');

            // if ($key != $env_key)
            //     return response()->json(['message' => 'Unauthorized request'], 401);

            if ($key != $settings->NotificationKey)
                return response()->json(['message' => 'Unauthorized request'], 401);

            $data = json_encode($request->json()->all());

            // var_dump($data);
            $notification = new Notification();
            $notification->source = $request->input('source');
            $notification->payload = $data; // Store the entire JSON request body
            $notification->save();

            event(new NotificationStored($notification)); // to raise an event on notificationstored. The system will try to process the notification.

            if ($request->input('source') == 'ecwid')
                return response(null, 200);

            return response()->json(['message' => 'Data saved successfully'], 201);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
