<?php

namespace Backstage\Laravel\Users\Notifications\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChanged extends Notification
{
    use Queueable;

    public function __construct(
        public string $oldEmail,
        public string $newEmail,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your email address has been changed'))
            ->greeting(__('Hello!'))
            ->line(__('This is a confirmation that the email address on your account has been changed from :old to :new.', [
                'old' => $this->oldEmail,
                'new' => $this->newEmail,
            ]))
            ->line(__('If you did not initiate this change, please contact support immediately.'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
