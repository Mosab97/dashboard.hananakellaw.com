<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class WorkingDayFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply working day filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying working day filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping working day filter for: $key as the value is null or empty.");
            }
        }

        logQuery($query);
        Log::info('All working day filters applied.');

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
                $this->filterByDay($query, $value);
                break;
            default:
                Log::warning("Unknown working day filter key: $key");
                break;
        }
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        $query->where(function ($query) use ($value) {
            $query->where('day', 'like', "%$value%");
        });
    }
    /**
     * Filters the query by day.
     */
    private function filterByDay($query, $value)
    {
        $query->where('day', $value);
    }
}
