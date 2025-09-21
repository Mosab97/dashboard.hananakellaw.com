<?php

namespace App\Http\Middleware;

use Akaunting\Setting\Facade as Setting;
use App\Exceptions\CustomBusinessException;
use Closure;
use Illuminate\Http\Request;

class CheckAppVersion
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get app version from header
        $appVersion = $request->header('X-App-Version', '');

        // Require version to be specified
        if (empty($appVersion)) {
            throw new CustomBusinessException(
                'App version header is required',
                400,
                [
                    'required_header' => 'X-App-Version',
                ]
            );
        }

        // Get the required version from settings
        $requiredVersion = Setting::get('mobile_app_version');
        // Check if version is supported
        if (version_compare($appVersion, $requiredVersion, '<')) {
            throw new CustomBusinessException(
                'App update required',
                426,
                [
                    'current_version' => $appVersion,
                    'required_version' => $requiredVersion,
                    'update_links' => [
                        'android' => Setting::get('mobile_app_link_android'),
                        'ios' => Setting::get('mobile_app_link_ios'),
                    ],
                ]
            );
        }

        return $next($request);
    }
}
