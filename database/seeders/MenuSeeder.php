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



        $Menu[] = [

            'name' => t(config('modules.sliders.plural_name'), [], 'ar'),
            'name_en' => config('modules.sliders.plural_name'),
            'name_he' => config('modules.sliders.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-sliders-h"></i>', // FontAwesome icon for sliders
            'order' => 5,
            'permission_name' => config('modules.sliders.permissions.view'),
            'route' => config('modules.sliders.full_route_name') . '.index',
        ];

        $Menu[] = [

            'name' => t(config('modules.why_choose_us.plural_name'), [], 'ar'),
            'name_en' => config('modules.why_choose_us.plural_name'),
            'name_he' => config('modules.why_choose_us.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-question"></i>', // FontAwesome icon for sliders
            'order' => 5,
            'permission_name' => config('modules.why_choose_us.permissions.view'),
            'route' => config('modules.why_choose_us.full_route_name') . '.index',
        ];
        $Menu[] = [
            'name' => t(config('modules.customer_rates.plural_name'), [], 'ar'),
            'name_en' => config('modules.customer_rates.plural_name'),
            'name_he' => config('modules.customer_rates.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-star"></i>', // FontAwesome icon for sliders
            'order' => 5,
            'permission_name' => config('modules.customer_rates.permissions.view'),
            'route' => config('modules.customer_rates.full_route_name') . '.index',
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
            'name' => t(config('modules.sucess_stories.plural_name'), [], 'ar'),
            'name_en' => config('modules.sucess_stories.plural_name'),
            'name_he' => config('modules.sucess_stories.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-star"></i>', // FontAwesome icon for sucess stories
            'order' => 5,
            'permission_name' => config('modules.sucess_stories.permissions.view'),
            'route' => config('modules.sucess_stories.full_route_name') . '.index',
        ];
        $Menu[] = [
            'name' => t(config('modules.videos.plural_name'), [], 'ar'),
            'name_en' => config('modules.videos.plural_name'),
            'name_he' => config('modules.videos.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-video"></i>', // FontAwesome icon for videos
            'order' => 5,
            'permission_name' => config('modules.videos.permissions.view'),
            'route' => config('modules.videos.full_route_name') . '.index',
        ];

        $Menu[] = [
            'name' => t(config('modules.about_office.plural_name'), [], 'ar'),
            'name_en' => config('modules.about_office.plural_name'),
            'name_he' => config('modules.about_office.plural_name'),
            'route' => null,
            'icon_svg' => '<i class="fas fa-building"></i>', // FontAwesome icon for videos
            'order' => 5,
            'permission_name' => config('modules.about_office.permissions.view'),
            'route' => config('modules.about_office.full_route_name') . '.index',
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
