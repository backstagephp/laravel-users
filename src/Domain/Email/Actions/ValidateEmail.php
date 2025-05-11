<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ValidateEmail
{
    use AsAction;

    public function handle(string|array $email): bool|array
    {
        if (is_array($email)) {
            if (count($email) == 1) {
                $email = $email[0];
            } elseif (count($email) > 1) {
                return collect($email)->mapWithKeys(fn ($e) => [$e => $this->validateSingleEmail($e)])->toArray();
            }
        }

        return $this->validateSingleEmail($email);
    }

    protected function validateSingleEmail(string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        if (! $this->validateEmail($email)) {
            return false;
        }

        return true;
    }

    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && $this->checkDnsrr($email);
    }

    protected function checkDnsrr(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);

        return checkdnsrr($domain, 'MX');
    }
}
