<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;

class ExpectationFilterService
{
    use HelperFilterTrait,HijriDateTrait;

    protected $allowedFields = [
        'teacher_id',
        'class_id',
        'session_id',
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
            'classroom',
            'session',
        ];

        if (in_array($key, $this->allowedFields)) {
            switch ($key) {
                case 'teacher_id':
                    $query->byTeacher($value);
                    break;

                case 'class_id':
                    $query->byClass($value);
                    break;

                case 'session_id':
                    $query->bySession($value);
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

            case 'classroom':
                $query->byClassName($value);
                break;

            case 'session':
                $query->bySessionName($value);
                break;
        }
    }
}
