<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\RefreshTokenRepositoryContract;
use App\Contracts\Repositories\TokenBlacklistRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Security\JwtSignerContract;
use App\Contracts\Services\AuthServiceContract;
use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Events\UserRegisteredEvent;
use App\Exceptions\ApiException;
use App\Models\User;
use Carbon\Carbon;

class AuthService implements AuthServiceContract
{
    private const REFRESH_TOKEN_TTL_DAYS = 30;

    public function __construct(
        private readonly UserRepositoryContract $userRepository,
        private readonly JwtSignerContract $jwtSigner,
        private readonly TokenBlacklistRepositoryContract $tokenBlacklistRepository,
        private readonly RefreshTokenRepositoryContract $refreshTokenRepository
    ) {}

    /**
     * Register a new user.
     *
     * @throws ApiException
     */
    public function registerUser(RegisterDTO $dto): array
    {
        if ($this->userRepository->findByEmail($dto->email)) {
            throw new ApiException('User with this email already exists', 409);
        }

        $user = $this->userRepository->createUser([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);

        event(new UserRegisteredEvent(
            id: $user->id,
            email: $user->email,
            name: $user->name,
            correlationId: request()->get('correlationId')
        ));

        $token = $this->generateJwtToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * Login a user and generate JWT token.
     *
     * @throws ApiException
     */
    public function loginUser(LoginDTO $dto): array
    {
        $user = $this->userRepository->verifyCredentials($dto->email, $dto->password);

        if (! $user) {
            throw new ApiException('Invalid credentials', 401);
        }

        $token = $this->generateJwtToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * Generate JWT token for a user.
     */
    public function generateJwtToken(User $user): string
    {
        $payload = [
            'sub' => (string) $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'token_version' => $user->token_version ?? 1,
        ];

        return $this->jwtSigner->generateToken($payload);
    }

    /**
     * Get user profile by ID.
     */
    public function getUserProfile(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * Logout a user by blacklisting their token.
     */
    public function logoutUser(string $jti, int $userId, int $expiresAt): void
    {
        $this->tokenBlacklistRepository->blacklistToken(
            $jti,
            $userId,
            Carbon::createFromTimestamp($expiresAt),
            'logout'
        );

        // Delete all refresh tokens for the user
        $this->refreshTokenRepository->deleteAllForUser($userId);
    }

    /**
     * Generate refresh token for a user.
     */
    public function generateRefreshToken(User $user): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = now()->addDays(self::REFRESH_TOKEN_TTL_DAYS);

        $this->refreshTokenRepository->createRefreshToken(
            $user->id,
            $token,
            $expiresAt,
            request()->ip(),
            request()->userAgent()
        );

        return $token;
    }

    /**
     * Generate a new access token using a refresh token.
     *
     * @throws ApiException
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        $token = $this->refreshTokenRepository->findByToken($refreshToken);

        if (! $token) {
            throw new ApiException('Invalid refresh token', 401);
        }

        if ($token->isExpired()) {
            $this->refreshTokenRepository->deleteByToken($refreshToken);

            throw new ApiException('Refresh token has expired', 401);
        }

        $user = $token->user;

        if (! $user) {
            throw new ApiException('User not found', 404);
        }

        // Mark the refresh token as used
        $token->markAsUsed();

        // Generate new access token
        $accessToken = $this->generateJwtToken($user);

        return [
            'user' => $user,
            'token' => $accessToken,
        ];
    }
}
