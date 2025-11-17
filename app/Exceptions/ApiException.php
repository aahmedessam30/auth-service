<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected array $errors = [];

    public function __construct(string $message = '', int $code = 400, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
