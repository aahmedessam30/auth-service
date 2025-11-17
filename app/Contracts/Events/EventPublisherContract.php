<?php

namespace App\Contracts\Events;

interface EventPublisherContract
{
    /**
     * Publish an event.
     */
    public function publish(string $eventName, array $payload): void;
}
