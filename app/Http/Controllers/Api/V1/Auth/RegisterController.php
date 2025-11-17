<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\RegisterAction;
use App\DTO\Auth\RegisterDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    public function __construct(
        private readonly RegisterAction $registerAction
    ) {}

    /**
     * Register a new user.
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $dto = new RegisterDTO(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
        );

        $result = $this->registerAction->execute($dto);

        return $this->success(
            data: new AuthResource($result),
            message: 'User registered successfully',
            code: 201
        );
    }
}
