<?php

namespace App\Services\API\Filters;

use App\Exceptions\CustomBusinessException;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply report filters with params:', is_array($params) ? $params : ['params' => $params]);

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

                    // Apply default search (search in ID, subject, reason, or related entities)
                    $query->where(function ($q) use ($valueToSearch) {
                        $q->where('reports.id', 'LIKE', "%{$valueToSearch}%");
                        $q->orWhere(function ($subjectQuery) use ($valueToSearch) {
                            $this->searchInTranslatableFields($subjectQuery, 'reports.subject', $valueToSearch);
                        });
                        $q->orWhere(function ($reasonQuery) use ($valueToSearch) {
                            $this->searchInTranslatableFields($reasonQuery, 'reports.reason', $valueToSearch);
                        });
                        // Search in reportable (student or teacher)
                        $q->orWhere(function ($reportableQuery) use ($valueToSearch) {
                            // Search in student's name (through reportable relation)
                            $reportableQuery->whereHasMorph('reportable', [Student::class], function ($query) use ($valueToSearch) {
                                $this->searchInTranslatableFields($query, 'name', $valueToSearch);
                            });

                            // Search in teacher's name (through member relation in TeacherProfile)
                            $reportableQuery->orWhereHasMorph('reportable', [TeacherProfile::class], function ($query) use ($valueToSearch) {
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

        Log::info('All report filters applied successfully');

        return $query;
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {
        Log::debug("ReportFilterService: Beginning to apply filter [$key]", ['value' => $value]);

        switch ($key) {
            case 'reportable_id':
            case 'id':
            case 'classroom_id':
            case 'status_id':
            case 'module_type_id':
            case 'school_id':
            case 'created_by':
                // Use the common function for foreign keys and IDs
                $this->filterByForeignKey($query, $key, $value);
                break;
            case 'reportable_type':
                $this->filterByReportableType($query, $value);
                break;
            case 'rating':
                $query->where('rating', $value);
                break;
            case 'report_date_time':
                $this->filterByDateField($query, $key, $value);
                break;
            default:
                Log::warning("Unknown filter key for report: $key", ['value' => $value]);
                break;
        }

        Log::debug("ReportFilterService: Completed applying filter [$key]");
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
            'reportable_id',
            'reportable_type',
            'subject',
            'status_id',
            'module_type_id',
            'classroom_id',
            'report_date_time',
            'rating',
            'reason',
            'school_id',
            'created_by',
            'created_at',
        ];

        $allowedRelations = [
            'reportable',
            'classroom',
            'status',
            'moduleType',
            'school',
            'creator',
        ];

        // Check if this is a simple field or relation search
        if (in_array($key, $allowedFields)) {
            Log::debug('Direct field search detected', ['field' => $key]);

            // Direct field search
            switch ($key) {
                case 'subject':
                case 'reason':
                    Log::debug("Applying translatable {$key} filter");
                    $this->searchInTranslatableFields($query, "reports.{$key}", $value);
                    Log::debug("Translatable {$key} filter applied");
                    break;

                case 'reportable_id':
                case 'id':
                case 'classroom_id':
                case 'status_id':
                case 'module_type_id':
                case 'school_id':
                case 'created_by':
                    // For IDs, check if it's a comma-separated list
                    if (strpos($value, ',') !== false) {
                        Log::debug('Multiple ID values detected');
                        $this->filterByForeignKey($query, $key, $value);
                    } else {
                        Log::debug('Single ID value detected');
                        $query->where("reports.{$key}", $value);
                    }
                    Log::debug('ID filter applied');
                    break;

                case 'reportable_type':
                    Log::debug('Applying reportable_type filter');
                    $this->filterByReportableType($query, $value);
                    Log::debug('Reportable type filter applied');
                    break;

                case 'rating':
                    Log::debug('Applying rating filter');
                    if (strpos($value, '-') !== false) {
                        [$min, $max] = explode('-', $value);
                        $query->whereBetween('rating', [trim($min), trim($max)]);
                        Log::debug('Rating range filter applied');
                    } else {
                        $query->where('rating', $value);
                        Log::debug('Exact rating filter applied');
                    }
                    break;

                case 'report_date_time':
                case 'created_at':
                    Log::debug("Applying date filter for {$key}");
                    $this->filterByDateField($query, $key, $value);
                    Log::debug('Date filter applied');
                    break;

                default:
                    Log::debug('Applying default field search');
                    $query->where("reports.{$key}", 'LIKE', "%{$value}%");
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
            case 'reportable':
                Log::debug('Searching reportable entities');
                // Search by name or ID in both types
                $query->where(function ($q) use ($value) {
                    // Search in reportable (student or teacher)
                    $q->orWhere(function ($reportableQuery) use ($value) {
                        // Search in student's name (through reportable relation)
                        $reportableQuery->whereHasMorph('reportable', [Student::class], function ($query) use ($value) {
                            $this->searchInTranslatableFields($query, 'name', $value);
                        });

                        // Search in teacher's name (through member relation in TeacherProfile)
                        $reportableQuery->orWhereHasMorph('reportable', [TeacherProfile::class], function ($query) use ($value) {
                            $query->whereHas('member', function ($query) use ($value) {
                                $this->searchInTranslatableFields($query, 'name', $value);
                            });
                        });
                    });
                });
                Log::debug('Reportable search applied');
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

            case 'moduleType':
                Log::debug('Searching module type by name');
                $query->whereHas('moduleType', function ($q) use ($value) {
                    $this->searchInTranslatableFields($q, 'constants.name', $value);
                });
                Log::debug('Module type search applied');
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
     * Filter by reportable type
     */
    private function filterByReportableType($query, $value)
    {
        Log::debug("Beginning reportable type filter with value: $value");

        switch (strtolower($value)) {
            case 'teacher':
            case 'teacherprofile':
                $query->where('reportable_type', TeacherProfile::class);
                Log::info('Filtering for teacher profiles');
                break;

            case 'student':
                $query->where('reportable_type', Student::class);
                Log::info('Filtering for student profiles');
                break;

            default:
                Log::error("Unsupported reportable type received: $value");
                throw new CustomBusinessException(
                    message: 'Report Member Type is not supported.',
                    code: 422,
                    data: [
                        'reportable_type' => $value,
                        'supported_types' => ['student', 'teacher'],
                    ]
                );
        }
    }

    /**
     * Filter by date field with support for various formats
     */
    private function filterByDateField($query, $field, $value)
    {
        // Handle date ranges with "to" separator
        if (strpos($value, ' to ') !== false) {
            [$startDate, $endDate] = explode(' to ', $value);
            $startDate = trim($startDate);
            $endDate = trim($endDate);

            try {
                $startCarbon = new Carbon($startDate);
                $endCarbon = new Carbon($endDate);

                // Add a day to the end date to make it inclusive
                $endCarbon->addDay();

                $query->whereBetween($field, [$startCarbon->startOfDay(), $endCarbon->startOfDay()]);
                Log::info("Filtering by date range: $startDate to $endDate");
            } catch (\Exception $e) {
                Log::error('Invalid date format for filtering', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        // Check if it's a single date
        else {
            try {
                $dateCarbon = new Carbon($value);
                $query->whereDate($field, $dateCarbon->format('Y-m-d'));
                Log::info("Filtering by exact date: {$dateCarbon->format('Y-m-d')}");
            } catch (\Exception $e) {
                Log::error('Invalid date format for filtering', [
                    'date' => $value,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
