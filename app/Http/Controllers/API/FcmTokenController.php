<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Test sending notifications to our generated token
     */
    public function testNotification(Request $request)
    {
        $token = $request->input('token');

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'No token provided',
            ], 400);
        }

        try {
            // Create a temporary member with the token
            $tempMember = new \App\Models\Member;
            $tempMember->id = 0;
            $tempMember->fcm_token = $token;

            // Send notification
            $tempMember->notify(new \App\Notifications\TestFcmNotification);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the real token generator page
     */
    public function realTokenGenerator()
    {
        $firebaseConfig = [
            'apiKey' => config('services.fcm.api_key'),
            'authDomain' => config('services.fcm.auth_domain'),
            'projectId' => config('services.fcm.project_id'),
            'storageBucket' => config('services.fcm.storage_bucket'),
            'messagingSenderId' => config('services.fcm.messaging_sender_id'),
            'appId' => config('services.fcm.app_id'),
            'measurementId' => config('services.fcm.measurement_id'),
        ];

        $vapidKey = config('services.fcm.vapid_key');

        return view('fcm_token_generator.real-token-generator', [
            'firebaseConfig' => $firebaseConfig,
            'vapidKey' => $vapidKey,
        ]);
    }
}
