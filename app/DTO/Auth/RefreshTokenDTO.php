<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\DTO\BaseDTO;

class RefreshTokenDTO extends BaseDTO
{
    public function __construct(
        public readonly string $refreshToken
    ) {}
}
