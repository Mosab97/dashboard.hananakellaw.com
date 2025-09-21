<?php

namespace App\Services\API\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait TimeFilterTrait
{
    /**
     * Filters the query by a time field.
     *
     * @param  Builder  $query
     * @param  string  $field  The time field name
     * @param  string  $value  The time value
     */
    protected function filterByTime($query, $field, $value)
    {
        $fieldWithTable = $this->tableName.'.'.$field;

        // Handle time range with "to" separator
        if (strpos($value, ' to ') !== false) {
            $this->filterByTimeRange($query, $fieldWithTable, $value);
        }
        // Handle minutes as integer
        elseif (is_numeric($value)) {
            $this->filterByMinutes($query, $fieldWithTable, $value);
        }
        // Handle direct time format (HH:MM)
        else {
            $query->whereTime($fieldWithTable, '=', $value);
            Log::info("Filtering by $field: $value");
        }
    }

    /**
     * Filter by time range
     *
     * @param  Builder  $query
     * @param  string  $fieldWithTable
     * @param  string  $value
     */
    protected function filterByTimeRange($query, $fieldWithTable, $value)
    {
        [$startTime, $endTime] = explode(' to ', $value);
        $startTime = trim($startTime);
        $endTime = trim($endTime);

        $query->whereTime($fieldWithTable, '>=', $startTime)
            ->whereTime($fieldWithTable, '<=', $endTime);

        Log::info("Filtering by time between $startTime and $endTime");
    }

    /**
     * Filter by minutes
     *
     * @param  Builder  $query
     * @param  string  $fieldWithTable
     * @param  int  $minutes
     */
    protected function filterByMinutes($query, $fieldWithTable, $minutes)
    {
        $minutes = (int) $minutes;

        // Convert minutes to hours and minutes
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        $timeStr = sprintf('%02d:%02d', $hours, $mins);

        $query->whereTime($fieldWithTable, '=', $timeStr);
        Log::info("Filtering by time: $timeStr ($minutes minutes)");
    }

    /**
     * Convert minutes to time format (HH:MM)
     */
    protected function minutesToTimeFormat(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }
}
