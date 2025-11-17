<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\TokenBlacklist;
use Carbon\Carbon;

interface TokenBlacklistRepositoryContract
{
    /**
     * Add a token to the blacklist.
     */
    public function blacklistToken(string $jti, int $userId, Carbon $expiresAt, string $reason = 'logout'): TokenBlacklist;

    /**
     * Check if a token is blacklisted.
     */
    public function isBlacklisted(string $jti): bool;

    /**
     * Remove expired tokens from the blacklist.
     */
    public function cleanupExpired(): int;

    /**
     * Blacklist all tokens for a user.
     */
    public function blacklistAllUserTokens(int $userId, string $reason = 'security'): int;
}
