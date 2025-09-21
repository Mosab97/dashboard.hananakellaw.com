<?php

namespace App\Http\Controllers\API;

use Akaunting\Setting\Facade as Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    protected const CACHE_DURATION = 86400; // 24 hours

    /**
     * Get privacy policy in current language
     */
    public function getPrivacyPolicy()
    {
        try {
            $locale = app()->getLocale(); // Get current language
            $cacheKey = "privacy_policy_{$locale}";

            $privacyPolicy = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($locale) {
                $policy = Setting::get('privacy_policy', []);

                return $policy[$locale] ?? $policy['en']; // Fallback to English if translation not found
            });

            return apiSuccess($privacyPolicy);
        } catch (\Exception $e) {
            return apiError('Failed to retrieve privacy policy', 500);
        }
    }

    /**
     * Get terms and conditions in current language
     */
    public function getTermsConditions()
    {
        try {
            $locale = app()->getLocale();
            $cacheKey = "terms_conditions_{$locale}";

            $terms = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($locale) {
                $terms = Setting::get('terms_conditions', []);

                return $terms[$locale] ?? $terms['en'];
            });

            return apiSuccess(['content' => $terms]);
        } catch (\Exception $e) {
            return apiError('Failed to retrieve terms and conditions', 500);
        }
    }

    /**
     * Get social media links
     */
    public function getSocialMediaLinks()
    {
        try {
            $socialLinks = Cache::remember('social_media_links', self::CACHE_DURATION, function () {
                return [
                    'facebook' => Setting::get('social_facebook'),
                    'instagram' => Setting::get('social_instagram'),
                    'twitter' => Setting::get('social_twitter'),
                ];
            });

            return apiSuccess($socialLinks);
        } catch (\Exception $e) {
            return apiError('Failed to retrieve social media links', 500);
        }
    }

    /**
     * Get all public settings with current language content
     */
    public function getPublicSettings()
    {
        try {
            $locale = app()->getLocale();
            $cacheKey = "public_settings_{$locale}";

            $settings = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($locale) {
                $privacyPolicy = Setting::get('privacy_policy', []);
                $termsConditions = Setting::get('terms_conditions', []);

                return [
                    'privacy_policy' => $privacyPolicy[$locale] ?? $privacyPolicy['en'], // Fallback to English
                    'terms_conditions' => $termsConditions[$locale] ?? $termsConditions['en'],
                    'social_media' => [
                        'facebook' => Setting::get('social_facebook'),
                        'instagram' => Setting::get('social_instagram'),
                        'twitter' => Setting::get('social_twitter'),
                    ],
                ];
            });

            return apiSuccess($settings);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve public settings: '.$e->getMessage());

            return apiError('Failed to retrieve settings', 500);
        }
    }

    /**
     * Get mobile app settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMobileAppSettings()
    {
        $settings = [
            'version' => Setting::get('mobile_app_version'),
            'links' => [
                'android' => Setting::get('mobile_app_link_android'),
                'ios' => Setting::get('mobile_app_link_ios'),
            ],
        ];

        return apiSuccess($settings);

    }

    public function getSubscriptionSettings()
    {
        $settings = Cache::remember('subscription_settings', self::CACHE_DURATION, function () {
            return [
                'subscription_price' => Setting::get('subscription_price'),
                'subscription_discount' => Setting::get('subscription_discount'),
                'subscription_total_price' => Setting::get('subscription_total_price'),
                'subscription_end_date' => Setting::get('subscription_end_date'),
            ];
        });

        return apiSuccess($settings);
    }
}
