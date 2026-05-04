<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Backstage\Laravel\Users\Domain\Email\Exceptions\EmailChangeException;
use Backstage\Laravel\Users\Eloquent\Models\User;
use Backstage\Laravel\Users\Events\Email\EmailChangeCancelled;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelEmailChange
{
    use AsAction;

    public function handle(User $user, string $rawToken): void
    {
        if ($user->pending_email === null || $user->pending_email_token === null) {
            throw EmailChangeException::noPendingChange();
        }

        if (! hash_equals((string) $user->pending_email_token, hash('sha256', $rawToken))) {
            throw EmailChangeException::tokenInvalid();
        }

        $abandonedEmail = (string) $user->pending_email;

        $user->forceFill([
            'pending_email' => null,
            'pending_email_token' => null,
            'pending_email_token_expires_at' => null,
            'pending_email_requested_at' => null,
        ])->save();

        EmailChangeCancelled::dispatch($user->fresh(), $abandonedEmail);
    }
}
