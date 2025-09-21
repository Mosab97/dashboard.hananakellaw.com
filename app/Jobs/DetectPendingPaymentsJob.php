<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class DetectPendingPaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // this will do for all payment gateaways that are not provided webhooks
        // // Check for pending payments and process them
        // // This is where you would implement the logic to check for pending payments
        // // and take appropriate actions, such as sending notifications or updating records.

        // Log::info('CheckPendingPaymentsJob start.');

        // Payments::chunkById(10, function ($payments) {
        //     foreach ($payments as $payment) {

        //     }

        // })

    }
}
