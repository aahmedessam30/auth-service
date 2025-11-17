<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\LogoutAction;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends BaseController
{
    public function __construct(
        private readonly LogoutAction $logoutAction
    ) {}

    /**
     * Logout the authenticated user.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->attributes->get('jwt_payload');

        $this->logoutAction->execute([
            'jti' => $payload['jti'],
            'user_id' => (int) $payload['sub'],
            'exp' => (int) $payload['exp'],
        ]);

        return $this->success(
            data: null,
            message: 'Logged out successfully'
        );
    }
}
