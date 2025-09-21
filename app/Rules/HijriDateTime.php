<?php

namespace App\Rules;

use GeniusTS\HijriDate\Hijri;
use Illuminate\Contracts\Validation\Rule;

class HijriDateTime implements Rule
{
    private $message;

    private $requireTime;

    public function __construct(bool $requireTime = true)
    {
        $this->requireTime = $requireTime;
    }

    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return false;
        }

        try {
            // Support multiple formats: "d/m/yyyy - H:i" or "d/m/yyyy H:i"
            if (strpos($value, ' - ') !== false) {
                $parts = explode(' - ', $value);
            } else {
                $parts = preg_split('/\s+/', $value, 2);
            }

            $datePart = trim($parts[0]);
            $timePart = isset($parts[1]) ? trim($parts[1]) : '';

            // Validate date part format (d/m/yyyy or dd/mm/yyyy)
            if (! preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $datePart, $matches)) {
                $this->message = 'Invalid date format. Expected: d/m/yyyy';

                return false;
            }

            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];

            // Basic validation for Hijri date ranges
            if ($day < 1 || $day > 30 || $month < 1 || $month > 12 || $year < 1400 || $year > 1500) {
                $this->message = 'Invalid Hijri date values';

                return false;
            }

            // Additional validation using Hijri library
            try {
                $gregorianDate = Hijri::convertToGregorian($day, $month, $year);
                if (! $gregorianDate) {
                    $this->message = 'Invalid Hijri date combination';

                    return false;
                }
            } catch (\Exception $e) {
                $this->message = 'Invalid Hijri date';

                return false;
            }

            // Validate time part if required (H:i)
            if ($this->requireTime && ! empty($timePart)) {
                if (! preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $timePart)) {
                    $this->message = 'Invalid time format. Expected: H:i';

                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->message = 'Validation error: '.$e->getMessage();

            return false;
        }
    }

    public function message(): string
    {
        return api($this->message ?? 'Invalid Hijri date format');
    }
}
