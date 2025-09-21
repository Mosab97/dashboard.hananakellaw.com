<?php

namespace App\Services\API\Filters;

use App\Models\Student;
use App\Models\TeacherProfile;
use App\Services\API\Filters\Traits\RelationConfigTrait;

class AttendanceTrackerFilterServices extends BaseFilterService
{
    use RelationConfigTrait;

    /**
     * The main table name for attendance tracker
     */
    protected $tableName = 'attendance_trackers';

    /**
     * List of translatable fields in the attendance tracker model
     */
    protected $translatableFields = ['reason'];

    /**
     * List of foreign key fields in the attendance tracker model
     */
    protected $foreignKeyFields = [
        'attendable_id',
        'status_id',
        'classroom_id',
        'school_id',
        'created_by',
    ];

    /**
     * List of date fields in the attendance tracker model
     */
    protected $dateFields = [
        'attendance_date_time',
        'created_at',
        'updated_at',
    ];

    /**
     * List of time fields in the attendance tracker model
     */
    protected $timeFields = [
        'tardiness_time',
    ];

    /**
     * List of boolean fields in the attendance tracker model
     */
    protected $booleanFields = [];

    /**
     * List of JSON array fields in the attendance tracker model
     */
    protected $jsonArrayFields = [];

    /**
     * List of regular fields in the attendance tracker model (for exact matching)
     */
    protected $regularFields = [
        'id',
        // 'attendable_type'
    ];

    /**
     * List of fields that should use partial matching (LIKE)
     */
    protected $partialMatchFields = [];

    /**
     * List of relation fields for filtering attendance records
     */
    protected $relationFields = [
        'classroom' => ['id', 'name', 'class_number', 'grade_level_id'],
        'status' => ['id', 'name', 'type_id'],
        'school' => ['id', 'name'],
        'creator' => ['id', 'name', 'email', 'phone_number'],
        'attendable' => ['id', 'name', 'id_number', 'email', 'phone_number'],
    ];

    /**
     * Map relation names to their table names
     */
    protected $tableMap = [
        'classroom' => 'classrooms',
        'status' => 'constants',
        'school' => 'school_profiles',
        'creator' => 'members',
        'attendable.student' => 'students',
        'attendable.teacher' => 'teacher_profiles',
    ];

    /**
     * Polymorphic relation mappings
     */
    protected $polymorphicRelations = [
        'attendable' => [
            'type_field' => 'attendable_type',
            'id_field' => 'attendable_id',
            'types' => [
                'student' => Student::class,
                'students' => Student::class,
                'teacher' => TeacherProfile::class,
                'teachers' => TeacherProfile::class,
                'teacherprofile' => TeacherProfile::class,
            ],
        ],
    ];
}
