<?php

declare(strict_types=1);

namespace App\Security;

use App\Contracts\Security\JwtSignerContract;
use App\Exceptions\ApiException;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;

class JwtSigner extends BaseJwtHandler implements JwtSignerContract
{
    private int $ttl;

    public function __construct()
    {
        parent::__construct();
        $this->ttl = config('jwt.ttl', 3600);
    }

    /**
     * Generate a JWT token from payload data.
     *
     *
     * @throws ApiException
     */
    public function generateToken(array $payload): string
    {
        $privateKey = $this->loadPrivateKey();

        $issuedAt = time();
        $expiresAt = $issuedAt + $this->ttl;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'jti' => (string) Str::uuid(),
            'service_name' => config('service.service_name', 'auth-service'),
        ]);

        if (request()->has('correlationId')) {
            $tokenPayload['correlation_id'] = request()->get('correlationId');
        }

        return JWT::encode($tokenPayload, $privateKey, $this->getAlgorithm());
    }
}
