<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class WhyChooseUsFilterService
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
            case 'product_id':
                $this->filterByProduct($query, $value);
                break;
            case 'active':
                $this->filterByActive($query, $value);
                break;
            case 'price_range':
                $this->filterByPriceRange($query, $value);
                break;
            case 'date_range':
                $this->filterByDateRange($query, $value, 'created_at');
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
                    ['%'.strtolower($value).'%']
                );
            }
        });
    }

    /**
     * Filter by product
     */
    private function filterByProduct($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('product_id', $value);
        } else {
            $query->where('product_id', $value);
        }
    }

    /**
     * Filter by active status
     */
    private function filterByActive($query, $value)
    {
        if (is_array($value)) {
            if (in_array('1', $value) && ! in_array('0', $value)) {
                $query->where('active', true);
            } elseif (! in_array('1', $value) && in_array('0', $value)) {
                $query->where('active', false);
            }
            // If both or none selected, no filtering needed
        } else {
            $query->where('active', $value == '1');
        }
    }

    /**
     * Filter by price range
     */
    private function filterByPriceRange($query, $range)
    {
        if (is_array($range)) {
            if (! empty($range['min'])) {
                $query->where('price', '>=', $range['min']);
            }
            if (! empty($range['max'])) {
                $query->where('price', '<=', $range['max']);
            }
        }
    }

    /**
     * Filter by date range
     */
    private function filterByDateRange($query, $range, $field = 'created_at')
    {
        if (is_string($range) && strpos($range, ' to ') !== false) {
            [$from, $to] = explode(' to ', $range);

            if (! empty($from)) {
                $query->whereDate($field, '>=', trim($from));
            }
            if (! empty($to)) {
                $query->whereDate($field, '<=', trim($to));
            }
        } elseif (is_array($range)) {
            if (! empty($range['from'])) {
                $query->whereDate($field, '>=', $range['from']);
            }
            if (! empty($range['to'])) {
                $query->whereDate($field, '<=', $range['to']);
            }
        }
    }
}
