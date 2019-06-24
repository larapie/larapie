<?php

namespace App\Modules\User\Notifications;

use App\Modules\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * WelcomeNotification constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toMail(User $user)
    {
        return (new MailMessage)
            ->greeting('Hello! '.$user->name)
            ->line('Welcome to Larapie!')
            ->line('Thank you for using our application!');
    }

    public function via(User $user)
    {
        return ['mail'];
    }
}
