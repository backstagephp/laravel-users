<?php

namespace Backstage\Laravel\Users\Events\Auth;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
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
     * @var User
     */
    public function __construct(public Model|User $user) {}
}
