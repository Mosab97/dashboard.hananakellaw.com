<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;

class HolidayFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    protected $allowedFields = [
        'name',
        'date',
    ];

    public function applyFilters($query, $params)
    {

        foreach ($this->allowedFields as $field) {
            if (isset($params[$field])) {
                $this->applyDynamicFilter($query, $field, $params[$field]);
            }
        }

        if (isset($params['search_key']) && isset($params['search_value'])) {
            $this->applyDynamicFilter($query, $params['search_key'], $params['search_value']);
        }

        return $query;
    }

    protected function applyDynamicFilter($query, $key, $value)
    {

        $allowedRelations = [
            'creator',
        ];

        if (in_array($key, $this->allowedFields)) {
            switch ($key) {
                case 'name':
                    $query->byName($value);
                    break;
                case 'date':
                    $this->filterByDateField($query, 'date', $value);
                    break;
            }
        } elseif (in_array($key, $allowedRelations)) {
            $this->filterByRelation($query, $key, $value);
        }
    }

    private function filterByRelation($query, $relation, $value)
    {

        switch ($relation) {
            case 'creator':
                $query->byCreatorName($value);
                break;
        }
    }
}
