<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class ArticleContentFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply slider filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying slider filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping slider filter for: $key as the value is null or empty.");
            }
        }

        logQuery($query);
        Log::info('All slider filters applied.');

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

            default:
                Log::warning("Unknown slider filter key: $key");
                break;
        }
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        $query->where(function ($query) use ($value) {
            // Search in translatable name field
            $locales = config('app.locales', ['en', 'ar']);
            foreach ($locales as $locale) {
                $query->orWhereRaw(
                    "LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?",
                    ['%' . strtolower($value) . '%']
                );
            }
        });
    }
}
