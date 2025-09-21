<?php

namespace App\Jobs;

use App\Enums\AttendanceTrackerStatus;
use App\Models\AttendanceTracker;
use App\Models\ExitPermit;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Notifications\API\ExitPermitNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendExitPermitNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $permit;

    protected $student;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    public function __construct(ExitPermit $permit, Student $student)
    {
        $this->permit = $permit;
        $this->student = $student;
    }

    public function handle()
    {
        try {
            // Find teachers who are checked in and teaching this student's class
            $statusId = AttendanceTrackerStatus::PRESENT->getFromDatabase()->id;
            $checkedInTeachers = AttendanceTracker::where('attendable_type', TeacherProfile::class)
                ->where('status_id', $statusId)
                ->whereDate('attendance_date_time', now())
                ->whereHasMorph('attendable', [TeacherProfile::class], function ($query) {
                    $query->whereHas('classrooms', function ($q) {
                        $q->where('classrooms.id', $this->student->class_id);
                    });
                })
                ->with(['attendable.member'])
                ->get();

            foreach ($checkedInTeachers as $attendance) {
                $teacher = $attendance->attendable->member;
                try {
                    $teacher->notify(new ExitPermitNotification($this->permit));
                } catch (\Exception $e) {
                    Log::error('Failed to send exit permit notification', [
                        'teacher_id' => $teacher->id,
                        'student_id' => $this->student->id,
                        'permit_id' => $this->permit->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'attempt' => $this->attempts(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        // 'job_id' => $this->job->getJobId()
                    ]);

                    // Re-throw to trigger job retry
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            Log::error('Job failed: SendExitPermitNotificationsJob', [
                'permit_id' => $this->permit->id,
                'student_id' => $this->student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),

                // 'job_id' => $this->job->getJobId()
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $exception)
    {
        Log::error('Job failed permanently: SendExitPermitNotificationsJob', [
            'permit_id' => $this->permit->id,
            'student_id' => $this->student->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
        ]);
    }
}
