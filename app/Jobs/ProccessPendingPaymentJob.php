<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\API\Subscription\SubscriptionService;
use App\Services\Payments\PaymentProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProccessPendingPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $paymentId;

    /**
     * Create a new job instance.
     */
    public function __construct($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = Payment::find($this->paymentId);
        if ($payment) {
            $paymentService = PaymentProviderFactory::create($payment->provider);
            $response = $paymentService->check($payment->transaction_id);
            if ($response['success']) {
                $payment->status = Payment::COMPLETED;
                $payment->save();
                $payment->refresh();
                (new SubscriptionService)->subscribeUsingSettingPlan($payment);

            } else {
                $payment->status = Payment::FAILED;
                $payment->save();
            }
        }
    }
}
