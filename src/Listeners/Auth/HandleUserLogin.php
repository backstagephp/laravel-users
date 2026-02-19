<?php

namespace Backstage\Laravel\Users\Listeners\Auth;

use Backstage\Laravel\Users\Jobs\RecordUserLogin;
use Illuminate\Auth\Events\Login;

class HandleUserLogin
{
    public function handle(Login $event): void
    {
        /** @var \Backstage\Laravel\Users\Eloquent\Models\User $user */
        $user = $event->user;

        $inputs = request()->except('_method', '_token', 'password');

        RecordUserLogin::dispatch(
            userId: $user->id,
            type: 'login',
            url: request()->url(),
            referrer: request()->server('HTTP_REFERER'),
            inputs: count($inputs) ? $inputs : null,
            userAgent: request()->server('HTTP_USER_AGENT'),
            ipAddress: request()->ip(),
        );
    }
}
