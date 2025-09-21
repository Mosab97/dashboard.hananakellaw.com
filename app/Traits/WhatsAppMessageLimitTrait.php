<?php

namespace App\Traits;

use Akaunting\Setting\Facade as Setting;
use App\Models\Subscription;
use App\Services\MessageRateLimit\MessageLimitLogger;
use Carbon\Carbon;

trait WhatsAppMessageLimitTrait
{
    /**
     * Get the message limit for this school
     * Falls back to system default if not set
     */
    public function getMessageLimitAttribute()
    {
        // First check for active subscription limit
        $activeSubscription = Subscription::where('member_id', $this->member_id)
            ->active()
            ->orderBy('end_date', 'desc')
            ->first();

        $limit = 0;
        $source = 'default';

        if ($activeSubscription && $activeSubscription->whatsapp_message_limit > 0) {
            $limit = $activeSubscription->whatsapp_message_limit;
            $source = 'subscription';
        } elseif ($this->whatsapp_message_limit > 0) {
            // If no subscription or subscription has no limit, fall back to school's own limit
            $limit = $this->whatsapp_message_limit;
            $source = 'school';
        } else {
            // Default system limit as last resort
            $limit = Setting::get('whatsapp_message_limit_system', 1000);
            $source = 'system';
        }

        return $limit;
    }

    /**
     * Get the number of remaining messages this school can send
     */
    public function getRemainingMessagesAttribute()
    {
        $this->checkMessageReset();
        $remaining = max(0, $this->message_limit - $this->whatsapp_message_count);

        return $remaining;
    }

    /**
     * Check if the message count should be reset based on the system settings
     */
    public function checkMessageReset()
    {
        // We now reset message count only when a new subscription is added
        // So this method just ensures we have a last reset date recorded
        if (! $this->last_whatsapp_message_reset) {
            $this->resetMessageCount();
        }

        return $this;
    }

    /**
     * Reset the message count to zero
     */
    public function resetMessageCount()
    {
        $this->whatsapp_message_count = 0;
        $this->last_whatsapp_message_reset = Carbon::now();
        $this->save();

        return $this;
    }

    /**
     * Calculate the cost of extra messages
     */
    public function calculateExtraMessagesCost()
    {
        $messagePrice = Setting::get('whatsapp_message_price', 0.10);
        $oldCost = $this->extra_messages_cost;
        $this->extra_messages_cost = $this->extra_messages_count * $messagePrice;

        MessageLimitLogger::info('Calculated extra messages cost', [
            'school_id' => $this->id,
            'extra_messages' => $this->extra_messages_count,
            'message_price' => $messagePrice,
            'old_cost' => $oldCost,
            'new_cost' => $this->extra_messages_cost,
            'action' => 'calculate_extra_cost',
        ]);

        $this->save();

        return $this;
    }

    /**
     * Reset the extra messages counter (when subscription is renewed)
     */
    public function resetExtraMessagesCount()
    {
        MessageLimitLogger::info('Resetting extra messages count', [
            'school_id' => $this->id,
            'old_extra_count' => $this->extra_messages_count,
            'old_extra_cost' => $this->extra_messages_cost,
            'action' => 'reset_extra_count',
        ]);

        $this->extra_messages_count = 0;
        $this->extra_messages_cost = 0;
        $this->save();

        return $this;
    }

    /**
     * Check if the school can send more WhatsApp messages
     */
    public function canSendMessage()
    {
        return $this->remaining_messages > 0;
    }

    /**
     * Increment the message count for this school
     */
    public function incrementMessageCount($count = 1)
    {
        $this->checkMessageReset();
        $this->whatsapp_message_count += $count;
        $this->save();

        return $this;
    }

    /**
     * Get usage percentage of WhatsApp messages
     */
    public function getMessageUsagePercentAttribute()
    {
        $this->checkMessageReset();
        if ($this->message_limit <= 0) {
            return 100;
        }

        return min(100, round(($this->whatsapp_message_count / $this->message_limit) * 100));
    }

    /**
     * Get WhatsApp message status for display
     */
    public function getMessageStatusAttribute()
    {
        $percent = $this->message_usage_percent;

        if ($percent >= 90) {
            return 'danger';
        } elseif ($percent >= 70) {
            return 'warning';
        } else {
            return 'success';
        }
    }
}
