<?php

namespace App\Traits;

trait WhatsAppNotifiable
{
    /**
     * Route notifications for the UltraMsg channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForUltramsg($notification)
    {
        return $this->full_phone;
    }

    /**
     * Route notifications for the Twilio channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForTwilio($notification)
    {
        return $this->full_phone;
    }
}
