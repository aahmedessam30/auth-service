<?php

declare(strict_types=1);

namespace App\Contracts\Security;

interface JwtSignerContract
{
    /**
     * Generate a JWT token from payload data.
     *
     * @throws \App\Exceptions\ApiException
     */
    public function generateToken(array $payload): string;
}
