<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupExportFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('Starting export files cleanup job');

        try {
            $disk = Storage::disk('public');
            $directory = 'exports';
            $expirationTime = Carbon::now()->subDay();

            $deletedCount = 0;

            collect($disk->files($directory))
                ->filter(function ($file) use ($disk, $expirationTime) {
                    return $disk->lastModified($file) < $expirationTime->timestamp;
                })
                ->each(function ($file) use ($disk, &$deletedCount) {
                    $disk->delete($file);
                    $deletedCount++;
                    Log::info('Deleted expired export file', ['file' => $file]);
                });

            Log::info('Cleanup job completed', ['files_deleted' => $deletedCount]);
        } catch (\Exception $e) {
            Log::error('Error in cleanup job', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
