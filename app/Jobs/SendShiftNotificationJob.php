<?php

namespace App\Jobs;

use App\Models\Shift;
use App\Notifications\API\ShiftNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendShiftNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shift;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function handle(): void
    {
        try {
            Log::info('Starting to send shift notifications', [
                'shift_id' => $this->shift->id,
                'total_teachers' => $this->shift->teachers->count(),
            ]);

            // Load necessary relationships
            $this->shift->load(['teachers.member', 'type', 'creator']);

            // Send notification to each teacher
            foreach ($this->shift->teachers as $teacher) {
                try {
                    $teacher->member->notify(new ShiftNotification($this->shift));

                    Log::info('Shift notification sent to teacher', [
                        'shift_id' => $this->shift->id,
                        'teacher_id' => $teacher->id,
                        'teacher_name' => $teacher->member->name,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send shift notification to teacher', [
                        'shift_id' => $this->shift->id,
                        'teacher_id' => $teacher->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            Log::error('Job failed: SendShiftNotificationJob', [
                'shift_id' => $this->shift->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Job failed permanently: SendShiftNotificationJob', [
            'shift_id' => $this->shift->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
