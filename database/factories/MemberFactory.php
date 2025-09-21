<?php

namespace Database\Factories;

use App\Enums\MemberAccountType;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        $phoneNumber = $this->faker->numerify('5########');

        return [
            'name' => [
                'en' => $this->faker->name(),
                'ar' => 'عربي '.$this->faker->name(),
            ],
            'email' => $this->faker->unique()->safeEmail(),
            'country_code' => '+966',
            'phone_number' => $phoneNumber,
            'full_phone' => '+966'.$phoneNumber,
            'password' => Hash::make('password123'),
            'type_id' => $this->faker->randomElement([
                MemberAccountType::TEACHER->getFromDatabase()->id,
                MemberAccountType::SCHOOL->getFromDatabase()->id,
            ]),
            'preferred_language' => $this->faker->randomElement(['en', 'ar']),
            'is_verified' => true,
            'active' => true,
            'last_login_at' => now(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterMaking(function (Member $member) {
            //
        })->afterCreating(function (Member $member) {
            //
        });
    }

    /**
     * Indicate that the member is a teacher.
     */
    public function teacher()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => MemberAccountType::TEACHER->getFromDatabase()->id,
            ];
        });
    }

    /**
     * Indicate that the member is a school.
     */
    public function school()
    {
        return $this->state(function (array $attributes) {
            return [
                'type_id' => MemberAccountType::SCHOOL->getFromDatabase()->id,
            ];
        });
    }
}
