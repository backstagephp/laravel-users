<?php

namespace Backstage\Laravel\Users\Notifications\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmEmailChange extends Notification
{
    use Queueable;

    public function __construct(
        public string $newEmail,
        public string $confirmUrl,
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
            ->subject(__('Confirm your new email address'))
            ->greeting(__('Hello!'))
            ->line(__('We received a request to change the email address on your account to :email.', ['email' => $this->newEmail]))
            ->line(__('Click the button below to confirm this change. The link will expire after a short time for your security.'))
            ->action(__('Confirm email change'), $this->confirmUrl)
            ->line(__('If you did not request this change, you can safely ignore this email.'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
