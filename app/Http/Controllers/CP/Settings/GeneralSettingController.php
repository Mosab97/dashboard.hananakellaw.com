<?php

namespace App\Http\Controllers\CP\Settings;

use Akaunting\Setting\Facade as Setting;
use App\Http\Controllers\Controller;
use App\Services\Filters\SettingsFilterService;
use App\Traits\HijriDateTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralSettingController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    private $config;

    // public function __construct(SettingsFilterService $filterService)
    public function __construct()
    {
        $this->config = config('modules.settings.children.general');
        // $this->filterService = $filterService;
        Log::info('............... '.$this->config['controller'].' initialized ...........');
    }

    /**
     * Display and handle the general settings page
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = $this->getCommonData('index');

            return view($data['_view_path'].'index', $data);
        }

        // If POST request, update settings
        if ($request->isMethod('POST')) {
            return $this->update($request);
        }
    }

    /**
     * Update general settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            // Site information settings
            if ($request->has('site_name')) {
                Setting::set('site_name', $request->input('site_name'));
            }

            if ($request->has('site_description')) {
                Setting::set('site_description', $request->input('site_description'));
            }

            if ($request->has('site_email')) {
                Setting::set('site_email', $request->input('site_email'));
            }

            if ($request->has('site_phone')) {
                Setting::set('site_phone', $request->input('site_phone'));
            }

            if ($request->has('site_address')) {
                Setting::set('site_address', $request->input('site_address'));
            }

            // Social media settings
            if ($request->has('social_facebook')) {
                Setting::set('social_facebook', $request->input('social_facebook'));
            }

            if ($request->has('social_instagram')) {
                Setting::set('social_instagram', $request->input('social_instagram'));
            }

            if ($request->has('social_twitter')) {
                Setting::set('social_twitter', $request->input('social_twitter'));
            }

            // Privacy and terms settings
            if ($request->has('privacy_policy')) {
                Setting::set('privacy_policy', [
                    'en' => $request->input('privacy_policy.en', ''),
                    'ar' => $request->input('privacy_policy.ar', ''),
                ]);
            }

            if ($request->has('terms_conditions')) {
                Setting::set('terms_conditions', [
                    'en' => $request->input('terms_conditions.en', ''),
                    'ar' => $request->input('terms_conditions.ar', ''),
                ]);
            }

            // Mobile app settings
            if ($request->has('mobile_app_version')) {
                Setting::set('mobile_app_version', $request->input('mobile_app_version'));
            }

            if ($request->has('mobile_app_link_android')) {
                Setting::set('mobile_app_link_android', $request->input('mobile_app_link_android'));
            }

            if ($request->has('mobile_app_link_ios')) {
                Setting::set('mobile_app_link_ios', $request->input('mobile_app_link_ios'));
            }

            // WhatsApp settings
            if ($request->has('whatsapp_message_limit_system')) {
                Setting::set('whatsapp_message_limit_system', (int) $request->input('whatsapp_message_limit_system'));
            }

            if ($request->has('whatsapp_message_price')) {
                Setting::set('whatsapp_message_price', (float) $request->input('whatsapp_message_price'));
            }

            if ($request->has('whatsapp_reset_period')) {
                Setting::set('whatsapp_reset_period', $request->input('whatsapp_reset_period'));
            }

            if ($request->has('whatsapp_reset_period')) {
                Setting::set('whatsapp_reset_period', $request->input('whatsapp_reset_period'));
            }

            if ($request->has('subscription_price')) {
                Setting::set('subscription_price', (float) $request->input('subscription_price'));
            }

            if ($request->has('subscription_discount')) {
                Setting::set('subscription_discount', $request->input('subscription_discount'));
            }

            if ($request->has('subscription_total_price')) {
                Setting::set('subscription_total_price', (float) $request->input('subscription_total_price'));
            }

            if ($request->has('subscription_end_date')) {
                Setting::set('subscription_end_date', $request->input('subscription_end_date'));
            }

            Cache::forget('subscription_settings');

            // Save all settings
            Setting::save();

            DB::commit();

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => t('Settings updated successfully'),
                ]);
            }

            return redirect()->back()->with('success', t('Settings updated successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => t('Error updating settings: ').$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', t('Error updating settings: ').$e->getMessage());
        }
    }

    /**
     * Prepare common data for views
     *
     * @param  string|null  $action
     * @return array
     */
    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->config['view_path'],
            'config' => $this->config,
        ];

        // Load all settings
        $data['settings'] = [
            // Site information
            'site_name' => Setting::get('site_name', config('app.name')),
            'site_description' => Setting::get('site_description', ''),
            'site_email' => Setting::get('site_email', ''),
            'site_phone' => Setting::get('site_phone', ''),
            'site_address' => Setting::get('site_address', [
                'en' => '',
                'ar' => '',
            ]),
            // Social media
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_twitter' => Setting::get('social_twitter', ''),

            // Privacy and terms
            'privacy_policy' => Setting::get('privacy_policy', [
                'en' => '',
                'ar' => '',
            ]),
            'terms_conditions' => Setting::get('terms_conditions', [
                'en' => '',
                'ar' => '',
            ]),

            // Mobile app
            'mobile_app_version' => Setting::get('mobile_app_version', '1.0.0'),
            'mobile_app_link_android' => Setting::get('mobile_app_link_android', ''),
            'mobile_app_link_ios' => Setting::get('mobile_app_link_ios', ''),

            // WhatsApp
            'whatsapp_message_limit_system' => Setting::get('whatsapp_message_limit_system', 1000),
            'whatsapp_message_price' => Setting::get('whatsapp_message_price', 0.10),
            'whatsapp_reset_period' => Setting::get('whatsapp_reset_period', 'monthly'),
            'subscription_price' => Setting::get('subscription_price', 0),
            'subscription_discount' => Setting::get('subscription_discount', 0),
            'subscription_total_price' => Setting::get('subscription_total_price', 0),
            'subscription_end_date' => Setting::get('subscription_end_date', ''),

        ];

        return $data;
    }
}
