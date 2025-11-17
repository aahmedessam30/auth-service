<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\RefreshToken;
use Carbon\Carbon;

interface RefreshTokenRepositoryContract
{
    /**
     * Create a new refresh token.
     */
    public function createRefreshToken(int $userId, string $token, Carbon $expiresAt, ?string $ipAddress = null, ?string $userAgent = null): RefreshToken;

    /**
     * Find a refresh token by token string.
     */
    public function findByToken(string $token): ?RefreshToken;

    /**
     * Delete a refresh token by token string.
     */
    public function deleteByToken(string $token): bool;

    /**
     * Delete all refresh tokens for a user.
     */
    public function deleteAllForUser(int $userId): int;

    /**
     * Delete expired refresh tokens.
     */
    public function deleteExpired(): int;

    /**
     * Get all active refresh tokens for a user.
     */
    public function getActiveTokensForUser(int $userId): array;
}
