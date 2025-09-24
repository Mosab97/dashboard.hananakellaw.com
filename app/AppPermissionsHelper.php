<?php

namespace App;

use App\Models\Attachment;
use Exception;
use Illuminate\Support\Facades\Route;

class AppPermissionsHelper
{
    /*
        :::::::: IMPORTANT NOTE ::::::::

        all permission should have postfix as one of the following
        _access
        _add
        _edit
        _delete
    */
    public static function getPermissions()
    {
        $permissions = [
            'User Management Module' => [
                'Manage' => 'user_management_access',
            ],
            'Settings Module' => [
                'Menu settings' => 'settings_menu_access',
                'Constants' => 'settings_constants_access',

            ],

            Attachment::ui['p_ucf'] => [
                'access' => Attachment::ui['s_lcf'] . '_access',
                'add' => Attachment::ui['s_lcf'] . '_add',
                'edit' => Attachment::ui['s_lcf'] . '_edit',
                'delete' => Attachment::ui['s_lcf'] . '_delete',
            ],

        ];

        // Main Settings module permissions
        $config = config('modules.settings');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
            'delete' => $config['permissions']['delete'],
        ];
        // Main Settings module permissions
        $config = config('modules.settings');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
            'delete' => $config['permissions']['delete'],
        ];

        // General Settings permissions
        $config = config('modules.settings.children.general');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'edit' => $config['permissions']['edit'],
        ];


        // About Office permissions (child of restaurants)
        $config = config('modules.about_office');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
        ];



        // slider permissions (child of restaurants)
        $config = config('modules.sliders');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
            'delete' => $config['permissions']['delete'],
        ];
        // slider permissions (child of restaurants)
        $config = config('modules.services');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
            'delete' => $config['permissions']['delete'],
        ];
        // sucess stories permissions (child of restaurants)
        $config = config('modules.sucess_stories');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
            'delete' => $config['permissions']['delete'],
        ];
        // videos permissions (child of restaurants)
        $config = config('modules.videos');
        $permissions[$config['plural_name']] = [
            'access' => $config['permissions']['view'],
            'add' => $config['permissions']['create'],
            'edit' => $config['permissions']['edit'],
            'delete' => $config['permissions']['delete'],
        ];

        $permissionFlatten = collect($permissions)->unique()->flatten(1);
        self::CheckMiddlewares($permissionFlatten);

        return $permissions;
    }

    private static function CheckMiddlewares($usedPermissions)
    {

        $routes = Route::getRoutes()->getRoutesByName();
        $remove = [
            'sanctum.csrf-cookie',
            'ignition.healthCheck',
            'ignition.executeSolution',
            'ignition.updateConfig',
            'login',
            'authenticate',
            'logout',
            'home',
            'setLanguage',
        ];

        $routes = array_diff_key($routes, array_flip($remove));
        // $routeNames = array_keys($routes);

        $routesAndPermissions = [];

        foreach ($routes as $route) {
            $routeMiddleware = collect($route->action['middleware']);
            $filtered = $routeMiddleware->filter(function ($value, $key) {
                if (strpos($value, 'permission:') === 0) {
                    return $value;
                }
            })->map(function ($item, $key) {
                $permission = substr($item, 11);
                $permissions = explode('|', $permission);

                return $permissions;
            })->flatten(1);
            // dd($filtered);
            foreach ($filtered as $permissionMiddleware) {
                // code...
                array_push($routesAndPermissions, $permissionMiddleware);
            }
        }
        $routesAndPermissions = collect($routesAndPermissions)->unique();
        if ($routesAndPermissions->diff($usedPermissions)->count() > 0) {

            $diff = $routesAndPermissions->diff($usedPermissions)->toArray();
            throw new Exception("Please Check AppPermissionsHelper.php file \n middleware used in web routes aren't included!" . implode(',', $diff));
        }
    }
}
