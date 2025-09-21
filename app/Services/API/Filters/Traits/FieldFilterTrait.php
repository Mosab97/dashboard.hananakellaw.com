<?php

namespace App\Services\API\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait FieldFilterTrait
{
    /**
     * Filters the query by a regular field with exact matching.
     *
     * @param  Builder  $query
     * @param  string  $field  The field name (id, etc.)
     * @param  mixed  $value  The value to match
     */
    protected function filterByRegularField($query, $field, $value)
    {
        $fieldWithTable = $this->tableName.'.'.$field;

        // Check if it's an array of values or a comma-separated string
        if (is_array($value) || strpos($value, ',') !== false) {
            $values = is_array($value) ? $value : explode(',', $value);
            $values = array_map('trim', $values);
            $values = $this->filterArrayForNullValues($values);

            if (count($values) > 0) {
                $query->whereIn($fieldWithTable, $values);
                Log::info("Filtering by $field with multiple values:", $values);
            }
        } else {
            // Special case for partial matching on specific fields
            if (in_array($field, $this->partialMatchFields)) {
                $query->where($fieldWithTable, 'LIKE', "%{$value}%");
                Log::info("Filtering by $field with partial match: %{$value}%");
            } else {
                $query->where($fieldWithTable, $value);
                Log::info("Filtering by $field with single value: $value");
            }
        }
    }

    /**
     * Filters the query by a translatable field.
     *
     * @param  Builder  $query
     * @param  string  $field  The field name (name, etc.)
     * @param  string  $value  The search value
     */
    protected function filterByTranslatable($query, $field, $value)
    {
        Log::info("Filtering by translatable field $field: $value");

        // Get locales from config
        $locales = config('app.locales');

        $query->where(function ($subQuery) use ($field, $value, $locales) {
            // Search in the field across all locales
            foreach ($locales as $locale) {
                $searchValue = '%'.strtolower($value).'%';
                $rawQuery = "json_extract(LOWER({$this->tableName}.{$field}), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
                $subQuery->orWhereRaw($rawQuery, [$searchValue]);
            }
        });
    }

    /**
     * Filters the query by a foreign key field.
     *
     * @param  Builder  $query
     * @param  string  $field  The field name (foregin_id, etc.)
     * @param  mixed  $value  The foreign key value(s)
     */
    protected function filterByForeignKey($query, $field, $value)
    {
        $fieldWithTable = $this->tableName.'.'.$field;

        // Handle both array and comma-separated string
        $values = is_array($value) ? $value : (strpos($value, ',') !== false ? explode(',', $value) : [$value]);
        $values = array_map('trim', $values);
        $ids = $this->filterArrayForNullValues($values);

        if (count($ids) > 0) {
            $query->whereIn($fieldWithTable, $ids);
            Log::info("Filtering by $field with values:", $ids);
        } else {
            Log::info("No valid $field values to filter.");
        }
    }

    /**
     * Filters the query by a boolean field.
     *
     * @param  Builder  $query
     * @param  string  $field  The boolean field name
     * @param  mixed  $value  The boolean value
     */
    protected function filterByBoolean($query, $field, $value)
    {
        $fieldWithTable = $this->tableName.'.'.$field;
        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $query->where($fieldWithTable, $boolValue);
        Log::info("Filtering by $field: ".($boolValue ? 'true' : 'false'));
    }

    /**
     * Filters the query by a JSON array field.
     *
     * @param  Builder  $query
     * @param  string  $field  The JSON array field name
     * @param  mixed  $value  The value(s) to search for in the array
     */
    protected function filterByJsonArray($query, $field, $value)
    {
        $fieldWithTable = $this->tableName.'.'.$field;

        // Handle both array and comma-separated string
        $values = is_array($value) ? $value : (strpos($value, ',') !== false ? explode(',', $value) : [$value]);
        $values = array_map('trim', $values);
        $arrayValues = $this->filterArrayForNullValues($values);

        if (count($arrayValues) > 0) {
            // Use JSON contains to check if the array field contains any of the specified values
            $query->where(function ($q) use ($fieldWithTable, $arrayValues) {
                foreach ($arrayValues as $val) {
                    // In MySQL, you can use JSON_CONTAINS
                    $q->orWhereRaw("JSON_CONTAINS({$fieldWithTable}, ?)", [json_encode($val)]);
                }
            });
            Log::info("Filtering by $field with values:", $arrayValues);
        } else {
            Log::info("No valid $field values to filter.");
        }
    }

    /**
     * Apply an appropriate filter based on field type
     *
     * @param  Builder  $query
     * @param  string  $field  The field name
     * @param  mixed  $value  The filter value
     */
    protected function applyFieldFilter($query, $field, $value)
    {
        // Determine the appropriate filter based on the field type
        if (in_array($field, $this->getTranslatableFields())) {
            $this->applyTranslatableFilter($query, $field, $value);
        } elseif (in_array($field, $this->getPartialMatchFields())) {
            $query->where($field, 'LIKE', "%{$value}%");
        } elseif (in_array($field, $this->getDateFields())) {
            $this->applyDateFilter($query, $field, $value);
        } elseif (in_array($field, $this->getBooleanFields())) {
            $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            $query->where($field, $boolValue);
        } else {
            // Default to exact match
            $query->where($field, $value);
        }
    }

    /**
     * Apply translatable field filter
     *
     * @param  Builder  $query
     * @param  string  $field  The field name
     * @param  string  $value  The search value
     */
    protected function applyTranslatableFilter($query, $field, $value)
    {
        $locales = config('app.locales');
        $query->where(function ($subQuery) use ($field, $value, $locales) {
            foreach ($locales as $locale) {
                $searchValue = '%'.strtolower($value).'%';
                $rawQuery = "json_extract(LOWER($field), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
                $subQuery->orWhereRaw($rawQuery, [$searchValue]);
            }
        });
    }

    /**
     * Apply date field filter
     *
     * @param  Builder  $query
     * @param  string  $field  The field name
     * @param  string  $value  The date value
     */
    protected function applyDateFilter($query, $field, $value)
    {
        // Check if value is in Hijri format (DD/MM/YYYY)
        $isHijriFormat = preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $value);

        // Check if the value contains "to" for a date range
        if (strpos($value, ' to ') !== false) {
            $this->applyDateRangeFilter($query, $field, $value, $isHijriFormat);
        } else {
            $this->applySingleDateFilter($query, $field, $value, $isHijriFormat);
        }
    }

    /**
     * Apply date range filter
     *
     * @param  Builder  $query
     * @param  string  $field
     * @param  string  $value
     * @param  bool  $isHijriFormat
     */
    protected function applyDateRangeFilter($query, $field, $value, $isHijriFormat)
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
                return;
            }
        }

        $startDate = \Carbon\Carbon::parse($startDateStr)->startOfDay();
        $endDate = \Carbon\Carbon::parse($endDateStr)->endOfDay();

        $query->whereBetween($field, [$startDate, $endDate]);
    }

    /**
     * Apply single date filter
     *
     * @param  Builder  $query
     * @param  string  $field
     * @param  string  $value
     * @param  bool  $isHijriFormat
     */
    protected function applySingleDateFilter($query, $field, $value, $isHijriFormat)
    {
        // Handle single date
        $dateStr = trim($value);

        // Convert date if in Hijri format
        if ($isHijriFormat) {
            $dateStr = $this->convertHijriToGregorian($dateStr);

            if (! $dateStr) {
                return;
            }
        }

        $date = \Carbon\Carbon::parse($dateStr);
        $query->whereDate($field, '=', $date->format('Y-m-d'));
    }
}
