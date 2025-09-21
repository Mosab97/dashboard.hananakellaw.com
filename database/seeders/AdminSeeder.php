<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->create_admin();
    }

    private function create_admin(): void
    {
        Log::info('............... AdminSeeder run started ...............');

        // Admin user setup
        $adminUser = User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'name' => [
                'en' => 'Admin',
                'ar' => 'Admin',
            ],
            'mobile' => '0000',
            'password' => Hash::make('admin'),
            'active' => true,
        ]);
        $adminUser->assignRole('super-admin');
        // Admin user setup
        $hossamUser = User::updateOrCreate(['email' => 'hossam@gmail.com'], [
            'name' => [
                'en' => 'Hossam',
                'ar' => 'حسام',
            ],
            'mobile' => '01000000000',
            'password' => Hash::make('admin'),
            'active' => true,
        ]);
        $hossamUser->assignRole('super-admin');

        Log::info('AdminSeeder run completed');
    }
}
