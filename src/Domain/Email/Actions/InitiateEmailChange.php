<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Backstage\Laravel\Users\Domain\Email\Exceptions\EmailChangeException;
use Backstage\Laravel\Users\Eloquent\Models\User;
use Backstage\Laravel\Users\Events\Email\EmailChangeInitiated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class InitiateEmailChange
{
    use AsAction;

    public function handle(User $user, string $newEmail, ?string $source = null): string
    {
        $newEmail = mb_strtolower(trim($newEmail));

        $this->guardFormat($newEmail);
        $this->guardNotIdentical($user, $newEmail);
        $this->guardCooldown($user);

        $rawToken = Str::random(64);
        $expiresInMinutes = (int) config('users.email_change.token_lifetime_minutes', 60 * 24);

        DB::transaction(function () use ($user, $newEmail, $rawToken, $expiresInMinutes) {
            $this->guardUniqueness($user, $newEmail);

            $user->forceFill([
                'pending_email' => $newEmail,
                'pending_email_token' => hash('sha256', $rawToken),
                'pending_email_token_expires_at' => now()->addMinutes($expiresInMinutes),
                'pending_email_requested_at' => now(),
            ])->save();
        });

        EmailChangeInitiated::dispatch(
            $user->fresh(),
            $newEmail,
            $rawToken,
            $expiresInMinutes,
            $source,
        );

        return $rawToken;
    }

    protected function guardFormat(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw EmailChangeException::invalidFormat();
        }
    }

    protected function guardNotIdentical(User $user, string $newEmail): void
    {
        if (mb_strtolower((string) $user->email) === $newEmail) {
            throw EmailChangeException::sameAsCurrent();
        }
    }

    protected function guardCooldown(User $user): void
    {
        $cooldownMinutes = (int) config('users.email_change.cooldown_minutes', 5);

        if ($cooldownMinutes <= 0 || $user->pending_email_requested_at === null) {
            return;
        }

        $unlocksAt = $user->pending_email_requested_at->copy()->addMinutes($cooldownMinutes);

        if ($unlocksAt->isFuture()) {
            throw EmailChangeException::cooldownActive($unlocksAt->diffInSeconds(now()));
        }
    }

    protected function guardUniqueness(User $user, string $newEmail): void
    {
        $userClass = config('auth.providers.users.model', User::class);

        $exists = $userClass::query()
            ->where('id', '!=', $user->getKey())
            ->where(function ($query) use ($newEmail) {
                $query->whereRaw('LOWER(email) = ?', [$newEmail])
                    ->orWhereRaw('LOWER(pending_email) = ?', [$newEmail]);
            })
            ->exists();

        if ($exists) {
            throw EmailChangeException::alreadyTaken();
        }
    }
}
