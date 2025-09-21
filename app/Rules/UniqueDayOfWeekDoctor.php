<?php

namespace App\Rules;

use App\Models\DoctorSchedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueDayOfWeekDoctor implements ValidationRule
{
    protected $doctorId;

    public function __construct($doctorId)
    {
        $this->doctorId = $doctorId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existingSchedule = DoctorSchedule::where('doctor_id', $this->doctorId)
            ->where('day_of_week', $value)
            ->first();

        if ($existingSchedule != null) {
            $fail('The day of week has already been taken for this doctor.');
        }
    }
}
