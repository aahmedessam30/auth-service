<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\RefreshTokenAction;
use App\DTO\Auth\RefreshTokenDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Http\JsonResponse;

class RefreshTokenController extends BaseController
{
    public function __construct(
        private readonly RefreshTokenAction $refreshTokenAction
    ) {}

    /**
     * Refresh access token using refresh token.
     */
    public function __invoke(RefreshTokenRequest $request): JsonResponse
    {
        $dto = new RefreshTokenDTO(
            refreshToken: $request->input('refresh_token')
        );

        $result = $this->refreshTokenAction->execute($dto);

        return $this->success(
            data: new AuthResource($result),
            message: 'Token refreshed successfully'
        );
    }
}
