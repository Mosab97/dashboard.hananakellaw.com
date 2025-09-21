<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;

class ShiftFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    protected $allowedFields = [
        'type_id',
        'teacher_id',
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
            'teacher',
            'type',
        ];

        if (in_array($key, $this->allowedFields)) {
            switch ($key) {
                case 'type_id':
                    $query->byType($value);
                    break;

                case 'teacher_id':
                    $query->byTeacher($value);
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
            case 'teacher':
                $query->byTeacherName($value);
                break;

            case 'type':
                $query->byTypeName($value);
                break;
        }
    }
}
