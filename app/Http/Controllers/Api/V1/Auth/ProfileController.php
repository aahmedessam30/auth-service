<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Contracts\Services\AuthServiceContract;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    public function __construct(
        private readonly AuthServiceContract $authService
    ) {}

    /**
     * Get authenticated user profile.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->attributes->get('user_id');

        $user = $this->authService->getUserProfile((int) $userId);

        if (! $user) {
            return $this->error('User not found', 404);
        }

        return $this->success(
            data: new UserResource($user),
            message: 'Profile retrieved successfully'
        );
    }
}
