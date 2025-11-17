<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contracts\Repositories\TokenBlacklistRepositoryContract;
use App\Contracts\Security\JwtVerifierContract;
use App\Exceptions\ApiException;
use App\Models\User;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    use ApiResponse;

    public function __construct(
        private readonly JwtVerifierContract $jwtVerifier,
        private readonly TokenBlacklistRepositoryContract $tokenBlacklistRepository
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $this->extractBearerToken($request);
            $payload = $this->jwtVerifier->verify($token);

            $this->checkBlacklist($payload);
            $this->verifyTokenVersion($payload);
            $this->attachPayloadToRequest($request, $payload);
            $this->setUserResolver($request, $payload);

            return $next($request);
        } catch (ApiException $e) {
            return $this->error($e->getMessage(), $e->getCode(), $e->getErrors());
        }
    }

    /**
     * Extract Bearer token from Authorization header.
     *
     * @throws ApiException
     */
    private function extractBearerToken(Request $request): string
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            throw new ApiException('Missing authorization token', 401);
        }

        $token = trim(substr($header, 7));

        if (empty($token)) {
            throw new ApiException('Invalid authorization token', 401);
        }

        return $token;
    }

    /**
     * Attach JWT payload to request attributes.
     */
    private function attachPayloadToRequest(Request $request, array $payload): void
    {
        $request->attributes->set('jwt_payload', $payload);
        $request->attributes->set('user_id', $payload['sub'] ?? null);
    }

    /**
     * Set user resolver for authenticated user.
     */
    private function setUserResolver(Request $request, array $payload): void
    {
        $userId = $payload['sub'] ?? null;

        $request->setUserResolver(fn () => $userId ? User::find($userId) : null);
    }

    /**
     * Check if token is blacklisted.
     *
     * @throws ApiException
     */
    private function checkBlacklist(array $payload): void
    {
        $jti = $payload['jti'] ?? null;

        if (! $jti) {
            throw new ApiException('Token missing JTI claim', 401);
        }

        if ($this->tokenBlacklistRepository->isBlacklisted($jti)) {
            throw new ApiException('Token has been revoked', 401);
        }
    }

    /**
     * Verify token version matches user's current version.
     *
     * @throws ApiException
     */
    private function verifyTokenVersion(array $payload): void
    {
        $userId = $payload['sub'] ?? null;
        $tokenVersion = $payload['token_version'] ?? null;

        if (! $userId || $tokenVersion === null) {
            return;
        }

        $user = User::find($userId);

        if (! $user) {
            throw new ApiException('User not found', 404);
        }

        if ($user->token_version !== $tokenVersion) {
            throw new ApiException('Token version mismatch - please login again', 401);
        }
    }
}
