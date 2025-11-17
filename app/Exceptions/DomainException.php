<?php

namespace App\Exceptions;

use Exception;

class DomainException extends Exception
{
    protected array $errors = [];

    public function __construct(string $message = '', int $code = 422, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
