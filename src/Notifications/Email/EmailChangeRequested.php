<?php

namespace Backstage\Laravel\Users\Notifications\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeRequested extends Notification
{
    use Queueable;

    public function __construct(
        public string $newEmail,
        public string $cancelUrl,
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
            ->subject(__('A change to your email address was requested'))
            ->greeting(__('Hello!'))
            ->line(__('Someone requested to change the email address on your account to :email.', ['email' => $this->newEmail]))
            ->line(__('If this was you, no further action is needed; the change will take effect once it is confirmed from the new address.'))
            ->action(__('I did not request this'), $this->cancelUrl)
            ->line(__('Clicking the button above will cancel the pending change immediately.'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
