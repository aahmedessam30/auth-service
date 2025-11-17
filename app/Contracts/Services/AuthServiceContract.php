<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Models\User;

interface AuthServiceContract
{
    /**
     * Register a new user.
     */
    public function registerUser(RegisterDTO $dto): array;

    /**
     * Login a user and generate JWT token.
     */
    public function loginUser(LoginDTO $dto): array;

    /**
     * Generate JWT token for a user.
     */
    public function generateJwtToken(User $user): string;

    /**
     * Get user profile by ID.
     */
    public function getUserProfile(int $userId): ?User;

    /**
     * Logout a user by blacklisting their token.
     */
    public function logoutUser(string $jti, int $userId, int $expiresAt): void;

    /**
     * Generate a new access token using a refresh token.
     */
    public function refreshAccessToken(string $refreshToken): array;

    /**
     * Generate refresh token for a user.
     */
    public function generateRefreshToken(User $user): string;
}
