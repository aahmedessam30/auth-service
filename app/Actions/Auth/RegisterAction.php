<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Actions\BaseAction;
use App\Contracts\Services\AuthServiceContract;
use App\DTO\Auth\RegisterDTO;

class RegisterAction extends BaseAction
{
    public function __construct(
        private readonly AuthServiceContract $authService
    ) {}

    /**
     * Handle user registration.
     *
     * @param  RegisterDTO  $dto
     * @return array
     *
     * @throws \App\Exceptions\ApiException
     */
    protected function handle(mixed $dto): mixed
    {
        return $this->authService->registerUser($dto);
    }
}
