<?php

namespace App\Actions;

use App\Contracts\Actions\ActionContract;

abstract class BaseAction implements ActionContract
{
    /**
     * Execute the action with validation pipeline.
     */
    final public function execute(mixed $dto): mixed
    {
        $this->validate($dto);

        return $this->handle($dto);
    }

    /**
     * Validate the input data.
     * Override this method in child classes if validation is needed.
     */
    protected function validate(mixed $dto): void
    {
        // Default: no validation
        // Child classes can override this for custom validation logic
    }

    /**
     * Handle the action logic.
     * Must be implemented by child classes.
     */
    abstract protected function handle(mixed $dto): mixed;
}
