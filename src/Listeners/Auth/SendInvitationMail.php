<?php

namespace Backstage\Laravel\Users\Listeners\Auth;

use Backstage\Laravel\Users\Events\Auth\UserCreated;
use Backstage\Laravel\Users\Notifications\Invitation;

class SendInvitationMail
{
    public function handle(UserCreated $event)
    {
        $event->user->notify(new Invitation);
    }
}
