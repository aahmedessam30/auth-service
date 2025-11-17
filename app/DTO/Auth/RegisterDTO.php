<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\DTO\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
