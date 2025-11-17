<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\LoginAction;
use App\DTO\Auth\LoginDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Http\JsonResponse;

class LoginController extends BaseController
{
    public function __construct(
        private readonly LoginAction $loginAction
    ) {}

    /**
     * Login a user.
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $dto = new LoginDTO(
            email: $request->input('email'),
            password: $request->input('password'),
        );

        $result = $this->loginAction->execute($dto);

        return $this->success(
            data: new AuthResource($result),
            message: 'Login successful'
        );
    }
}
