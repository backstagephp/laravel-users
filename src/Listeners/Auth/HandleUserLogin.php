<?php

namespace Backstage\Laravel\Users\Listeners\Auth;

use Illuminate\Auth\Events\Login;

class HandleUserLogin
{
    public function handle(Login $event)
    {
        /**
         * @var \Backstage\Laravel\Users\Eloquent\Models\User $user
         */
        $user = $event->user;

        $inputs = request()->except('_method', '_token', 'password');

        $user->logins()->create([
            'user_id' => $user->id,
            'type' => 'login',
            'url' => request()->url(),
            'referrer' => request()->server('HTTP_REFERER'),
            'inputs' => count($inputs) ? json_encode($inputs) : null,
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'ip_address' => request()->ip(),
            'hostname' => gethostbyaddr(request()->ip()),
        ]);
    }
}
