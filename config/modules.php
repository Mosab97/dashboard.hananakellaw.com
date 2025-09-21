<?php

use App\Http\Controllers\CP\Attachments\AttachmentController;
use App\Http\Controllers\CP\Notify\NotifyController;
use App\Http\Controllers\CP\Restaurant\ProductController;
use App\Http\Controllers\CP\Restaurant\ProductSizeController;
use App\Http\Controllers\CP\Restaurant\SizeController;
use App\Http\Controllers\CP\Restaurant\SliderController;
use App\Http\Controllers\CP\Restaurant\SucessStoryController;
use App\Http\Controllers\CP\Restaurant\VideoController;
use App\Http\Controllers\CP\School\ClassroomController;
use App\Http\Controllers\CP\School\SchoolController;
use App\Http\Controllers\CP\School\SchoolSubscriptionController;
use App\Http\Controllers\CP\School\StudentController;
use App\Http\Controllers\CP\School\TeacherController;
use App\Http\Controllers\CP\Settings\GeneralSettingController;
use App\Http\Controllers\CP\Settings\OnboardingController;
use App\Http\Controllers\CP\Settings\UltraMsgInstanceController;
use App\Http\Controllers\CP\Subscription\SubscriptionController;
use App\Http\Controllers\CP\Subscription\SubscriptionPricingController;

return [



    'settings' => [
        'table' => 'settings',
        'route' => 'settings',
        'full_route_name' => 'settings',
        'singular_name' => 'Setting',
        'plural_name' => 'Settings',
        'singular_key' => 'setting',
        'plural_key' => 'settings',
        'view_path' => 'CP.settings.',
        'id_field' => 'id',
        // 'controller' => SettingController::class,
        'upload_path' => 'settings',
        'permissions' => [
            'view' => 'view_settings',
            'create' => 'create_settings',
            'edit' => 'edit_settings',
            'delete' => 'delete_settings',
        ],
        'children' => [
            'general' => [
                'route' => 'general',
                'full_route_name' => 'settings.general',
                'singular_name' => 'General Setting',
                'singular_key' => 'general_setting',

                'plural_name' => 'General Settings',
                'view_path' => 'CP.settings.general.',
                'controller' => GeneralSettingController::class,
                'permissions' => [
                    'view' => 'view_general_settings',
                    'edit' => 'edit_general_settings',
                ],
            ],
            'onboardings' => [
                'table' => 'onboardings',
                'route' => 'onboardings',
                'full_route_name' => 'settings.onboardings',
                'singular_name' => 'Onboarding',
                'plural_name' => 'Onboardings',
                'singular_key' => 'onboarding',
                'plural_key' => 'onboardings',
                'view_path' => 'CP.settings.onboardings.',
                'id_field' => 'id',
                'controller' => OnboardingController::class,
                'upload_path' => 'onboardings',
                'permissions' => [
                    'view' => 'view_onboardings',
                    'create' => 'create_onboardings',
                    'edit' => 'edit_onboardings',
                    'delete' => 'delete_onboardings',
                ],
                'children' => [],
            ],
            'ultramsg' => [
                'table' => 'ultra_msg_instances',
                'route' => 'ultramsg',
                'full_route_name' => 'settings.ultramsg',
                'singular_name' => 'UltraMsg Instance',
                'plural_name' => 'UltraMsg Instances',
                'singular_key' => 'ultramsg_instance',
                'plural_key' => 'ultramsg_instances',
                'view_path' => 'CP.settings.ultramsg.',
                'id_field' => 'id',
                'controller' => UltraMsgInstanceController::class,
                'upload_path' => 'ultramsg',
                'permissions' => [
                    'view' => 'view_ultramsg_instances',
                    'create' => 'create_ultramsg_instances',
                    'edit' => 'edit_ultramsg_instances',
                    'delete' => 'delete_ultramsg_instances',
                ],
            ],

        ],
    ],

    'categories' => [
        'table' => 'categories',
        'route' => 'categories',
        'full_route_name' => 'categories',
        'singular_name' => 'Category',
        'plural_name' => 'Categories',
        'singular_key' => 'category',
        'plural_key' => 'categories',
        'view_path' => 'CP.restaurants_module.categories.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\Restaurant\CategoryController::class,
        'upload_path' => 'categories',
        'permissions' => [
            'view' => 'view_categories',
            'create' => 'create_categories',
            'edit' => 'edit_categories',
            'delete' => 'delete_categories',
        ],
    ],

    'sliders' => [
        'table' => 'sliders',
        'route' => 'sliders',
        'full_route_name' => 'sliders',
        'singular_name' => 'Slider',
        'plural_name' => 'Sliders',
        'singular_key' => 'slider',
        'plural_key' => 'sliders',
        'view_path' => 'CP.restaurants_module.sliders.',
        'id_field' => 'id',
        'controller' => SliderController::class,
        'upload_path' => 'sliders',
        'permissions' => [
            'view' => 'view_sliders',
            'create' => 'create_sliders',
            'edit' => 'edit_sliders',
            'delete' => 'delete_sliders',
        ],
    ],

    'sucess_stories' => [
        'table' => 'sucess_stories',
        'route' => 'sucess_stories',
        'full_route_name' => 'sucess_stories',
        'singular_name' => 'Sucess Story',
        'plural_name' => 'Sucess Stories',
        'singular_key' => 'sucess_story',
        'plural_key' => 'sucess_stories',
        'view_path' => 'CP.restaurants_module.sucess_stories.',
        'id_field' => 'id',
        'controller' => SucessStoryController::class,
        'upload_path' => 'sucess_stories',
        'permissions' => [
            'view' => 'view_sucess_stories',
            'create' => 'create_sucess_stories',
            'edit' => 'edit_sucess_stories',
            'delete' => 'delete_sucess_stories',
        ],
    ],
    'videos' => [
        'table' => 'videos',
        'route' => 'videos',
        'full_route_name' => 'videos',
        'singular_name' => 'Video',
        'plural_name' => 'Videos',
        'singular_key' => 'video',
        'plural_key' => 'videos',
        'view_path' => 'CP.restaurants_module.videos.',
        'id_field' => 'id',
        'controller' => VideoController::class,
        'upload_path' => 'videos',
        'permissions' => [
            'view' => 'view_videos',
            'create' => 'create_videos',
            'edit' => 'edit_videos',
            'delete' => 'delete_videos',
        ],
    ],

];
