<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\RefreshTokenRepositoryContract;
use App\Models\RefreshToken;
use Carbon\Carbon;

class RefreshTokenRepository extends BaseRepository implements RefreshTokenRepositoryContract
{
    public function __construct(RefreshToken $refreshToken)
    {
        $this->model = $refreshToken;
    }

    /**
     * Create a new refresh token.
     */
    public function createRefreshToken(int $userId, string $token, Carbon $expiresAt, ?string $ipAddress = null, ?string $userAgent = null): RefreshToken
    {
        return $this->model->create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Find a refresh token by token string.
     */
    public function findByToken(string $token): ?RefreshToken
    {
        return $this->model->where('token', $token)->first();
    }

    /**
     * Delete a refresh token by token string.
     */
    public function deleteByToken(string $token): bool
    {
        return $this->model->where('token', $token)->delete() > 0;
    }

    /**
     * Delete all refresh tokens for a user.
     */
    public function deleteAllForUser(int $userId): int
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    /**
     * Delete expired refresh tokens.
     */
    public function deleteExpired(): int
    {
        return $this->model->where('expires_at', '<', now())->delete();
    }

    /**
     * Get all active refresh tokens for a user.
     */
    public function getActiveTokensForUser(int $userId): array
    {
        return $this->model->where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
