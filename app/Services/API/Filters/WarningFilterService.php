<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;

class WarningFilterService
{
    use HelperFilterTrait,HijriDateTrait;

    protected $allowedFields = [
        'teacher_id',
        'title_id',
        'date',
        'is_seen',
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
            'title',
            'description',
        ];

        if (in_array($key, $this->allowedFields)) {
            switch ($key) {
                case 'teacher_id':
                    $query->byTeacher($value);
                    break;

                case 'title_id':
                    $query->byTitle($value);
                    break;

                case 'date':
                    $this->filterByDateField($query, 'date', $value);
                    break;

                case 'is_seen':
                    $query->where('is_seen', filter_var($value, FILTER_VALIDATE_BOOLEAN));
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

            case 'title':
                $query->byTitleName($value);
                break;

            case 'description':
                $query->where('description', 'LIKE', "%{$value}%")
                    ->orWhere('custom_title', 'LIKE', "%{$value}%");
                break;
        }
    }
}
