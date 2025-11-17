<?php

namespace App\Contracts\DTO;

interface BaseDataTransferObject
{
    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array;

    /**
     * Create DTO from array.
     */
    public static function fromArray(array $data): static;
}
