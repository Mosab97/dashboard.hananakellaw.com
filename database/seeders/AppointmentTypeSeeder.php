<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentType::create([
            'name' => [
                'he' => 'לא, אין לי תיקים פתוחים בהוצאה לפועל',
                'ar' => 'لا ، ليس هناك ملفات مفتوحة بال הוצאה לפועל',
            ],
            'active' => true,
            'order' => 1,
        ]);
        AppointmentType::create([
            'name' => [
                'he' => 'יש לי תיקים פתוחים בהוצאה לפועל',
                'ar' => 'في ملفات مفتوحة بال הוצאה לפועל',
            ],
            'active' => true,
            'order' => 2,
        ]);
        AppointmentType::create([
            'name' => [
                'he' => 'בימים הקרובים צפוי להיפתח תיק בהוצאה לפועל',
                'ar' => 'بالأيام القريبة متوقع ينفتح ملفات بال הוצאה לפועל',
            ],
            'active' => true,
            'order' => 3,
        ]);
    }
}
