<?php

namespace Backstage\Laravel\Users\Eloquent\Observers;

use Backstage\Laravel\Users\Events\Auth\UserCreated;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \Backstage\Laravel\Users\Eloquent\Models\User  $user
     * @return void
     */
    public function created($user)
    {
        event(new UserCreated($user));
    }
}
