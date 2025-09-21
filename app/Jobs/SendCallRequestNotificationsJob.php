<?php

namespace App\Jobs;

use App\Models\CallRequest;
use App\Notifications\API\CallRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendCallRequestNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $callRequest;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(CallRequest $callRequest)
    {
        $this->callRequest = $callRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting to send call request notifications', [
                'call_request_id' => $this->callRequest->id,
                'total_teachers' => $this->callRequest->teachers->count(),
            ]);

            // Load necessary relationships if not already loaded
            $this->callRequest->load(['teachers.member', 'student', 'type']);

            // Send notifications to each assigned teacher
            foreach ($this->callRequest->teachers as $teacher) {
                try {
                    $teacher->member->notify(new CallRequestNotification($this->callRequest));

                    Log::info('Call request notification sent to teacher', [
                        'call_request_id' => $this->callRequest->id,
                        'teacher_id' => $teacher->id,
                        'student_id' => $this->callRequest->student_id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send call request notification to teacher', [
                        'call_request_id' => $this->callRequest->id,
                        'teacher_id' => $teacher->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    throw $e;
                }
            }
        } catch (\Exception $e) {
            Log::error('Job failed: SendCallRequestNotificationsJob', [
                'call_request_id' => $this->callRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Job failed permanently: SendCallRequestNotificationsJob', [
            'call_request_id' => $this->callRequest->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
