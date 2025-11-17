<?php

declare(strict_types=1);

namespace App\Contracts\Security;

interface JwtKeyLoaderContract
{
    /**
     * Load the private key from the configured path.
     *
     * @throws \App\Exceptions\ApiException
     */
    public function loadPrivateKey(): string;

    /**
     * Load the public key from the configured path.
     *
     * @throws \App\Exceptions\ApiException
     */
    public function loadPublicKey(): string;

    /**
     * Get the primary algorithm for JWT operations.
     */
    public function getAlgorithm(): string;
}
