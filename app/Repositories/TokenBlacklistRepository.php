<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TokenBlacklistRepositoryContract;
use App\Models\TokenBlacklist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TokenBlacklistRepository extends BaseRepository implements TokenBlacklistRepositoryContract
{
    private const CACHE_PREFIX = 'token_blacklist:';

    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(TokenBlacklist $tokenBlacklist)
    {
        $this->model = $tokenBlacklist;
    }

    /**
     * Add a token to the blacklist.
     */
    public function blacklistToken(string $jti, int $userId, Carbon $expiresAt, string $reason = 'logout'): TokenBlacklist
    {
        $blacklistedToken = $this->model->create([
            'jti' => $jti,
            'user_id' => $userId,
            'expires_at' => $expiresAt,
            'blacklisted_at' => now(),
            'reason' => $reason,
        ]);

        // Cache the blacklist entry
        $cacheKey = self::CACHE_PREFIX.$jti;
        $ttl = $expiresAt->diffInSeconds(now());
        Cache::put($cacheKey, true, min($ttl, self::CACHE_TTL));

        return $blacklistedToken;
    }

    /**
     * Check if a token is blacklisted.
     */
    public function isBlacklisted(string $jti): bool
    {
        $cacheKey = self::CACHE_PREFIX.$jti;

        // Check cache first
        if (Cache::has($cacheKey)) {
            return true;
        }

        // Check database
        $exists = $this->model->where('jti', $jti)->exists();

        if ($exists) {
            // Cache for future checks
            Cache::put($cacheKey, true, self::CACHE_TTL);
        }

        return $exists;
    }

    /**
     * Remove expired tokens from the blacklist.
     */
    public function cleanupExpired(): int
    {
        return $this->model->where('expires_at', '<', now())->delete();
    }

    /**
     * Blacklist all tokens for a user.
     */
    public function blacklistAllUserTokens(int $userId, string $reason = 'security'): int
    {
        // This is a placeholder - in reality, we can't blacklist tokens we don't know about
        // This would be used in conjunction with incrementing token_version
        return 0;
    }
}
