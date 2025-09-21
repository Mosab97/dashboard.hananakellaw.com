<?php

namespace Database\Seeders;

use App\AppPermissionsHelper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::updateOrCreate(['name' => 'super-admin']);
        Role::updateOrCreate(['name' => 'admin']);

        // create permissions
        $permissionModules = AppPermissionsHelper::getPermissions();

        foreach ($permissionModules as $key => $permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(['name' => $permission]);
                $this->command->info($permission.' added  successfully!');
            }
        }
    }
}
