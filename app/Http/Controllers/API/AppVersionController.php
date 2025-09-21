<?php

namespace App\Http\Controllers\API;

use Akaunting\Setting\Facade as Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Update mobile app version
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVersion(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/', // Validate semantic versioning format
            'android_link' => 'nullable|url',
            'ios_link' => 'nullable|url',
        ]);

        // Update settings
        Setting::set('mobile_app_version', $request->version);

        // Update store links if provided
        if ($request->has('android_link')) {
            Setting::set('mobile_app_link_android', $request->android_link);
        }

        if ($request->has('ios_link')) {
            Setting::set('mobile_app_link_ios', $request->ios_link);
        }

        // Save settings
        Setting::save();

        // Return updated settings
        $data = [
            'version' => Setting::get('mobile_app_version'),
            'links' => [
                'android' => Setting::get('mobile_app_link_android'),
                'ios' => Setting::get('mobile_app_link_ios'),
            ],
        ];

        return apiSuccess($data, 'App version updated successfully');
    }

    /**
     * Get current app version settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersion()
    {
        $data = [
            'version' => Setting::get('mobile_app_version'),
            'links' => [
                'android' => Setting::get('mobile_app_link_android'),
                'ios' => Setting::get('mobile_app_link_ios'),
            ],
        ];

        return apiSuccess($data);
    }
}
