<?php

namespace App\Jobs;

use App\Enums\AttendanceTrackerStatus;
use App\Models\ExitPermit;
use App\Notifications\API\ExitPermitExpiredNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireExitPermitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $permitId;

    public $tries = 3;

    public $timeout = 30;

    public function __construct(int $permitId)
    {
        $this->permitId = $permitId;
    }

    public function handle()
    {
        try {
            $permit = ExitPermit::with(['student', 'user'])->find($this->permitId);

            if (! $permit) {
                Log::warning('Exit permit not found for expiration', [
                    'permit_id' => $this->permitId,
                ]);

                return;
            }

            $endTime = $permit->getEndTime();
            if (now()->greaterThanOrEqualTo($endTime)) {
                $permit->update(['active' => false]);

                Log::info('Exit permit expired automatically', [
                    'permit_id' => $permit->id,
                    'student_id' => $permit->student_id,
                    'end_time' => $endTime->format('Y-m-d H:i:s'),
                ]);

                // Notify relevant users (teachers and school)
                if ($permit->student) {
                    // Notify teachers of the student's class
                    $startTime = $permit->created_at;
                    $endTime = $permit->getEndTime();
                    $statusId = AttendanceTrackerStatus::PRESENT->getFromDatabase()->id;

                    // Notify teachers who were present during permit duration
                    $teachersToNotify = $permit->student->classroom->teachers()
                        ->whereHas('attendanceRecords', function ($q) use ($startTime, $endTime, $statusId) {
                            $q->where('status_id', $statusId)
                                ->where('attendance_date_time', '>=', $startTime)
                                ->where('attendance_date_time', '<=', $endTime);
                        })
                        ->get();

                    foreach ($teachersToNotify as $teacher) {
                        $teacher->member->notify(new ExitPermitExpiredNotification($permit));
                    }

                    // Notify school
                    if ($permit->student->school && $permit->student->school->member) {
                        $permit->student->school->member->notify(
                            new ExitPermitExpiredNotification($permit)
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to expire exit permit', [
                'permit_id' => $this->permitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
