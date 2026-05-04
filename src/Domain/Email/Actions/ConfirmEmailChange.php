<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Backstage\Laravel\Users\Domain\Email\Exceptions\EmailChangeException;
use Backstage\Laravel\Users\Eloquent\Models\User;
use Backstage\Laravel\Users\Events\Email\EmailChangeConfirmed;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ConfirmEmailChange
{
    use AsAction;

    public function handle(User $user, string $rawToken): User
    {
        if ($user->pending_email === null || $user->pending_email_token === null) {
            throw EmailChangeException::noPendingChange();
        }

        if (! hash_equals((string) $user->pending_email_token, hash('sha256', $rawToken))) {
            throw EmailChangeException::tokenInvalid();
        }

        if ($user->pending_email_token_expires_at !== null && $user->pending_email_token_expires_at->isPast()) {
            throw EmailChangeException::tokenExpired();
        }

        $oldEmail = (string) $user->email;
        $newEmail = (string) $user->pending_email;

        DB::transaction(function () use ($user, $newEmail) {
            $userClass = config('auth.providers.users.model', User::class);

            $taken = $userClass::query()
                ->where('id', '!=', $user->getKey())
                ->whereRaw('LOWER(email) = ?', [mb_strtolower($newEmail)])
                ->exists();

            if ($taken) {
                throw EmailChangeException::alreadyTaken();
            }

            $user->forceFill([
                'email' => $newEmail,
                'email_verified_at' => now(),
                'pending_email' => null,
                'pending_email_token' => null,
                'pending_email_token_expires_at' => null,
                'pending_email_requested_at' => null,
            ])->save();
        });

        EmailChangeConfirmed::dispatch($user->fresh(), $oldEmail);

        return $user;
    }
}
