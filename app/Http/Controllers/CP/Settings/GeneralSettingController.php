<?php

namespace App\Http\Controllers\CP\Settings;

use Akaunting\Setting\Facade as Setting;
use App\Http\Controllers\Controller;
use App\Services\Filters\SettingsFilterService;
use App\Traits\HijriDateTrait;
use Exception;
use Illuminate\Http\Request;
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

            if ($request->has('site_whatsapp')) {
                Setting::set('site_whatsapp', $request->input('site_whatsapp'));
            }

            if ($request->has('site_address')) {
                Setting::set('site_address', $request->input('site_address'));
            }

            if ($request->has('years_of_experience')) {
                Setting::set('years_of_experience', $request->input('years_of_experience'));
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
                Setting::set('privacy_policy', $request->input('privacy_policy'));
            }

            if ($request->has('terms_conditions')) {
                Setting::set('terms_conditions', $request->input('terms_conditions'));
            }

            if ($request->has('faq')) {
                Setting::set('faq', $request->input('faq'));
            }

            if ($request->has('disclaimer')) {
                Setting::set('disclaimer', $request->input('disclaimer'));
            }

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


        return $data;
    }
}
