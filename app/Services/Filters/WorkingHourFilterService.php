<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class WorkingHourFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply working hour filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying working hour filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping working hour filter for: $key as the value is null or empty.");
            }
        }

        logQuery($query);
        Log::info('All working hour filters applied.');

        return $query;
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {
        switch ($key) {
            case 'search':
                $this->filterBySearch($query, $value);
                break;

            case 'day':
                $this->filterByDate($query, $value);
                break;
            case 'start_time':
                $this->filterByStartTime($query, $value);
                break;
            case 'end_time':
                $this->filterByEndTime($query, $value);
                break;
            default:
                Log::warning("Unknown working hour filter key: $key");
                break;
        }
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        $query->where(function ($query) use ($value) {
            $query->where('day', 'like', "%$value%")
                ->orWhere('start_time', 'like', "%$value%")
                ->orWhere('end_time', 'like', "%$value%");
        });
    }

    /**
     * Filters the query by date.
     */
    private function filterByDate($query, $value)
    {
        $query->where('day', $value);
    }

    /**
     * Filters the query by start time.
     */
    private function filterByStartTime($query, $value)
    {
        $query->where('start_time', $value);
    }

    /**
     * Filters the query by end time.
     */
    private function filterByEndTime($query, $value)
    {
        $query->where('end_time', $value);
    }
}
