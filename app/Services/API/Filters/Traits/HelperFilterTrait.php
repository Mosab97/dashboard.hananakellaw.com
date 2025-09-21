<?php

namespace App\Services\API\Filters\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HelperFilterTrait
{
    /**
     * Helper method to search in translatable fields
     */
    private function searchInTranslatableFields($query, $field, $value)
    {
        Log::debug('Searching in translatable field', [
            'field' => $field,
            'value' => $value,
            'method' => 'searchInTranslatableFields',
        ]);

        $locales = config('app.locales');
        Log::debug('Locales retrieved from config', ['locales' => $locales]);

        foreach ($locales as $index => $locale) {
            $sqlFragment = "json_extract(LOWER($field), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
            $bindingValue = '%'.strtolower($value).'%';

            if ($index === 0) {
                Log::debug('Applying first locale filter', ['locale' => $locale]);
                $query->whereRaw($sqlFragment, [$bindingValue]);
            } else {
                Log::debug('Applying additional locale filter', ['locale' => $locale]);
                $query->orWhereRaw($sqlFragment, [$bindingValue]);
            }
        }

        Log::debug('Translatable field search completed');
    }

    /**
     * Common function for filtering by foreign keys and IDs
     */
    private function filterByForeignKey($query, $field, $value)
    {
        Log::debug('Filtering by foreign key', [
            'field' => $field,
            'value' => $value,
            'method' => 'filterByForeignKey',
        ]);

        if (is_array($value) || strpos($value, ',') !== false) {
            $ids = is_array($value) ? $value : explode(',', $value);
            $ids = array_map('trim', $ids);
            $ids = array_filter($ids, fn ($id) => $id !== null && $id !== '');

            Log::debug('Multiple IDs detected', ['ids' => $ids]);

            if (count($ids) > 0) {
                $query->whereIn("$field", $ids);
                Log::info('Multiple ID filter applied', [
                    'field' => $field,
                    'count' => count($ids),
                ]);
            } else {
                Log::warning('Empty ID array after filtering', [
                    'original_value' => $value,
                ]);
            }
        } else {
            Log::debug('Single ID detected', ['id' => $value]);
            $query->where("$field", $value);
            Log::info('Single ID filter applied');
        }
    }

    /**
     * Filter by date field
     */
    private function filterByDateField($query, $field, $value)
    {
        Log::debug('Filtering by date field', [
            'field' => $field,
            'value' => $value,
            'method' => 'filterByDateField',
        ]);

        try {
            // Check if value is in Hijri format (DD/MM/YYYY)
            $isHijriFormat = preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $value);
            Log::debug('Date format detection', ['is_hijri' => $isHijriFormat]);

            if ($isHijriFormat) {
                // Convert to Gregorian if Hijri
                if (strpos($value, ' to ') !== false) {
                    Log::debug('Hijri date range detected');
                    [$startDate, $endDate] = explode(' to ', trim($value));
                    $startDate = $this->convertHijriToGregorian(trim($startDate));
                    $endDate = $this->convertHijriToGregorian(trim($endDate));
                    $value = $startDate.' to '.$endDate;
                    Log::debug('Converted Hijri date range', [
                        'original' => trim($value),
                        'converted' => "$startDate to $endDate",
                    ]);
                } else {
                    Log::debug('Single Hijri date detected');
                    $value = $this->convertHijriToGregorian($value);
                    Log::debug('Converted Hijri date', [
                        'original' => trim($value),
                        'converted' => $value,
                    ]);
                }
            }

            if (strpos($value, ' to ') !== false) {
                Log::debug('Date range detected');
                [$startDate, $endDate] = explode(' to ', trim($value));
                $startDate = Carbon::parse(trim($startDate))->startOfDay();
                $endDate = Carbon::parse(trim($endDate))->endOfDay();

                Log::debug('Parsed date range', [
                    'start' => $startDate->toDateTimeString(),
                    'end' => $endDate->toDateTimeString(),
                ]);

                $query->whereBetween("$field", [$startDate, $endDate]);
                Log::info('Date range filter applied successfully');
            } else {
                Log::debug('Single date detected');
                $date = Carbon::parse(trim($value))->format('Y-m-d');
                Log::debug('Parsed single date', ['date' => $date]);

                $query->whereDate("$field", $date);
                Log::info('Single date filter applied successfully');
            }
        } catch (\Exception $e) {
            Log::error('Error parsing date', [
                'field' => $field,
                'value' => $value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
