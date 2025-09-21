<?php

namespace App\Traits;

use Alkoumi\LaravelHijriDate\Hijri;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HijriDateTrait
{
    /**
     * Convert a Gregorian date to Hijri format
     */
    protected function convertGregorianToHijri(?string $gregorianDate): ?string
    {
        if (empty($gregorianDate)) {
            return null;
        }

        try {
            return Hijri::Date('d/m/Y', $gregorianDate);
        } catch (\Exception $e) {
            Log::warning("Failed to convert date to Hijri: {$gregorianDate}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Convert a Gregorian datetime to Hijri format with time
     */
    protected function convertGregorianToHijriDateTime(?string $gregorianDateTime): ?string
    {
        if (empty($gregorianDateTime)) {
            return null;
        }

        try {
            $carbonDate = Carbon::parse($gregorianDateTime);
            $hijriDate = Hijri::Date('d/m/Y', $carbonDate->toDateString());

            return $hijriDate.' - '.$carbonDate->format('H:i');
        } catch (\Exception $e) {
            Log::warning("Failed to convert datetime to Hijri: {$gregorianDateTime}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Convert a Hijri date to Gregorian format
     * Expects format: DD/MM/YYYY
     */
    protected function convertHijriToGregorian(?string $hijriDate): ?string
    {
        if (empty($hijriDate)) {
            return null;
        }

        try {
            if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $hijriDate, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];

                return Hijri::DateToGregorianFromDMY($day, $month, $year);
            }

            return null;
        } catch (\Exception $e) {
            Log::warning("Failed to convert Hijri to Gregorian: {$hijriDate}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Convert a Hijri datetime with time to Gregorian format
     * Expects format: DD/MM/YYYY - HH:MM
     */
    protected function convertHijriDateTimeToGregorian(?string $hijriDateTime): ?string
    {
        if (empty($hijriDateTime)) {
            return null;
        }

        try {
            if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})\s*-\s*(\d{1,2}):(\d{1,2})/', $hijriDateTime, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                $hour = $matches[4];
                $minute = $matches[5];

                $gregorianDate = Hijri::DateToGregorianFromDMY($day, $month, $year);

                return Carbon::createFromFormat('Y-m-d H:i', "{$gregorianDate} {$hour}:{$minute}")
                    ->format('Y-m-d H:i:s');
            }

            return null;
        } catch (\Exception $e) {
            Log::warning("Failed to convert Hijri datetime to Gregorian: {$hijriDateTime}", ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Get the attendance_date_time in Hijri format with time
     */
    public function getHijriAttendanceDateTimeAttribute(): ?string
    {
        return $this->convertGregorianToHijriDateTime($this->attendance_date_time);
    }

    /**
     * Get the attendance_date in Hijri format (date only)
     */
    public function getHijriAttendanceDateAttribute(): ?string
    {
        return $this->convertGregorianToHijri($this->attendance_date_time);
    }

    /**
     * Get the created_at date in Hijri format
     */
    public function getHijriCreatedAtAttribute(): ?string
    {
        return $this->convertGregorianToHijri($this->created_at);
    }

    /**
     * Get the updated_at date in Hijri format
     */
    public function getHijriUpdatedAtAttribute(): ?string
    {
        return $this->convertGregorianToHijri($this->updated_at);
    }
}
