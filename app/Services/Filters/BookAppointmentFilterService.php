<?php

namespace App\Services\Filters;

use Illuminate\Support\Facades\Log;

class BookAppointmentFilterService
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply book appointment filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying book appointment filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping book appointment filter for: $key as the value is null or empty.");
            }
        }

        logQuery($query);
        Log::info('All book appointment filters applied.');

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

            case 'appointment_type_id':
                $this->filterByAppointmentType($query, $value);
                break;

            case 'city':
                $this->filterByCity($query, $value);
                break;

            case 'date_from':
                $this->filterByDateFrom($query, $value);
                break;

            case 'date_to':
                $this->filterByDateTo($query, $value);
                break;

            default:
                Log::warning("Unknown book appointment filter key: $key");
                break;
        }
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        $query->where(function ($query) use ($value) {
            $query->where('name', 'like', "%$value%")
                ->orWhere('phone', 'like', "%$value%")
                ->orWhere('city', 'like', "%$value%");
        });
    }

    /**
     * Filters the query by appointment type.
     */
    private function filterByAppointmentType($query, $value)
    {
        $query->where('appointment_type_id', $value);
    }

    /**
     * Filters the query by city.
     */
    private function filterByCity($query, $value)
    {
        $query->where('city', 'like', "%$value%");
    }

    /**
     * Filters the query by date from.
     */
    private function filterByDateFrom($query, $value)
    {
        $query->whereDate('date', '>=', $value);
    }

    /**
     * Filters the query by date to.
     */
    private function filterByDateTo($query, $value)
    {
        $query->whereDate('date', '<=', $value);
    }


}
