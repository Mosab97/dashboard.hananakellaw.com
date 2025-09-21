<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class RestaurantFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply restaurant filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying restaurant filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping restaurant filter for: $key as the value is null or empty.");
            }
        }

        logQuery($query);
        Log::info('All restaurant filters applied.');

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
            case 'active':
                $this->filterByActive($query, $value);
                break;
            case 'delivery_available':
                $this->filterByDeliveryAvailable($query, $value);
                break;
            case 'pickup_available':
                $this->filterByPickupAvailable($query, $value);
                break;
            case 'dine_in_available':
                $this->filterByDineInAvailable($query, $value);
                break;
            case 'date_range':
                $this->filterByDateRange($query, $value, 'created_at');
                break;
            default:
                Log::warning("Unknown restaurant filter key: $key");
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
                    "LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}'))) LIKE ?",
                    ['%'.strtolower($value).'%']
                );
            }
        });
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
     * Filter by delivery availability
     */
    private function filterByDeliveryAvailable($query, $value)
    {
        if (is_array($value)) {
            if (in_array('1', $value) && ! in_array('0', $value)) {
                $query->where('delivery_available', true);
            } elseif (! in_array('1', $value) && in_array('0', $value)) {
                $query->where('delivery_available', false);
            }
        } else {
            $query->where('delivery_available', $value == '1');
        }
    }

    /**
     * Filter by pickup availability
     */
    private function filterByPickupAvailable($query, $value)
    {
        if (is_array($value)) {
            if (in_array('1', $value) && ! in_array('0', $value)) {
                $query->where('pickup_available', true);
            } elseif (! in_array('1', $value) && in_array('0', $value)) {
                $query->where('pickup_available', false);
            }
        } else {
            $query->where('pickup_available', $value == '1');
        }
    }

    /**
     * Filter by dine-in availability
     */
    private function filterByDineInAvailable($query, $value)
    {
        if (is_array($value)) {
            if (in_array('1', $value) && ! in_array('0', $value)) {
                $query->where('dine_in_available', true);
            } elseif (! in_array('1', $value) && in_array('0', $value)) {
                $query->where('dine_in_available', false);
            }
        } else {
            $query->where('dine_in_available', $value == '1');
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
