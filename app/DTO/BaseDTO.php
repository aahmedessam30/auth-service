<?php

namespace App\DTO;

use App\Contracts\DTO\BaseDataTransferObject;

abstract class BaseDTO implements BaseDataTransferObject
{
    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Create DTO from array.
     */
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}
