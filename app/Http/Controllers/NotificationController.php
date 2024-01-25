<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'source' => 'required|string'
            ]);

            $notification = new Notification();
            $notification->source = $request->input('source');
            $notification->payload = $request->json()->all(); // Store the entire JSON request body
            $notification->save();

            return response()->json(['message' => 'Data saved successfully'], 201);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
