<?php

namespace App\Jobs\AttendanceTracker;

use App\Models\AttendanceTracker;
use App\Notifications\API\TeacherTardinessNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class TeacherTardinessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attendanceDateTime;

    protected $attendanceTrackerId;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    public function __construct(string $attendanceDateTime, int $attendanceTrackerId)
    {
        $this->attendanceDateTime = $attendanceDateTime;
        $this->attendanceTrackerId = $attendanceTrackerId;
    }

    public function handle(): void
    {
        try {
            $attendanceTracker = AttendanceTracker::with(['attendable.member', 'creator'])
                ->findOrFail($this->attendanceTrackerId);

            // Send notification to the teacher
            try {
                $attendanceTracker->attendable->member->notify(
                    new TeacherTardinessNotification($this->attendanceDateTime, $attendanceTracker)
                );

            } catch (\Exception $e) {
                Log::error('Failed to send tardiness notification', [
                    'attendance_tracker_id' => $attendanceTracker->id,
                    'teacher_id' => $attendanceTracker->attendable_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Job failed: TeacherTardinessNotificationJob', [
                'attendance_tracker_id' => $this->attendanceTrackerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Job failed permanently: TeacherTardinessNotificationJob', [
            'attendance_tracker_id' => $this->attendanceTrackerId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
