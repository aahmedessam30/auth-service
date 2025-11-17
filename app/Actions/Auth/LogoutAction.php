<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Actions\BaseAction;
use App\Contracts\Services\AuthServiceContract;

class LogoutAction extends BaseAction
{
    public function __construct(
        private readonly AuthServiceContract $authService
    ) {}

    /**
     * Execute the logout action.
     */
    protected function handle(mixed $data): mixed
    {
        $this->authService->logoutUser(
            jti: $data['jti'],
            userId: $data['user_id'],
            expiresAt: $data['exp']
        );

        return null;
    }
}
