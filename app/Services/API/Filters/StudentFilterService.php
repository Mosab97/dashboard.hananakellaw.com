<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;
use Illuminate\Support\Facades\Log;

class StudentFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply student filters with params:', is_array($params) ? $params : ['params' => $params]);

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

                    // Apply default search (search in name, email, id_number, and ID)
                    $query->where(function ($q) use ($valueToSearch) {
                        $this->searchInTranslatableFields($q, 'students.name', $valueToSearch);
                        $q->orWhere('students.email', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere('students.id_number', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere('students.id', 'LIKE', "%{$valueToSearch}%");
                    });

                    Log::info('Default search applied successfully');
                }
            }
        } else {
            Log::info('No search parameters found');
        }

        // Apply filters for each parameter
        foreach ($params as $key => $value) {
            if ($value !== null && $key !== 'per_page' && $key !== 'page') {
                Log::info("Applying filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping filter for: $key as the value is null or pagination parameter.");
            }
        }

        Log::info('All student filters applied successfully');

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
            'microsoft_email',
            'id_number',
            'phone_number',
            'mother_contact_number',
            'madrasati_account_number',
            'home_phone',
            'relative_contact_number',
            'school_id',
            'teacher_id',
            'guardian_id',
            'class_id',
            'grade_level_id',
            'transportation_method_id',
            'nationality_id',
            'member_id',
            'source_id',
            'registration_status',
            'date_of_birth',
            'residence_permit_date',
            'residence_permit_expiry_date',
            'created_at',
        ];

        $allowedRelations = [
            'school',
            'teacher',
            'guardian',
            'member',
            'nationality',
            'grade_level',
            'transportation_method',
            'classroom',
            'source',
        ];

        // Check if this is a simple field or relation search
        if (in_array($key, $allowedFields)) {
            Log::debug('Direct field search detected', ['field' => $key]);

            // Direct field search
            switch ($key) {
                case 'name':
                    Log::debug('Applying translatable name filter');
                    $this->searchInTranslatableFields($query, 'students.name', $value);
                    Log::debug('Translatable name filter applied');
                    break;

                case 'email':
                case 'microsoft_email':
                case 'id_number':
                case 'phone_number':
                case 'mother_contact_number':
                case 'madrasati_account_number':
                case 'home_phone':
                case 'relative_contact_number':
                case 'relative_name':
                    Log::debug("Applying text field filter for {$key}");
                    $query->where("students.{$key}", 'LIKE', "%{$value}%");
                    Log::debug('Text field filter applied');
                    break;

                case 'id':
                case 'school_id':
                case 'teacher_id':
                case 'guardian_id':
                case 'class_id':
                case 'grade_level_id':
                case 'transportation_method_id':
                case 'nationality_id':
                case 'member_id':
                case 'source_id':
                    // For IDs, check if it's a comma-separated list
                    if (strpos($value, ',') !== false) {
                        Log::debug('Multiple ID values detected');
                        $this->filterByForeignKey($query, $key, $value);
                    } else {
                        Log::debug('Single ID value detected');
                        $query->where("students.{$key}", $value);
                    }
                    Log::debug('ID filter applied');
                    break;

                case 'registration_status':
                    Log::debug('Applying registration status filter');
                    $query->where('students.registration_status', $value);
                    Log::debug('Registration status filter applied');
                    break;

                case 'date_of_birth':
                case 'residence_permit_date':
                case 'residence_permit_expiry_date':
                case 'created_at':
                    Log::debug("Applying date filter for {$key}");
                    $this->filterByDateField($query, $key, $value);
                    Log::debug('Date filter applied');
                    break;

                default:
                    Log::debug('Applying default field search');
                    $this->searchInTranslatableFields($query, 'students.name', $value);
                    $query->orWhere('students.email', 'LIKE', "%{$value}%");
                    $query->orWhere('students.id_number', 'LIKE', "%{$value}%");
                    $query->orWhere('students.id', 'LIKE', "%{$value}%");
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
            case 'school':
                Log::debug('Searching school by name');
                $query->whereHas('school', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'school_profiles.name', $value);
                    });
                });
                Log::debug('School search applied');
                break;

            case 'teacher':
                Log::debug('Searching teacher');
                $query->whereHas('teacher', function ($q) use ($value) {
                    $q->whereHas('member', function ($memberQuery) use ($value) {
                        $this->searchInTranslatableFields($memberQuery, 'members.name', $value);
                    });
                });
                Log::debug('Teacher search applied');
                break;

            case 'guardian':
                Log::debug('Searching guardian');
                $query->whereHas('guardian', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'guardians.name', $value);
                });
                Log::debug('Guardian search applied');
                break;

            case 'member':
                Log::debug('Searching member');
                $query->whereHas('member', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'members.name', $value);
                });
                Log::debug('Member search applied');
                break;

            case 'nationality':
            case 'grade_level':
            case 'transportation_method':
            case 'source':
                Log::debug("Searching {$relation} by name");
                $query->whereHas($relation, function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'constants.name', $value);
                    });
                });
                Log::debug("{$relation} search applied");
                break;

            case 'classroom':
                Log::debug('Searching classroom by name');
                $query->whereHas('classroom', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'classrooms.name', $value);
                    });
                });
                Log::debug('Classroom search applied');
                break;
        }

        Log::info('Relation filter applied successfully', [
            'relation' => $relation,
        ]);
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {

        switch ($key) {
            case 'id':
            case 'class_id':
            case 'school_id':
                $this->filterByForeignKey($query, $key, $value);
                break;
            default:
                Log::warning("Unknown filter key for attendance: $key", ['value' => $value]);
                break;
        }
    }
}
