<?php

declare(strict_types=1);

namespace App\Contracts\Security;

interface JwtVerifierContract
{
    /**
     * Verify and decode a JWT token.
     *
     * @param  string  $jwt  The JWT token to verify
     * @return array The decoded JWT payload
     *
     * @throws \App\Exceptions\ApiException When verification fails
     */
    public function verify(string $jwt): array;
}
