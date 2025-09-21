<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;

class CallRequestFilterService
{
    use HelperFilterTrait,HijriDateTrait;

    protected $allowedFields = [
        'student_id',
        'type_id',
        'date',
        'class_id',
        'teacher_id',

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

        if ($params['date'] ?? null) {
            $query->byDateRange();
        }

        return $query;
    }

    protected function applyDynamicFilter($query, $key, $value)
    {

        $allowedRelations = [
            'student',
            'type',
            'teachers',
            'classroom',
        ];

        if (in_array($key, $this->allowedFields)) {
            switch ($key) {
                case 'student_id':
                    $query->byStudent($value);
                    break;

                case 'type_id':
                    $query->byType($value);
                    break;

                case 'date':
                    $this->filterByDateField($query, 'created_at', $value);
                    break;

                case 'class_id':
                    $query->byClassroom($value);
                    break;
                case 'teacher_id':
                    $query->byTeacher($value);
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
                $query->byStudentName($value);
                break;

            case 'type':
                $query->byTypeName($value);
                break;

            case 'teachers':
                $query->byTeacherName($value);
                break;

            case 'classroom':
                $query->byClassroomName($value);
                break;
        }
    }
}
