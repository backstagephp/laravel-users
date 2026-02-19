<?php

namespace Backstage\Laravel\Users\Listeners\Auth;

use Backstage\Laravel\Users\Jobs\RecordUserLogin;
use Illuminate\Auth\Events\Logout;

class HandleUserLogout
{
    public function handle(Logout $event): void
    {
        /** @var \Backstage\Laravel\Users\Eloquent\Models\User|null $user */
        $user = $event->user;

        if (! $user) {
            return;
        }

        $inputs = request()->except('_method', '_token', 'password');

        RecordUserLogin::dispatch(
            userId: $user->id,
            type: 'logout',
            url: request()->url(),
            referrer: request()->server('HTTP_REFERER'),
            inputs: count($inputs) ? $inputs : null,
            userAgent: request()->server('HTTP_USER_AGENT'),
            ipAddress: request()->ip(),
        );
    }
}
