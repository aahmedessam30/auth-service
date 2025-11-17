<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Actions\BaseAction;
use App\Contracts\Services\AuthServiceContract;
use App\DTO\Auth\RefreshTokenDTO;

class RefreshTokenAction extends BaseAction
{
    public function __construct(
        private readonly AuthServiceContract $authService
    ) {}

    /**
     * Execute the refresh token action.
     */
    protected function handle(mixed $dto): mixed
    {
        /** @var RefreshTokenDTO $dto */
        return $this->authService->refreshAccessToken($dto->refreshToken);
    }
}
