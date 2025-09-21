<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\TeacherProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tardiness>
 */
class TardinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tardy_date' => Carbon::now()->subDays(rand(0, 30))->setHour(8)->setMinute(rand(5, 45)),
            'delay_minutes' => rand(5, 60),
            'reason' => [
                'en' => $this->faker->sentence(),
                'ar' => 'سبب التأخير',
            ],
            'school_id' => SchoolProfile::factory(),
            'created_by' => Member::factory(),
        ];
    }

    /**
     * Configure the model factory to create a student tardiness.
     */
    public function student(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'tardy_id' => Student::factory(),
                'tardy_type' => Student::class,
            ];
        });
    }

    /**
     * Configure the model factory to create a teacher tardiness.
     */
    public function teacher(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'tardy_id' => TeacherProfile::factory(),
                'tardy_type' => TeacherProfile::class,
            ];
        });
    }
}
