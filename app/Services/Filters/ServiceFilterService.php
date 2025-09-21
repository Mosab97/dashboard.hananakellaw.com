<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class ServiceFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply service filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying service filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping service filter for: $key as the value is null or empty.");
            }
        }

        logQuery($query);
        Log::info('All service filters applied.');

        return $query;
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {
        switch ($key) {
            case 'search':
                $query->search($value);
                break;
            default:
                Log::warning("Unknown service filter key: $key");
                break;
        }
    }

}
