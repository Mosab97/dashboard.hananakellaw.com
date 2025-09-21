<?php

namespace App\Services\API\Filters\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait DateTimeFilterTrait
{
    /**
     * Filters the query by a date field, supporting both Gregorian and Hijri dates.
     *
     * @param  Builder  $query
     * @param  string  $field  The date field name
     * @param  string  $value  The date value
     */
    protected function filterByDate($query, $field, $value)
    {
        $fieldWithTable = $this->tableName.'.'.$field;

        // Check if value is in Hijri format (DD/MM/YYYY)
        $isHijriFormat = preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $value);

        // Check if the value contains "to" for a date range
        if (strpos($value, ' to ') !== false) {
            $this->filterByDateRange($query, $fieldWithTable, $value, $isHijriFormat);
        } else {
            $this->filterBySingleDate($query, $fieldWithTable, $value, $isHijriFormat);
        }
    }

    /**
     * Filter by date range
     *
     * @param  Builder  $query
     * @param  string  $fieldWithTable
     * @param  string  $value
     * @param  bool  $isHijriFormat
     */
    protected function filterByDateRange($query, $fieldWithTable, $value, $isHijriFormat)
    {
        // Handle date range
        $dates = array_pad(explode(' to ', trim($value)), 2, null);

        $startDateStr = trim($dates[0]);
        $endDateStr = trim($dates[1] ?? $dates[0]);

        // Convert dates if in Hijri format
        if ($isHijriFormat) {
            Log::info('Detected Hijri date format, converting to Gregorian for database query');

            // Use the HijriDateTrait method to convert dates
            $startDateStr = $this->convertHijriToGregorian($startDateStr);
            $endDateStr = $this->convertHijriToGregorian($endDateStr);

            if (! $startDateStr || ! $endDateStr) {
                Log::warning('Failed to convert Hijri date(s) to Gregorian', [
                    'start_date' => $dates[0],
                    'end_date' => $dates[1] ?? $dates[0],
                ]);

                return;
            }
        }

        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();

        $query->whereBetween($fieldWithTable, [$startDate, $endDate]);
        Log::info("Filtering by $fieldWithTable between $startDate and $endDate");
    }

    /**
     * Filter by single date
     *
     * @param  Builder  $query
     * @param  string  $fieldWithTable
     * @param  string  $value
     * @param  bool  $isHijriFormat
     */
    protected function filterBySingleDate($query, $fieldWithTable, $value, $isHijriFormat)
    {
        // Handle single date
        $dateStr = trim($value);

        // Convert date if in Hijri format
        if ($isHijriFormat) {
            Log::info('Detected Hijri date format, converting to Gregorian for database query');

            // Use the HijriDateTrait method to convert the date
            $dateStr = $this->convertHijriToGregorian($dateStr);

            if (! $dateStr) {
                Log::warning('Failed to convert Hijri date to Gregorian', ['date' => $value]);

                return;
            }
        }

        $date = Carbon::parse($dateStr);
        $query->whereDate($fieldWithTable, '=', $date->format('Y-m-d'));
        Log::info("Filtering by $fieldWithTable on single date: ".$date->format('Y-m-d'));
    }

    /**
     * Filter by relation date field
     *
     * @param  Builder  $query
     * @param  array  $relations
     * @param  string  $field
     * @param  string  $value
     */
    protected function filterByRelationDateField($query, $relations, $field, $value)
    {
        if (count($relations) === 1) {
            $tableName = $this->getTableNameForRelation($relations[0]);
        } else {
            $tableName = $this->getTableNameForRelation(implode('.', $relations));
        }

        // Check if value is in Hijri format (DD/MM/YYYY)
        $isHijriFormat = preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $value);

        // Check if the value contains "to" for a date range
        if (strpos($value, ' to ') !== false) {
            $this->filterByRelationDateRange($query, $relations, $field, $value, $isHijriFormat, $tableName);
        } else {
            $this->filterByRelationSingleDate($query, $relations, $field, $value, $isHijriFormat, $tableName);
        }
    }

    /**
     * Filter by relation date range
     *
     * @param  Builder  $query
     * @param  array  $relations
     * @param  string  $field
     * @param  string  $value
     * @param  bool  $isHijriFormat
     * @param  string  $tableName
     */
    protected function filterByRelationDateRange($query, $relations, $field, $value, $isHijriFormat, $tableName)
    {
        // Handle date range
        $dates = array_pad(explode(' to ', trim($value)), 2, null);

        $startDateStr = trim($dates[0]);
        $endDateStr = trim($dates[1] ?? $dates[0]);

        // Convert dates if in Hijri format
        if ($isHijriFormat) {
            $startDateStr = $this->convertHijriToGregorian($startDateStr);
            $endDateStr = $this->convertHijriToGregorian($endDateStr);

            if (! $startDateStr || ! $endDateStr) {
                Log::warning('Failed to convert Hijri date(s) to Gregorian', [
                    'start_date' => $dates[0],
                    'end_date' => $dates[1] ?? $dates[0],
                ]);

                return;
            }
        }

        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $endDate = Carbon::parse($endDateStr)->endOfDay();

        // Apply the date range filter to the relation
        if (count($relations) === 1) {
            $query->whereHas($relations[0], function ($q) use ($field, $startDate, $endDate, $tableName) {
                $q->whereBetween("{$tableName}.{$field}", [$startDate, $endDate]);
            });
        } elseif (count($relations) === 2) {
            $query->whereHas($relations[0], function ($q) use ($relations, $field, $startDate, $endDate, $tableName) {
                $q->whereHas($relations[1], function ($subQ) use ($field, $startDate, $endDate, $tableName) {
                    $subQ->whereBetween("{$tableName}.{$field}", [$startDate, $endDate]);
                });
            });
        }

        Log::info("Filtering by relation date field $field between $startDate and $endDate");
    }

    /**
     * Filter by relation single date
     *
     * @param  Builder  $query
     * @param  array  $relations
     * @param  string  $field
     * @param  string  $value
     * @param  bool  $isHijriFormat
     * @param  string  $tableName
     */
    protected function filterByRelationSingleDate($query, $relations, $field, $value, $isHijriFormat, $tableName)
    {
        // Handle single date
        $dateStr = trim($value);

        // Convert date if in Hijri format
        if ($isHijriFormat) {
            $dateStr = $this->convertHijriToGregorian($dateStr);

            if (! $dateStr) {
                Log::warning('Failed to convert Hijri date to Gregorian', ['date' => $value]);

                return;
            }
        }

        $date = Carbon::parse($dateStr);

        // Apply the single date filter to the relation
        if (count($relations) === 1) {
            $query->whereHas($relations[0], function ($q) use ($field, $date, $tableName) {
                $q->whereDate("{$tableName}.{$field}", '=', $date->format('Y-m-d'));
            });
        } elseif (count($relations) === 2) {
            $query->whereHas($relations[0], function ($q) use ($relations, $field, $date, $tableName) {
                $q->whereHas($relations[1], function ($subQ) use ($field, $date, $tableName) {
                    $subQ->whereDate("{$tableName}.{$field}", '=', $date->format('Y-m-d'));
                });
            });
        }

        Log::info("Filtering by relation date field $field on single date: ".$date->format('Y-m-d'));
    }
}
