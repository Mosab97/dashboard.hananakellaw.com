<?php



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
                'controller' => \App\Http\Controllers\CP\Settings\GeneralSettingController::class,
                'permissions' => [
                    'view' => 'view_general_settings',
                    'edit' => 'edit_general_settings',
                ],
            ],



        ],
    ],

    'about_office' => [
        'table' => 'about_office',
        'route' => 'about_office',
        'full_route_name' => 'about_office',
        'singular_name' => 'About Office',
        'plural_name' => 'About Office',
        'singular_key' => 'about_office',
        'plural_key' => 'about_office',
        'view_path' => 'CP.about_office.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\AboutOfficeController::class,
        'upload_path' => 'about_office',
        'permissions' => [
            'view' => 'view_about_office',
            'create' => 'create_about_office',
            'edit' => 'edit_about_office',
            'delete' => 'delete_about_office',
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
        'view_path' => 'CP.sliders.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\SliderController::class,
        'upload_path' => 'sliders',
        'permissions' => [
            'view' => 'view_sliders',
            'create' => 'create_sliders',
            'edit' => 'edit_sliders',
            'delete' => 'delete_sliders',
        ],
    ],
    'why_choose_us' => [
        'table' => 'why_choose_us',
        'route' => 'why_choose_us',
        'full_route_name' => 'why_choose_us',
        'singular_name' => 'Why Choose Us',
        'plural_name' => 'Why Choose Us',
        'singular_key' => 'why_choose_us',
        'plural_key' => 'why_choose_us',
        'view_path' => 'CP.why_choose_us.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\WhyChooseUsController::class,
        'upload_path' => 'why_choose_us',
        'permissions' => [
            'view' => 'view_why_choose_us',
            'create' => 'create_why_choose_us',
            'edit' => 'edit_why_choose_us',
            'delete' => 'delete_why_choose_us',
        ],
    ],

    'services' => [
        'table' => 'services',
        'route' => 'services',
        'full_route_name' => 'services',
        'singular_name' => 'Service',
        'plural_name' => 'Services',
        'singular_key' => 'service',
        'plural_key' => 'services',
        'view_path' => 'CP.services.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\ServiceController::class,
        'upload_path' => 'services',
        'permissions' => [
            'view' => 'view_services',
            'create' => 'create_services',
            'edit' => 'edit_services',
            'delete' => 'delete_services',
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
        'view_path' => 'CP.sucess_stories.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\SucessStoryController::class,
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
        'view_path' => 'CP.videos.',
        'id_field' => 'id',
        'controller' => \App\Http\Controllers\CP\VideoController::class,
        'upload_path' => 'videos',
        'permissions' => [
            'view' => 'view_videos',
            'create' => 'create_videos',
            'edit' => 'edit_videos',
            'delete' => 'delete_videos',
        ],
    ],

];
