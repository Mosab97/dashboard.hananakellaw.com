<?php

namespace App\Services\Filters;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null) {
                Log::info("Applying filter for: $key with value:", ['value' => $value]);

                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping filter for: $key as the value is null.");
            }
        }

        // Log the final SQL query and its bindings.
        logQuery($query);

        Log::info('All filters applied.');
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

            case 'role_id':
                $this->filterByRoleID($query, $value);
                break;
            case 'created_at':
                $this->filterByCreatedAt($query, $value);
                break;
            case 'is_active':
                $this->filterByIsActive($query, $value);
                break;

            default:
                Log::warning("Unknown filter key: $key");
                break;
        }
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        $locales = config('app.locales'); // Fetch locales from the config file
        Log::info('Locales fetched from config:', $locales);

        // Loop through each locale to apply search on 'name' field
        foreach ($locales as $index => $locale) {
            Log::info("Applying search for 'name' in locale: $locale");

            if ($index === 0) {
                $query->whereRaw(
                    "json_extract(LOWER(name), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci",
                    ['%'.$value.'%']
                );
            } else {
                $query->orWhereRaw(
                    "json_extract(LOWER(name), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci",
                    ['%'.$value.'%']
                );
            }
        }

        $query->orwhere('email', 'like', "%$value%");
        $query->orwhere('mobile', 'like', "%$value%");
    }

    private function filterByRoleID($query, $value)
    {
        // dd($value);
        $Ids = filterArrayForNullValues($value);
        if (count($Ids) > 0) {
            $query->whereHas('roles', function ($query) use ($Ids) {
                $query->whereIn('id', $Ids);
            });
        } else {
            Log::info('No valid roles values to filter.');
        }
    }

    private function filterByCreatedAt($query, $value)
    {
        $dates = array_pad(explode(' to ', trim($value)), 2, null);
        $startDate = Carbon::parse(trim($dates[0]))->startOfDay();
        $endDate = Carbon::parse(trim($dates[1] ?? $dates[0]))->endOfDay();

        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    private function filterByIsActive($query, $value)
    {
        // dd($value);
        $query->where('active', $value);
    }
}
