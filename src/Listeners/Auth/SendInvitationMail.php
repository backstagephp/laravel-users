<?php

namespace Backstage\LaravelUsers\Listeners\Auth;

use Backstage\LaravelUsers\Events\Auth\UserCreated;

class SendInvitationMail
{
    public function handle(UserCreated $event)
    {
        $event->user->notify(new Invitation);
    }
}
