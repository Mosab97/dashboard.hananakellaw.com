<?php

namespace App\Jobs;

use App\Models\Warning;
use App\Notifications\API\WarningNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWarningNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $warning;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    public function __construct(Warning $warning)
    {
        $this->warning = $warning;
    }

    public function handle(): void
    {
        try {
            Log::info('Starting to send warning notification', [
                'warning_id' => $this->warning->id,
                'teacher_id' => $this->warning->teacher_id,
            ]);

            $this->warning->load(['teacher.member', 'title', 'creator']);

            $this->warning->teacher->member->notify(
                new WarningNotification($this->warning)
            );

            Log::info('Warning notification sent successfully', [
                'warning_id' => $this->warning->id,
                'teacher_id' => $this->warning->teacher_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send warning notification', [
                'warning_id' => $this->warning->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Job failed permanently: SendWarningNotificationJob', [
            'warning_id' => $this->warning->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
