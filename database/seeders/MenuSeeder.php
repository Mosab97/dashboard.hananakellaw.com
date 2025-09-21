<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Menu = [
            [
                'name' => 'لوحة التحكم',
                'name_en' => 'Dashboard',
                'name_he' => 'Dashboard',
                'route' => 'home',
                'icon_svg' => getSvgIcon('dashboard'),
                'order' => 1,
                'permission_name' => 'dashboard_access',
            ],
        ];

        $config = config('modules.settings');
        $Menu[] = [
            'name' => t($config['plural_name'], [], 'ar'),
            'name_en' => $config['plural_name'],
            'name_he' => $config['plural_name'],
            'route' => null,
            'icon_svg' => getSvgIcon('settings'),
            'order' => 5,
            'permission_name' => $config['permissions']['view'],
            'subRoutes' => [
                [
                    'name' => t($config['children']['general']['plural_name'], [], 'ar'),
                    'name_en' => $config['children']['general']['plural_name'],
                    'name_he' => $config['children']['general']['plural_name'],
                    'route' => $config['children']['general']['full_route_name'] . '.index',
                    'icon_svg' => '',
                    'order' => 2,
                    'permission_name' => $config['children']['general']['permissions']['view'],
                ],

            ],
        ];
        // Add restaurant menu
        $config_categories = config('modules.categories');
        $config_sliders = config('modules.sliders');
        $config_sucess_stories = config('modules.sucess_stories');
        $config_videos = config('modules.videos');
        $Menu[] = [
            'name' => t($config_categories['plural_name'], [], 'ar'),
            'name_en' => $config_categories['plural_name'],
            'name_he' => $config_categories['plural_name'],
            'route' => null,
            'icon_svg' => '<i class="fas fa-th-list"></i>', // FontAwesome icon for categories
            'order' => 5,
            'permission_name' => $config_categories['permissions']['view'],
            'route' => $config_categories['full_route_name'] . '.index',
        ];


        $Menu[] = [

            'name' => t($config_sliders['plural_name'], [], 'ar'),
            'name_en' => $config_sliders['plural_name'],
            'name_he' => $config_sliders['plural_name'],
            'route' => null,
            'icon_svg' => '<i class="fas fa-sliders-h"></i>', // FontAwesome icon for sliders
            'order' => 5,
            'permission_name' => $config_sliders['permissions']['view'],
            'route' => $config_sliders['full_route_name'] . '.index',
        ];

        $Menu[] = [

            'name' => t(config('modules.services.plural_name'), [], 'ar'),
            'name_en' => config('modules.services.plural_name'),
            'name_he' => config('modules.services.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-cogs"></i>', // FontAwesome icon for sliders
            'order' => 5,
            'permission_name' => config('modules.services.permissions.view'),
            'route' => config('modules.services.full_route_name') . '.index',
        ];
        $Menu[] = [
            'name' => t($config_sucess_stories['plural_name'], [], 'ar'),
            'name_en' => $config_sucess_stories['plural_name'],
            'name_he' => $config_sucess_stories['plural_name'],
            'route' => null,
            'icon_svg' => '<i class="fas fa-star"></i>', // FontAwesome icon for sucess stories
            'order' => 5,
            'permission_name' => $config_sucess_stories['permissions']['view'],
            'route' => $config_sucess_stories['full_route_name'] . '.index',
        ];
        $Menu[] = [
            'name' => t($config_videos['plural_name'], [], 'ar'),
            'name_en' => $config_videos['plural_name'],
            'name_he' => $config_videos['plural_name'],
            'route' => null,
            'icon_svg' => '<i class="fas fa-video"></i>', // FontAwesome icon for videos
            'order' => 5,
            'permission_name' => $config_videos['permissions']['view'],
            'route' => $config_videos['full_route_name'] . '.index',
        ];


        DB::table('menus')->delete();

        foreach ($Menu as $menuItem) {
            // dd($menuItem);
            $parent = Menu::updateOrCreate([
                'name' => $menuItem['name'],
                'name_en' => $menuItem['name_en'],
                'name_he' => $menuItem['name_he'],
                'route' => $menuItem['route'],
                'icon_svg' => $menuItem['icon_svg'],
                'order' => $menuItem['order'],
                'permission_name' => $menuItem['permission_name'],
            ]);
            if (isset($menuItem['subRoutes'])) {
                foreach ($menuItem['subRoutes'] as $subMenu) {
                    Menu::updateOrCreate([
                        'name' => $subMenu['name'],
                        'name_en' => $subMenu['name_en'],
                        'name_he' => $subMenu['name_he'],
                        'route' => $subMenu['route'],
                        'icon_svg' => $subMenu['icon_svg'],
                        'order' => $subMenu['order'],
                        'permission_name' => $subMenu['permission_name'],
                        'parent_id' => $parent->id,
                    ]);
                }
            }
        }

        $this->command->info('Menu created successfully!');
    }
}
