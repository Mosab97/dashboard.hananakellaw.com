<?php

namespace App\Notifications;

use App\Notifications\Channels\FCM\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestFcmNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toFcm($notifiable)
    {
        return [
            'title' => 'Test Notification',
            'body' => 'This is a test notification from your Laravel application.',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'sound' => 'default',
            'badge' => '1',
            'icon' => 'notification_icon',
            'type' => 'test',
            'time' => now()->toIso8601String(),
            'priority' => 'high',
        ];
    }
}
