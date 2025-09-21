<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;
use Illuminate\Support\Facades\Log;

class ClassroomFilterService
{
    use HelperFilterTrait,HijriDateTrait;

    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply classroom filters with params:', is_array($params) ? $params : ['params' => $params]);

        // Special handling for search_key and search_value pair
        if (isset($params['search_key']) || isset($params['search_value'])) {
            $searchKey = $params['search_key'] ?? null;
            $searchValue = $params['search_value'] ?? null;

            if ($searchKey !== null && $searchValue !== null) {
                // Both parameters are present
                Log::info('Applying dynamic search', [
                    'key' => $searchKey,
                    'value' => $searchValue,
                    'query_type' => 'dynamic_search',
                ]);

                $this->applyDynamicFilter($query, $searchKey, $searchValue);
                Log::info('Dynamic search filter applied successfully');
            } else {
                // One parameter is missing - apply default search
                $valueToSearch = $searchValue ?? $searchKey;

                if ($valueToSearch !== null) {
                    Log::info('Applying default search with partial parameters', [
                        'search_key_present' => isset($params['search_key']),
                        'search_value_present' => isset($params['search_value']),
                        'value_used' => $valueToSearch,
                        'query_type' => 'default_search',
                    ]);

                    // Apply default search (search in name and ID)
                    $query->where(function ($q) use ($valueToSearch) {
                        $this->searchInTranslatableFields($q, 'classrooms.name', $valueToSearch);
                        $q->orWhere('classrooms.id', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere('classrooms.class', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere('classrooms.class_number', 'LIKE', "%{$valueToSearch}%");
                    });

                    Log::info('Default search applied successfully');
                }
            }
        } else {
            Log::info('No search parameters found');
        }

        Log::info('All classroom filters applied successfully');

        return $query;
    }

    /**
     * Applies a dynamic filter based on search_key and search_value
     */
    protected function applyDynamicFilter($query, $key, $value)
    {
        Log::debug('Processing dynamic filter', [
            'key' => $key,
            'value' => $value,
            'method' => 'applyDynamicFilter',
        ]);

        // Whitelist of allowed fields and relations for security
        $allowedFields = [
            'id',
            'name',
            'created_at',
            'class',
            'class_number',
        ];

        $allowedRelations = [
            'grade_level',
            'school',
            'teachers',
            'students',
        ];

        // Check if this is a simple field or relation search
        if (in_array($key, $allowedFields)) {
            Log::debug('Direct field search detected', ['field' => $key]);

            // Direct field search
            switch ($key) {
                case 'name':
                    Log::debug('Applying translatable name filter');
                    $this->searchInTranslatableFields($query, 'classrooms.name', $value);
                    Log::debug('Translatable name filter applied');
                    break;

                case 'id':
                    // For IDs, check if it's a comma-separated list
                    if (strpos($value, ',') !== false) {
                        Log::debug('Multiple ID values detected');
                        $this->filterByForeignKey($query, $key, $value);
                    } else {
                        Log::debug('Single ID value detected');
                        $query->where("classrooms.$key", $value);
                    }
                    Log::debug('ID filter applied');
                    break;

                case 'created_at':
                    Log::debug('Applying date filter for created_at');
                    $this->filterByDateField($query, $key, $value);
                    Log::debug('Date filter applied');
                    break;

                case 'class':
                case 'class_number':
                    Log::debug("Applying LIKE filter for $key");
                    $query->where("classrooms.$key", 'LIKE', "%{$value}%");
                    Log::debug('LIKE filter applied');
                    break;

                default:
                    Log::debug('Applying default field search');
                    $this->searchInTranslatableFields($query, 'classrooms.name', $value);
                    $query->orWhere('classrooms.id', $value);
                    Log::debug('Default field search applied');
                    break;
            }
        } elseif (in_array($key, $allowedRelations)) {
            Log::debug('Relation search detected', ['relation' => $key]);

            // Relation search - handle each relation specially
            $this->filterByRelationName($query, $key, $value);
            Log::debug('Relation search filter applied');
        } else {
            Log::warning('Unauthorized field or relation attempted', [
                'key' => $key,
                'allowed_fields' => $allowedFields,
                'allowed_relations' => $allowedRelations,
            ]);

            return;
        }

        Log::debug('Dynamic filter processing completed', [
            'key' => $key,
        ]);
    }

    /**
     * Filter by the name of a related entity
     */
    private function filterByRelationName($query, $relation, $value)
    {
        Log::debug('Filtering by relation name', [
            'relation' => $relation,
            'value' => $value,
            'method' => 'filterByRelationName',
        ]);

        switch ($relation) {
            case 'grade_level':
                Log::debug('Searching grade level by name');
                $query->whereHas('grade_level', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'constants.name', $value);
                    });
                });
                Log::debug('Grade level search applied');
                break;

            case 'school':
                Log::debug('Searching school by name');
                $query->whereHas('school', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'school_profiles.name', $value);
                    });
                });
                Log::debug('School search applied');
                break;

            case 'teachers':
                Log::debug('Searching teachers by name');
                $query->whereHas('teachers', function ($q) use ($value) {
                    $q->whereHas('member', function ($memberQuery) use ($value) {
                        $this->searchInTranslatableFields($memberQuery, 'members.name', $value);
                    });
                });
                Log::debug('Teachers search applied');
                break;

            case 'students':
                Log::debug('Searching students by name');
                $query->whereHas('students', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'students.name', $value);
                });
                Log::debug('Students search applied');
                break;
        }

        Log::info('Relation filter applied successfully', [
            'relation' => $relation,
        ]);
    }
}
