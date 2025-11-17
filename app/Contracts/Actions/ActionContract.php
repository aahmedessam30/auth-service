<?php

namespace App\Contracts\Actions;

interface ActionContract
{
    /**
     * Execute the action with validation pipeline.
     */
    public function execute(mixed $dto): mixed;
}
