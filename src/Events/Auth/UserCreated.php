<?php

namespace Backstage\LaravelUsers\Events\Auth;

use Backstage\LaravelUsers\Eloquent\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * The user instance.
     *
     * @var \Backstage\LaravelUsers\Eloquent\Models\User
     */
    public function __construct(public User $user) {}
}
