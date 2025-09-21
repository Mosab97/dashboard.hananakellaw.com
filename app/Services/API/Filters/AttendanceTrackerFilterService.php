<?php

namespace App\Services\API\Filters;

use App\Enums\AttendableType;
use App\Exceptions\CustomBusinessException;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;
use Illuminate\Support\Facades\Log;

class AttendanceTrackerFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply attendance filters with params:', is_array($params) ? $params : ['params' => $params]);

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

                    // Apply default search (search in ID, reason, or related entities)
                    $query->where(function ($q) use ($valueToSearch) {
                        $q->where('attendance_trackers.id', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere(function ($reasonQuery) use ($valueToSearch) {
                            $this->searchInTranslatableFields($reasonQuery, 'attendance_trackers.reason', $valueToSearch);
                        });
                        // Search in attendable (student or teacher)
                        $q->orWhere(function ($attendableQuery) use ($valueToSearch) {
                            // Search in student's name (through attendable relation)
                            $attendableQuery->whereHasMorph('attendable', [Student::class], function ($query) use ($valueToSearch) {
                                $this->searchInTranslatableFields($query, 'name', $valueToSearch);
                            });

                            // Search in teacher's name (through member relation in TeacherProfile)
                            $attendableQuery->orWhereHasMorph('attendable', [TeacherProfile::class], function ($query) use ($valueToSearch) {
                                $query->whereHas('member', function ($query) use ($valueToSearch) {
                                    $this->searchInTranslatableFields($query, 'name', $valueToSearch);
                                });
                            });
                        });
                    });

                    Log::info('Default search applied successfully');
                }
            }
        } else {
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

        // Log the final SQL query and its bindings.
        if (function_exists('logQuery')) {
            logQuery($query);
        }

        Log::info('All attendance filters applied successfully');

        return $query;
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {
        Log::debug("AttendanceFilterService: Beginning to apply filter [$key]", ['value' => $value]);

        switch ($key) {
            case 'attendable_id':
            case 'id':
            case 'classroom_id':
            case 'status_id':
            case 'school_id':
            case 'created_by':
                // Use the common function for foreign keys and IDs
                $this->filterByForeignKey($query, $key, $value);
                break;
            case 'attendable_type':
                $this->filterByAttendableType($query, $value);
                break;
            default:
                Log::warning("Unknown filter key for attendance: $key", ['value' => $value]);
                break;
        }

        Log::debug("AttendanceFilterService: Completed applying filter [$key]");
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
            'attendable_id',
            'attendable_type',
            'status_id',
            'classroom_id',
            'attendance_date_time',
            'tardiness_time',
            'reason',
            'school_id',
            'created_by',
            'created_at',
        ];

        $allowedRelations = [
            'attendable',
            'classroom',
            'status',
            'school',
            'creator',
        ];

        // Check if this is a simple field or relation search
        if (in_array($key, $allowedFields)) {
            Log::debug('Direct field search detected', ['field' => $key]);

            // Direct field search
            switch ($key) {
                case 'reason':
                    Log::debug('Applying translatable reason filter');
                    $this->searchInTranslatableFields($query, 'attendance_trackers.reason', $value);
                    Log::debug('Translatable reason filter applied');
                    break;

                case 'attendable_id':
                case 'id':
                case 'classroom_id':
                case 'status_id':
                case 'school_id':
                case 'created_by':
                    // For IDs, check if it's a comma-separated list
                    if (strpos($value, ',') !== false) {
                        Log::debug('Multiple ID values detected');
                        $this->filterByForeignKey($query, $key, $value);
                    } else {
                        Log::debug('Single ID value detected');
                        $query->where("attendance_trackers.{$key}", $value);
                    }
                    Log::debug('ID filter applied');
                    break;

                case 'attendable_type':
                    Log::debug('Applying attendable_type filter');
                    $this->filterByAttendableType($query, $value);
                    Log::debug('Attendable type filter applied');
                    break;

                case 'attendance_date_time':
                case 'created_at':
                    Log::debug("Applying date filter for {$key}");
                    $this->filterByDateField($query, $key, $value);
                    Log::debug('Date filter applied');
                    break;

                case 'tardiness_time':
                    Log::debug('Applying tardiness_time filter');
                    $this->filterByTardinessTime($query, $value);
                    Log::debug('Tardiness time filter applied');
                    break;

                default:
                    Log::debug('Applying default field search');
                    $query->where('attendance_trackers.id', 'LIKE', "%{$value}%");
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
            case 'attendable':
                Log::debug('Searching attendable entities');
                // Otherwise search by name or ID in both types
                $query->where(function ($q) use ($value) {
                    // Search in attendable (student or teacher)
                    $q->orWhere(function ($attendableQuery) use ($value) {
                        // Search in student's name (through attendable relation)
                        $attendableQuery->whereHasMorph('attendable', [Student::class], function ($query) use ($value) {
                            $this->searchInTranslatableFields($query, 'name', $value);
                        });

                        // Search in teacher's name (through member relation in TeacherProfile)
                        $attendableQuery->orWhereHasMorph('attendable', [TeacherProfile::class], function ($query) use ($value) {
                            $query->whereHas('member', function ($query) use ($value) {
                                $this->searchInTranslatableFields($query, 'name', $value);
                            });
                        });
                    });
                });
                Log::debug('Attendable search applied');
                break;

            case 'classroom':
                Log::debug('Searching classroom by name');
                $query->whereHas('classroom', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'classrooms.name', $value);
                });
                Log::debug('Classroom search applied');
                break;

            case 'status':
                Log::debug('Searching status by name');
                $query->whereHas('status', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'constants.name', $value);
                });
                Log::debug('Status search applied');
                break;

            case 'school':
                Log::debug('Searching school by name');
                $query->whereHas('school', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'school_profiles.name', $value);
                });
                Log::debug('School search applied');
                break;

            case 'creator':
                Log::debug('Searching creator by name');
                $query->whereHas('creator', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'members.name', $value);
                });
                Log::debug('Creator search applied');
                break;
        }

        Log::info('Relation filter applied successfully', [
            'relation' => $relation,
        ]);
    }

    /**
     * Filter by attendable type
     */
    private function filterByAttendableType($query, $value)
    {
        // dd($value);
        Log::debug("Beginning attendable type filter with value: $value");

        switch (strtolower($value)) {
            case 'teacher':
            case AttendableType::TEACHER->value:
                $query->where('attendable_type', TeacherProfile::class);
                Log::info('Filtering for teacher profiles');
                break;

            case 'student':
            case AttendableType::STUDENT->value:
                $query->where('attendable_type', Student::class);
                Log::info('Filtering for student profiles');
                break;

            default:
                Log::error("Unsupported attendable type received: $value");
                throw new CustomBusinessException(
                    message: 'School Member Type is not supported.',
                    code: 422,
                    data: [
                        'attendable_type' => $value,
                        'supported_types' => ['student', 'teacher'],
                    ]
                );
        }
    }

    /**
     * Filter by tardiness time
     */
    private function filterByTardinessTime($query, $value)
    {
        if (strpos($value, ' to ') !== false) {
            [$startTime, $endTime] = explode(' to ', trim($value));
            $startTime = trim($startTime);
            $endTime = trim($endTime);

            $query->whereTime('tardiness_time', '>=', $startTime)
                ->whereTime('tardiness_time', '<=', $endTime);
            Log::info("Filtering by tardiness_time range: $startTime to $endTime");
        } elseif (is_numeric($value)) {
            // Convert minutes to time format
            $hours = floor($value / 60);
            $mins = $value % 60;
            $timeStr = sprintf('%02d:%02d', $hours, $mins);

            $query->whereTime('tardiness_time', '=', $timeStr);
            Log::info("Filtering by tardiness_time: $timeStr (from $value minutes)");
        } else {
            $query->whereTime('tardiness_time', '=', $value);
            Log::info("Filtering by tardiness_time: $value");
        }
    }
}
