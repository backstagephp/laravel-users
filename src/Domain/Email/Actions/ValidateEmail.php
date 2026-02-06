<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ValidateEmail
{
    use AsAction;

    public function handle(string | array $email): bool | array
    {
        if (is_array($email)) {
            return $this->validateMultipleEmails($email);
        }

        return $this->validateSingleEmail($email);
    }

    protected function validateMultipleEmails(array $emails): array
    {
        return collect($emails)
            ->mapWithKeys(fn (string $email) => [$email => $this->validateSingleEmail($email)])
            ->toArray();
    }

    protected function validateSingleEmail(string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        if (! $this->isValidFormat($email)) {
            return false;
        }

        if (! $this->hasValidDns($email)) {
            return false;
        }

        return true;
    }

    protected function isValidFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function hasValidDns(string $email): bool
    {
        $domain = $this->getDomainFromEmail($email);

        return checkdnsrr($domain, 'MX');
    }

    protected function getDomainFromEmail(string $email): string
    {
        return substr(strrchr($email, '@'), 1);
    }
}
