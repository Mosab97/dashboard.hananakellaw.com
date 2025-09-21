<?php

namespace App\Services\API\Filters;

use App\Enums\AttendableType;
use App\Exceptions\CustomBusinessException;
use App\Models\Student;
use App\Models\TeacherProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceTrackerFilterService1ss
{
    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply attendance tracker filters with params:', $params);

        foreach ($params as $key => $value) {
            if ($value !== null && $key !== 'per_page' && $key !== 'page') {
                Log::info("Applying filter for: $key with value:", ['value' => $value]);
                $this->applyFilter($query, $key, $value);
            } else {
                Log::info("Skipping filter for: $key as the value is null or pagination/type parameter.");
            }
        }

        // Log the final SQL query and its bindings.
        logQuery($query);

        Log::info('All attendance tracker filters applied.');

        return $query;
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {
        Log::debug("AttendanceTrackerFilterService: Beginning to apply filter [$key]", ['value' => $value]);

        switch ($key) {
            case 'search':
                Log::info("Applying search filter with value: $value");
                $this->filterBySearch($query, $value);
                break;
            case 'status_id':
                Log::info("Applying status_id filter with value: $value");
                $this->filterByStatusId($query, $value);
                break;
            case 'attendable_type':
                Log::info("Applying attendable type filter with value: $value");
                $this->filterByAttendableType($query, $value);
                break;
            case 'attendable_id':
                Log::info('Applying attendable_id filter with value:', ['attendable_id' => $value]);
                $this->filterByAttendableID($query, $value);
                break;
            case 'classroom_id':
                Log::info('Applying classroom_id filter with value:', ['classroom_id' => $value]);
                $this->filterByClassroomId($query, $value);
                break;
            case 'date_range':
                Log::info('Applying date_range filter with value:', ['date_range' => $value]);
                $this->filterByDateRange($query, $value);
                break;
            case 'attendance_date':
                Log::info('Applying attendance_date filter with value:', ['attendance_date' => $value]);
                $this->filterByAttendanceDate($query, $value);
                break;
            case 'school_id':
                Log::info('Applying school_id filter with value:', ['school_id' => $value]);
                $this->filterBySchoolId($query, $value);
                break;
            case 'created_by':
                Log::info('Applying created_by filter with value:', ['created_by' => $value]);
                $this->filterByCreatedBy($query, $value);
                break;
            default:
                Log::warning("Unknown filter key for attendance tracker: $key", ['value' => $value]);
                break;
        }

        Log::debug("AttendanceTrackerFilterService: Completed applying filter [$key]");
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        Log::debug("Beginning search filter with term: $value");

        $query->where(function ($query) use ($value) {
            Log::debug('Building search query for multiple fields');

            // Search in student's name (through attendable relation)
            Log::debug('Adding student name search condition');
            $query->whereHasMorph('attendable', [Student::class], function ($query) use ($value) {
                Log::debug("Searching in Student model with value: $value");
                $this->searchInTranslatableFields($query, 'name', $value);
            });

            // Search in teacher's name (through member relation in TeacherProfile)
            Log::debug('Adding teacher name search condition');
            $query->orWhereHasMorph('attendable', [TeacherProfile::class], function ($query) use ($value) {
                Log::debug("Searching in TeacherProfile model with value: $value");
                $query->whereHas('member', function ($query) use ($value) {
                    $this->searchInTranslatableFields($query, 'name', $value);
                });
            });

            // Search in reason (which is translatable)
            Log::debug('Adding reason search condition');
            $locales = config('app.locales');
            Log::debug('Using locales for reason search:', $locales);

            foreach ($locales as $index => $locale) {
                $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                Log::debug("Using $method for locale: $locale");

                $query->$method(
                    "json_extract(LOWER(reason), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci",
                    ['%'.strtolower($value).'%']
                );

                Log::debug("Added $method SQL condition for reason search in locale: $locale");
            }
        });

        Log::debug('Completed building search filter');
    }

    /**
     * Filter by status ID
     */
    private function filterByStatusId($query, $value)
    {
        Log::debug('Filtering by status_id', ['status_id' => $value]);

        if (is_array($value)) {
            Log::info('Filtering by multiple status IDs', ['status_ids' => $value]);
            $query->whereIn('status_id', $value);
        } else {
            Log::info("Filtering by single status ID: $value");
            $query->where('status_id', $value);
        }

        Log::debug('Completed filtering by status_id');
    }

    /**
     * Helper method to search in translatable fields
     */
    private function searchInTranslatableFields($query, $field, $value)
    {
        $locales = config('app.locales');
        Log::info('Locales fetched from config:', $locales);

        foreach ($locales as $index => $locale) {
            Log::info("Applying search for '$field' in locale: $locale");

            $sqlFragment = "json_extract(LOWER($field), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
            $bindingValue = '%'.strtolower($value).'%';

            if ($index === 0) {
                Log::debug("Using whereRaw with SQL: $sqlFragment and binding: $bindingValue");
                $query->whereRaw($sqlFragment, [$bindingValue]);
            } else {
                Log::debug("Using orWhereRaw with SQL: $sqlFragment and binding: $bindingValue");
                $query->orWhereRaw($sqlFragment, [$bindingValue]);
            }

            Log::debug("Added SQL condition for $field search in locale: $locale");
        }

        Log::debug("Completed translatable field search for field: $field");
    }

    /**
     * Filter by attendable type
     */
    private function filterByAttendableType($query, $value)
    {
        Log::debug("Beginning attendable type filter with value: $value");

        switch ($value) {
            case AttendableType::TEACHER->value:
                Log::info('Filtering for teacher profiles');
                $query->whereHasMorph('attendable', [TeacherProfile::class], function ($q) {
                    Log::debug('Adding eager loading for teacher.member relation');
                    $q->with(['member']);
                });
                Log::debug('Applied TeacherProfile filter');
                break;

            case AttendableType::STUDENT->value:
                Log::info('Filtering for student profiles');
                $query->whereHasMorph('attendable', [Student::class], function ($q) {
                    Log::debug('Applied Student filter');
                    // $q->with(['classroom']);
                });
                break;

            default:
                Log::error("Unsupported attendable type received: $value");
                throw new CustomBusinessException(
                    message: 'School Member Type is not supported.',
                    code: 422,
                    data: [
                        'attendable_type' => $value,
                        'supported_types' => AttendableType::getAllFromDatabase()->select(['constant_name']),
                    ]
                );
        }

        Log::debug('Completed attendable type filter');
    }

    /**
     * Filter by attendable ID
     */
    private function filterByAttendableID($query, $value)
    {
        Log::debug('Beginning filterByAttendableID', ['raw_value' => $value]);

        $Ids = filterArrayForNullValues($value);
        Log::info('Filtered attendable IDs', ['filtered_ids' => $Ids]);

        if (count($Ids) > 0) {
            Log::info('Applying attendable_id filter with values:', ['attendable_ids' => $Ids]);
            $query->whereIn('attendable_id', $Ids);
            Log::debug('Applied whereIn condition for attendable_id');
        } else {
            Log::info('No valid attendable_id values to filter.');
        }

        Log::debug('Completed filterByAttendableID');
    }

    /**
     * Filter by classroom ID
     */
    private function filterByClassroomId($query, $value)
    {
        Log::debug('Beginning filterByClassroomId', ['classroom_id' => $value]);

        if (is_array($value)) {
            Log::info('Filtering by multiple classroom IDs', ['classroom_ids' => $value]);
            $query->whereIn('classroom_id', $value);
        } else {
            Log::info("Filtering by single classroom ID: $value");
            $query->where('classroom_id', $value);
        }

        Log::debug('Completed filterByClassroomId');
    }

    /**
     * Filter by date range
     */
    private function filterByDateRange($query, $value)
    {
        Log::debug('Beginning filterByDateRange', ['date_range' => $value]);

        if (is_array($value) && count($value) === 2) {
            $startDate = $value[0];
            $endDate = $value[1];

            Log::info('Filtering by date range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            try {
                $startDateTime = Carbon::parse($startDate)->startOfDay();
                $endDateTime = Carbon::parse($endDate)->endOfDay();

                Log::debug('Parsed date range', [
                    'start_datetime' => $startDateTime->toDateTimeString(),
                    'end_datetime' => $endDateTime->toDateTimeString(),
                ]);

                $query->whereBetween('attendance_date_time', [
                    $startDateTime,
                    $endDateTime,
                ]);

                Log::debug('Applied whereBetween condition for date range');
            } catch (\Exception $e) {
                Log::error('Error parsing date range', [
                    'error' => $e->getMessage(),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        } else {
            Log::warning('Invalid date range format', ['value' => $value]);
        }

        Log::debug('Completed filterByDateRange');
    }

    /**
     * Filter by specific attendance date
     */
    private function filterByAttendanceDate($query, $value)
    {
        Log::debug('Beginning filterByAttendanceDate', ['attendance_date' => $value]);

        try {
            $date = Carbon::parse($value);
            Log::info('Filtering by attendance date', ['parsed_date' => $date->toDateString()]);

            $query->whereDate('attendance_date_time', $date);
            Log::debug('Applied whereDate condition for attendance_date_time');
        } catch (\Exception $e) {
            Log::error('Error parsing attendance date', [
                'error' => $e->getMessage(),
                'date' => $value,
            ]);
        }

        Log::debug('Completed filterByAttendanceDate');
    }

    /**
     * Filter by school ID
     */
    private function filterBySchoolId($query, $value)
    {
        Log::debug('Beginning filterBySchoolId', ['school_id' => $value]);

        if (is_array($value)) {
            Log::info('Filtering by multiple school IDs', ['school_ids' => $value]);
            $query->whereIn('school_id', $value);
        } else {
            Log::info("Filtering by single school ID: $value");
            $query->where('school_id', $value);
        }

        Log::debug('Completed filterBySchoolId');
    }

    /**
     * Filter by creator ID
     */
    private function filterByCreatedBy($query, $value)
    {
        Log::debug('Beginning filterByCreatedBy', ['created_by' => $value]);

        if (is_array($value)) {
            Log::info('Filtering by multiple creator IDs', ['creator_ids' => $value]);
            $query->whereIn('created_by', $value);
        } else {
            Log::info("Filtering by single creator ID: $value");
            $query->where('created_by', $value);
        }

        Log::debug('Completed filterByCreatedBy');
    }
}
