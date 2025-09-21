<?php

namespace App\Rules;

use App\Models\HospitalClinicSchedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueDayOfWeek implements ValidationRule
{
    protected $hospitalClinicId;

    public function __construct($hospitalClinicId)
    {
        $this->hospitalClinicId = $hospitalClinicId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existingSchedule = HospitalClinicSchedule::where('hospital_clinic_id', $this->hospitalClinicId)
            ->where('day_of_week', $value)
            ->first();

        if ($existingSchedule != null) {
            $fail('The day of week has already been taken for this hospital/clinic.');
        }
    }
}
