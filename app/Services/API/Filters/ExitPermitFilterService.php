<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;

class ExitPermitFilterService
{
    use HelperFilterTrait,HijriDateTrait;

    protected $allowedFields = [
        'student_id',
        'going_to_id',
        'date',
        'hours',
        'active',
    ];

    public function applyFilters($query, $params)
    {

        foreach ($this->allowedFields as $field) {
            if (isset($params[$field])) {
                $this->applyDynamicFilter($query, $field, $params[$field]);
            }
        }
        // Special handling for search_key and search_value pair
        if (isset($params['search_key']) || isset($params['search_value'])) {
            $searchKey = $params['search_key'] ?? null;
            $searchValue = $params['search_value'] ?? null;

            if ($searchKey !== null && $searchValue !== null) {

                $this->applyDynamicFilter($query, $searchKey, $searchValue);
            }
        }

        return $query;
    }

    protected function applyDynamicFilter($query, $key, $value)
    {

        $allowedRelations = [
            'student',
            'goingTo',
            'classroom',
        ];

        if (in_array($key, $this->allowedFields)) {

            switch ($key) {
                case 'student_id':
                    $this->filterByForeignKey($query, 'student_id', $value);
                    break;

                case 'going_to_id':
                    $this->filterByForeignKey($query, 'going_to_id', $value);
                    break;

                case 'date':
                    $this->filterByDateField($query, 'created_at', $value);
                    break;

                case 'active':
                    $query->where('active', filter_var($value, FILTER_VALIDATE_BOOLEAN));
                    break;

                case 'hours':
                    $query->where('hours', 'LIKE', "%{$value}%");
                    break;
            }
        } elseif (in_array($key, $allowedRelations)) {
            $this->filterByRelation($query, $key, $value);
        }

    }

    private function filterByRelation($query, $relation, $value)
    {

        switch ($relation) {
            case 'student':
                $query->whereHas('student', function ($q) use ($value) {
                    $q->where('name', 'LIKE', "%{$value}%")
                        ->orWhere('id', $value);
                });
                break;

            case 'goingTo':
                $query->bygoingToName($value);
                break;

            case 'classroom':
                $query->byClassroomName($value);
                break;
        }
    }
}
