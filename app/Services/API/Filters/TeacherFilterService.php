<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;
use Illuminate\Support\Facades\Log;

class TeacherFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply teacher filters with params:', is_array($params) ? $params : ['params' => $params]);

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

                    // Apply default search (search in member name, id_number, and ID)
                    $query->where(function ($q) use ($valueToSearch) {
                        $q->whereHas('member', function ($memberQuery) use ($valueToSearch) {
                            $this->searchInTranslatableFields($memberQuery, 'members.name', $valueToSearch);
                            $memberQuery->orWhere('members.email', 'LIKE', "%{$valueToSearch}%");
                            $memberQuery->orWhere('members.full_phone', 'LIKE', "%{$valueToSearch}%");
                        });
                        $q->orWhere('teacher_profiles.id_number', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere('teacher_profiles.id', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere(function ($locationQuery) use ($valueToSearch) {
                            $this->searchInTranslatableFields($locationQuery, 'teacher_profiles.location_description', $valueToSearch);
                        });
                    });

                    Log::info('Default search applied successfully');
                }
            }
        } else {
            Log::info('No search parameters found');
        }

        Log::info('All teacher filters applied successfully');

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
            'email',
            'full_phone',
            'id_number',
            'age',
            'graduation_date',
            'location_description',
            'school_id',
            'member_id',
            'lat',
            'lng',
            'created_at',
        ];

        $allowedRelations = [
            'member',
            'school',
            'classrooms',
            'grade_level',
        ];

        // Check if this is a simple field or relation search
        if (in_array($key, $allowedFields)) {
            Log::debug('Direct field search detected', ['field' => $key]);

            // Direct field search
            switch ($key) {
                case 'name':
                    $query->whereHas('member', function ($memberQuery) use ($value) {
                        $this->searchInTranslatableFields($memberQuery, 'members.name', $value);
                    });
                    break;
                case 'email':
                    $query->whereHas('member', function ($memberQuery) use ($value) {
                        $memberQuery->where('members.email', 'LIKE', "%{$value}%");
                    });
                case 'full_phone':
                    $query->whereHas('member', function ($memberQuery) use ($value) {
                        $memberQuery->where('members.full_phone', 'LIKE', "%{$value}%");
                    });
                    break;
                case 'location_description':
                    Log::debug('Applying translatable location_description filter');
                    $this->searchInTranslatableFields($query, 'teacher_profiles.location_description', $value);
                    Log::debug('Translatable location_description filter applied');
                    break;

                case 'id_number':
                    Log::debug('Applying id_number filter');
                    $query->where('teacher_profiles.id_number', 'LIKE', "%{$value}%");
                    Log::debug('ID number filter applied');
                    break;

                case 'age':
                    Log::debug('Applying age filter');
                    $query->where('teacher_profiles.age', $value);
                    Log::debug('Age filter applied');
                    break;

                case 'id':
                case 'school_id':
                case 'member_id':
                    // For IDs, check if it's a comma-separated list
                    if (strpos($value, ',') !== false) {
                        Log::debug('Multiple ID values detected');
                        $this->filterByForeignKey($query, $key, $value);
                    } else {
                        Log::debug('Single ID value detected');
                        $query->where("teacher_profiles.{$key}", $value);
                    }
                    Log::debug('ID filter applied');
                    break;

                case 'lat':
                case 'lng':
                    Log::debug("Applying coordinate filter for {$key}");
                    // For coordinates, we might want exact matches or ranges
                    if (strpos($value, ',') !== false) {
                        // Range format: min,max
                        [$min, $max] = explode(',', $value);
                        $query->whereBetween("teacher_profiles.{$key}", [$min, $max]);
                    } else {
                        $query->where("teacher_profiles.{$key}", $value);
                    }
                    Log::debug('Coordinate filter applied');
                    break;

                case 'graduation_date':
                case 'created_at':
                    Log::debug("Applying date filter for {$key}");
                    $this->filterByDateField($query, $key, $value);
                    Log::debug('Date filter applied');
                    break;

                default:
                    Log::debug('Applying default field search');
                    $query->whereHas('member', function ($memberQuery) use ($value) {
                        $this->searchInTranslatableFields($memberQuery, 'members.name', $value);
                    });
                    $query->orWhere('teacher_profiles.id_number', 'LIKE', "%{$value}%");
                    $query->orWhere('teacher_profiles.id', 'LIKE', "%{$value}%");
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
            case 'member':
                Log::debug('Searching member by name, email, or phone');
                $query->whereHas('member', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'members.name', $value);
                        $subQuery->orWhere('members.email', 'LIKE', "%{$value}%");
                        $subQuery->orWhere('members.full_phone', 'LIKE', "%{$value}%");
                    });
                });
                Log::debug('Member search applied');
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

            case 'classrooms':
                Log::debug('Searching classrooms by name');
                $query->whereHas('classrooms', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'classrooms.name', $value);
                    });
                });
                Log::debug('Classrooms search applied');
                break;
            case 'grade_level':
                Log::debug('Searching grade_level by name');
                $query->whereHas('classrooms', function ($q) use ($value) {
                    $q->whereHas('grade_level', function ($q) use ($value) {
                        $q->where(function ($subQuery) use ($value) {
                            $this->searchInTranslatableFields($subQuery, 'constants.name', $value);
                        });
                    });
                });
                Log::debug('grade_level search applied');
                break;
        }

        Log::info('Relation filter applied successfully', [
            'relation' => $relation,
        ]);
    }
}
