<?php

namespace Backstage\Laravel\Users\Listeners\Auth;

use Backstage\Laravel\Users\Events\Auth\UserCreated;
use Backstage\Laravel\Users\Notifications\Invitation;

class SendInvitationMail
{
    public function handle(UserCreated $event)
    {
        $invitationClass = config('users.events.auth.user_created.invitation_notification', Invitation::class);

        $event->user->notify(new $invitationClass);
    }
}
