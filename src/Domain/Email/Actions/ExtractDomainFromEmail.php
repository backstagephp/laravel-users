<?php

namespace Backstage\Laravel\Users\Domain\Email\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ExtractDomainFromEmail
{
    use AsAction;

    public function handle(string $email): string
    {
        return $this->extractDomain($email);
    }

    protected function extractDomain(string $email): string
    {
        return substr(strrchr($email, '@'), 1);
    }
}
