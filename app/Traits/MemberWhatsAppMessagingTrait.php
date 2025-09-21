<?php

namespace App\Traits;

use App\Models\Subscription;
use App\Services\MessageRateLimit\MessageLimitLogger;
use Illuminate\Support\Facades\Log;

trait MemberWhatsAppMessagingTrait
{
    /**
     * Get the active subscription for this member
     *
     * @return Subscription|null
     */
    public function getActiveSubscription()
    {
        return Subscription::where('member_id', $this->id)
            ->active()
            ->orderBy('end_date', 'desc')
            ->first();
    }

    /**
     * Check if member can send WhatsApp messages
     *
     * @return bool
     */
    public function canSendWhatsAppMessage()
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            MessageLimitLogger::warning('No active subscription found for message sending', [
                'member_id' => $this->id,
                'member_name' => $this->name ?? 'Unknown',
                'member_type' => $this->type ? $this->type->name : 'Unknown',
                'action' => 'check_message_permission',
            ]);

            return false;
        }

        return $subscription->canSendMessage();
    }

    /**
     * Get remaining message count
     *
     * @return int
     */
    public function getRemainingWhatsAppMessagesAttribute()
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return 0;
        }

        return $subscription->remaining_messages;
    }

    /**
     * Track a sent WhatsApp message
     *
     * @param  int  $count  Number of messages to track
     * @return $this
     */
    public function incrementMessageCount($count = 1)
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            MessageLimitLogger::error('Attempted to track message for member without active subscription', [
                'member_id' => $this->id,
                'member_name' => $this->name ?? 'Unknown',
                'member_type' => $this->type ? $this->type->name : 'Unknown',
                'message_count' => $count,
                'action' => 'track_message_failed',
            ]);

            return $this;
        }

        $subscription->incrementMessageCount($count);

        return $this;
    }

    /**
     * Get message usage percentage
     *
     * @return int
     */
    public function getWhatsAppMessageUsagePercentAttribute()
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return 100;
        }

        return $subscription->message_usage_percent;
    }

    /**
     * Get message status (for UI display)
     *
     * @return string
     */
    public function getWhatsAppMessageStatusAttribute()
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            return 'danger';
        }

        return $subscription->message_status;
    }

    /**
     * Increase the WhatsApp message quota
     *
     * @return bool|array
     */
    public function increaseWhatsAppMessageQuota(int $additionalMessages, ?string $reason = null)
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            MessageLimitLogger::error('Cannot increase quota: no active subscription found', [
                'member_id' => $this->id,
                'member_name' => $this->name ?? 'Unknown',
                'member_type' => $this->type ? $this->type->name : 'Unknown',
                'requested_messages' => $additionalMessages,
                'reason' => $reason,
                'action' => 'increase_quota_failed',
            ]);

            return [
                'status' => false,
                'message' => t('No active subscription found for this member. Please activate a subscription first.'),
            ];
        }

        try {
            $subscription->increaseMessageQuota($additionalMessages, $reason);

            return [
                'status' => true,
                'message' => t('WhatsApp message quota has been increased successfully!'),
                'new_limit' => $subscription->whatsapp_message_limit,
            ];
        } catch (\Exception $e) {
            Log::error('Error increasing WhatsApp message quota', [
                'member_id' => $this->id,
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => t('An error occurred while increasing the WhatsApp message quota. Please try again.'),
            ];
        }
    }
}
