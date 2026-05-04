<?php

namespace Backstage\Laravel\Users\Events\Email;

use Backstage\Laravel\Users\Eloquent\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailChangeInitiated
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $newEmail,
        public string $rawToken,
        public int $expiresInMinutes,
        public ?string $source = null,
    ) {}
}
