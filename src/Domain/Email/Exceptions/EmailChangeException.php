<?php

namespace Backstage\Laravel\Users\Domain\Email\Exceptions;

use RuntimeException;

class EmailChangeException extends RuntimeException
{
    public static function sameAsCurrent(): self
    {
        return new self((string) __('The new email address is identical to the current one.'));
    }

    public static function invalidFormat(): self
    {
        return new self((string) __('The new email address has an invalid format.'));
    }

    public static function alreadyTaken(): self
    {
        return new self((string) __('The new email address is already in use.'));
    }

    public static function cooldownActive(int $secondsRemaining): self
    {
        return new self((string) __('An email change was requested recently; try again in :seconds seconds.', ['seconds' => $secondsRemaining]));
    }

    public static function tokenInvalid(): self
    {
        return new self((string) __('The email change token is invalid.'));
    }

    public static function tokenExpired(): self
    {
        return new self((string) __('The email change token has expired.'));
    }

    public static function noPendingChange(): self
    {
        return new self((string) __('No pending email change exists for this user.'));
    }
}
