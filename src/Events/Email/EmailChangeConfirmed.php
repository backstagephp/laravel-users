<?php

namespace Backstage\Laravel\Users\Events\Email;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailChangeConfirmed
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $oldEmail,
    ) {}
}
