<?php

namespace App\Services\API\Filters;

use App\Enums\AttendableType;
use App\Enums\AttendanceTrackerStatus;
use App\Exceptions\CustomBusinessException;
use App\Models\Student;
use App\Models\TeacherProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceTrackerFilterServicesss
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
    }

    /**
     * Applies the filter based on the key provided.
     */
    protected function applyFilter($query, $key, $value)
    {
        switch ($key) {
            case 'search':
                $this->filterBySearch($query, $value);
                break;
            case 'status_type':
                $this->filterByStatusType($query, $value);
                break;
            case 'attendable_type':
                $this->filterByAttendableType($query, $value);
                break;

                // case 'classroom_id':
                //     $this->filterByClassroomId($query, $value);
                //     break;

                // case 'classroom_ids':
                //     $this->filterByClassroomIds($query, $value);
                //     break;

                // case 'status_id':
                //     $this->filterByStatusId($query, $value);
                //     break;

                // case 'status_ids':
                //     $this->filterByStatusIds($query, $value);
                //     break;

                // case 'attendance_date':
                //     $this->filterByAttendanceDate($query, $value);
                //     break;

                // case 'attendance_date_from':
                //     $this->filterByAttendanceDateFrom($query, $value);
                //     break;

                // case 'attendance_date_to':
                //     $this->filterByAttendanceDateTo($query, $value);
                //     break;

                // case 'student_id':
                //     $this->filterByStudentId($query, $value);
                //     break;

                // case 'teacher_id':
                //     $this->filterByTeacherId($query, $value);
                //     break;

                // case 'minutes_late_min':
                //     $this->filterByMinutesLateMin($query, $value);
                //     break;

                // case 'minutes_late_max':
                //     $this->filterByMinutesLateMax($query, $value);
                //     break;

                // case 'is_present':
                //     $this->filterByPresent($query, $value);
                //     break;

                // case 'is_absent':
                //     $this->filterByAbsent($query, $value);
                //     break;

                // case 'is_tardiness':
                //     $this->filterByTardiness($query, $value);
                //     break;

            default:
                Log::warning("Unknown filter key for attendance tracker: $key");
                break;
        }
    }

    /**
     * Filters the query by search term.
     */
    private function filterBySearch($query, $value)
    {
        $query->where(function ($query) use ($value) {
            // Search in student's name (through attendable relation)
            $query->whereHasMorph('attendable', [Student::class], function ($query) use ($value) {
                $this->searchInTranslatableFields($query, 'name', $value);
            });

            // Search in teacher's name (through member relation in TeacherProfile)
            $query->orWhereHasMorph('attendable', [TeacherProfile::class], function ($query) use ($value) {
                $query->whereHas('member', function ($query) use ($value) {
                    $this->searchInTranslatableFields($query, 'name', $value);
                });
            });

            // Search in reason (which is translatable)
            $locales = config('app.locales');
            foreach ($locales as $index => $locale) {
                $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                $query->$method(
                    "json_extract(LOWER(reason), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci",
                    ['%'.strtolower($value).'%']
                );
            }
        });
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

            if ($index === 0) {
                $query->whereRaw(
                    "json_extract(LOWER($field), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci",
                    ['%'.strtolower($value).'%']
                );
            } else {
                $query->orWhereRaw(
                    "json_extract(LOWER($field), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci",
                    ['%'.strtolower($value).'%']
                );
            }
        }
    }

    /**
     * Filter by classroom ID
     */
    private function filterByClassroomId($query, $value)
    {
        if ($value) {
            $query->where('classroom_id', $value);
        }
    }

    /**
     * Filter by multiple classroom IDs
     */
    private function filterByClassroomIds($query, $value)
    {
        $ids = filterArrayForNullValues($value);
        if (count($ids) > 0) {
            $query->whereIn('classroom_id', $ids);
        } else {
            Log::info('No valid classroom IDs to filter.');
        }
    }

    /**
     * Filter by status ID
     */
    private function filterByStatusId($query, $value)
    {
        if ($value) {
            $query->where('status_id', $value);
        }
    }

    /**
     * Filter by multiple status IDs
     */
    private function filterByStatusIds($query, $value)
    {
        $ids = filterArrayForNullValues($value);
        if (count($ids) > 0) {
            $query->whereIn('status_id', $ids);
        } else {
            Log::info('No valid status IDs to filter.');
        }
    }

    /**
     * Filter by exact attendance date
     */
    private function filterByAttendanceDate($query, $value)
    {
        try {
            $date = Carbon::parse($value)->toDateString();
            $query->whereDate('attendance_date', $date);
        } catch (\Exception $e) {
            Log::error("Invalid date format for attendance_date: $value", ['exception' => $e->getMessage()]);
        }
    }

    /**
     * Filter by attendance date from
     */
    private function filterByAttendanceDateFrom($query, $value)
    {
        try {
            $date = Carbon::parse($value)->toDateString();
            $query->whereDate('attendance_date', '>=', $date);
        } catch (\Exception $e) {
            Log::error("Invalid date format for attendance_date_from: $value", ['exception' => $e->getMessage()]);
        }
    }

    /**
     * Filter by attendance date to
     */
    private function filterByAttendanceDateTo($query, $value)
    {
        try {
            $date = Carbon::parse($value)->toDateString();
            $query->whereDate('attendance_date', '<=', $date);
        } catch (\Exception $e) {
            Log::error("Invalid date format for attendance_date_to: $value", ['exception' => $e->getMessage()]);
        }
    }

    /**
     * Filter by student ID (in attendable relation)
     */
    private function filterByStudentId($query, $value)
    {
        if ($value) {
            $query->where(function ($query) use ($value) {
                $query->where('attendable_type', Student::class)
                    ->where('attendable_id', $value);
            });
        }
    }

    /**
     * Filter by teacher ID (in attendable relation)
     */
    private function filterByTeacherId($query, $value)
    {
        if ($value) {
            $query->where(function ($query) use ($value) {
                $query->where('attendable_type', TeacherProfile::class)
                    ->where('attendable_id', $value);
            });
        }
    }

    /**
     * Filter by minimum minutes late
     */
    private function filterByMinutesLateMin($query, $value)
    {
        if (is_numeric($value)) {
            $query->where('minutes_late', '>=', intval($value));
        } else {
            Log::warning("Non-numeric value for minutes_late_min: $value");
        }
    }

    /**
     * Filter by maximum minutes late
     */
    private function filterByMinutesLateMax($query, $value)
    {
        if (is_numeric($value)) {
            $query->where('minutes_late', '<=', intval($value));
        } else {
            Log::warning("Non-numeric value for minutes_late_max: $value");
        }
    }

    /**
     * Filter by present status
     */
    private function filterByPresent($query, $value)
    {
        if ($value === 'true' || $value === '1' || $value === true || $value === 1) {
            $statusId = AttendanceTrackerStatus::PRESENT->getFromDatabase()->id;
            $query->where('status_id', $statusId);
        }
    }

    /**
     * Filter by absent status
     */
    private function filterByAbsent($query, $value)
    {
        if ($value === 'true' || $value === '1' || $value === true || $value === 1) {
            $statusId = AttendanceTrackerStatus::ABSENT->getFromDatabase()->id;
            $query->where('status_id', $statusId);
        }
    }

    /**
     * Filter by tardiness status
     */
    private function filterByTardiness($query, $value)
    {
        if ($value === 'true' || $value === '1' || $value === true || $value === 1) {
            $statusId = AttendanceTrackerStatus::TARDINESS->getFromDatabase()->id;
            $query->where('status_id', $statusId);
        }
    }

    /**
     * Filter by school member type
     */
    private function filterByAttendableType($query, $value)
    {
        // dd($value);
        switch ($value) {
            case AttendableType::TEACHER->value:
                $query->whereHasMorph('attendable', [TeacherProfile::class], function ($q) {
                    $q->with(['member']);
                });
                break;
            case AttendableType::STUDENT->value:
                $query->whereHasMorph('attendable', [Student::class], function ($q) {
                    $q->with(['classroom']);
                });
                break;
            default:
                throw new CustomBusinessException(
                    message: 'School Member Type is not supported.',
                    code: 422,
                    data: [
                        'attendable_type' => $value,
                        'supported_types' => AttendableType::getAllFromDatabase()->select(['constant_name']),
                    ]
                );
        }
    }

    /**
     * Filter by status type
     */
    private function filterByStatusType($query, $value)
    {
        switch ($value) {
            case AttendanceTrackerStatus::PRESENT->value:
                $statusId = AttendanceTrackerStatus::PRESENT->getFromDatabase()->id;
                $query->where('status_id', $statusId);
                break;
            case AttendanceTrackerStatus::ABSENT->value:
                $statusId = AttendanceTrackerStatus::ABSENT->getFromDatabase()->id;
                $query->where('status_id', $statusId);
                break;
            case AttendanceTrackerStatus::TARDINESS->value:
                $statusId = AttendanceTrackerStatus::TARDINESS->getFromDatabase()->id;
                $query->where('status_id', $statusId);
                break;
            default:
                throw new CustomBusinessException(
                    message: 'Status Type is not supported.',
                    code: 422,
                    data: [
                        'status_type' => $value,
                        'supported_types' => AttendanceTrackerStatus::getAllFromDatabase()->select(['constant_name']),
                    ]
                );
        }
    }
}
