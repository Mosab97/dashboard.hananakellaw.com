<?php

namespace App\Traits;

use Akaunting\Setting\Facade as Setting;
use App\Services\MessageRateLimit\MessageLimitLogger;
use Carbon\Carbon;

trait SubscriptionWhatsAppMessagingTrait
{
    /**
     * DB columns required in subscription table:
     * - whatsapp_message_limit: int (maximum number of messages allowed)
     * - whatsapp_message_count: int (current number of messages sent)
     * - last_whatsapp_message_reset: datetime (when the counter was last reset)
     * - extra_messages_count: int (number of messages sent beyond the limit)
     * - extra_messages_cost: decimal (cost of extra messages)
     */

    /**
     * Get the subscription member type for debugging/logging
     */
    public function getMemberTypeAttribute()
    {
        return $this->userType ? $this->userType->name : 'Unknown';
    }

    /**
     * Get the number of remaining messages for this subscription
     */
    public function getRemainingMessagesAttribute()
    {
        $this->checkMessageReset();
        $remaining = max(0, $this->whatsapp_message_limit - $this->whatsapp_message_count);

        return $remaining;
    }

    /**
     * Check if the message count should be reset based on the system settings
     */
    public function checkMessageReset()
    {
        // If no last reset date, initialize it
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
        $oldCount = $this->whatsapp_message_count ?? 0;
        $this->whatsapp_message_count = 0;
        $this->last_whatsapp_message_reset = Carbon::now();
        $this->save();

        // Log the reset action
        MessageLimitLogger::info('Reset WhatsApp message count', [
            'subscription_id' => $this->id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
            'old_count' => $oldCount,
            'new_count' => 0,
            'action' => 'reset_message_count',
        ]);

        return $this;
    }

    /**
     * Calculate the cost of extra messages
     */
    public function calculateExtraMessagesCost()
    {
        $messagePrice = Setting::get('whatsapp_message_price', 0.10);
        $oldCost = $this->extra_messages_cost ?? 0;
        $this->extra_messages_cost = $this->extra_messages_count * $messagePrice;

        MessageLimitLogger::info('Calculated extra messages cost', [
            'subscription_id' => $this->id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
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
            'subscription_id' => $this->id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
            'old_extra_count' => $this->extra_messages_count ?? 0,
            'old_extra_cost' => $this->extra_messages_cost ?? 0,
            'action' => 'reset_extra_count',
        ]);

        $this->extra_messages_count = 0;
        $this->extra_messages_cost = 0;
        $this->save();

        return $this;
    }

    /**
     * Check if the subscription can send more WhatsApp messages
     */
    public function canSendMessage()
    {
        return $this->remaining_messages > 0;
    }

    /**
     * Increment the message count for this subscription
     */
    public function incrementMessageCount($count = 1)
    {
        $this->checkMessageReset();

        $oldCount = $this->whatsapp_message_count ?? 0;
        $this->whatsapp_message_count = ($this->whatsapp_message_count ?? 0) + $count;

        // Check if we've exceeded the limit and need to track extra messages
        if ($oldCount < $this->whatsapp_message_limit && $this->whatsapp_message_count > $this->whatsapp_message_limit) {
            // Calculate how many messages went over the limit
            $extraMessages = $this->whatsapp_message_count - $this->whatsapp_message_limit;
            $this->extra_messages_count = ($this->extra_messages_count ?? 0) + $extraMessages;

            MessageLimitLogger::warning('Message limit exceeded', [
                'subscription_id' => $this->id,
                'member_id' => $this->member_id,
                'member_type' => $this->member_type,
                'limit' => $this->whatsapp_message_limit,
                'old_count' => $oldCount,
                'new_count' => $this->whatsapp_message_count,
                'extra_messages' => $extraMessages,
                'total_extra_messages' => $this->extra_messages_count,
                'action' => 'limit_exceeded',
            ]);

            // Recalculate cost for extra messages
            $this->calculateExtraMessagesCost();
        } else {
            MessageLimitLogger::info('Incremented message count', [
                'subscription_id' => $this->id,
                'member_id' => $this->member_id,
                'member_type' => $this->member_type,
                'old_count' => $oldCount,
                'new_count' => $this->whatsapp_message_count,
                'added' => $count,
                'remaining' => $this->remaining_messages,
                'action' => 'increment_count',
            ]);
        }

        $this->save();

        return $this;
    }

    /**
     * Get usage percentage of WhatsApp messages
     */
    public function getMessageUsagePercentAttribute()
    {
        $this->checkMessageReset();
        if ($this->whatsapp_message_limit <= 0) {
            return 100;
        }

        return min(100, round((($this->whatsapp_message_count ?? 0) / $this->whatsapp_message_limit) * 100));
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

    /**
     * Increase the WhatsApp message quota for a subscription
     *
     * @param  int  $additionalMessages  Number of messages to add to quota
     * @param  string|null  $reason  Optional reason for the increase
     * @return $this
     */
    public function increaseMessageQuota(int $additionalMessages, ?string $reason = null)
    {
        $currentLimit = $this->whatsapp_message_limit;
        $newLimit = $currentLimit + $additionalMessages;

        MessageLimitLogger::info('Manual increase of WhatsApp message quota', [
            'subscription_id' => $this->id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
            'member_name' => $this->member->name ?? 'Unknown',
            'current_limit' => $currentLimit,
            'additional_messages' => $additionalMessages,
            'new_limit' => $newLimit,
            'reason' => $reason,
            'increased_by' => auth()->id(),
            'increased_by_name' => auth()->user()->name,
            'action' => 'manual_quota_increase',
        ]);

        $this->whatsapp_message_limit = $newLimit;
        $this->save();

        return $this;
    }
}
