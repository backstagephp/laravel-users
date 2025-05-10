<?php

namespace Backstage\LaravelUsers\Eloquent\Observers;

use Backstage\LaravelUsers\Events\Auth\UserCreated;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \Backstage\LaravelUsers\Eloquent\Models\User  $user
     * @return void
     */
    public function created($user)
    {
        event(new UserCreated($user));
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \Backstage\LaravelUsers\Eloquent\Models\User  $user
     * @return void
     */
    public function updated($user)
    {
        // Logic to handle after a user is updated
    }
}
