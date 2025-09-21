<?php

namespace App\Jobs;

use App\Models\Expectation;
use App\Notifications\API\ExpectationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendExpectationNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $expectation;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    public function __construct(Expectation $expectation)
    {
        $this->expectation = $expectation;
    }

    public function handle(): void
    {
        try {
            Log::info('Starting to send expectation notification', [
                'expectation_id' => $this->expectation->id,
                'teacher_id' => $this->expectation->teacher_id,
            ]);

            // Load necessary relationships if not already loaded
            $this->expectation->load(['teacher.member', 'classroom', 'session']);

            // Send notification to teacher
            $this->expectation->teacher->member->notify(
                new ExpectationNotification($this->expectation)
            );

            Log::info('Expectation notification sent successfully', [
                'expectation_id' => $this->expectation->id,
                'teacher_id' => $this->expectation->teacher_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send expectation notification', [
                'expectation_id' => $this->expectation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Job failed permanently: SendExpectationNotificationJob', [
            'expectation_id' => $this->expectation->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
