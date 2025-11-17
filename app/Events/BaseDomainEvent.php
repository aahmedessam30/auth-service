<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class BaseDomainEvent
{
    use Dispatchable;
    use SerializesModels;

    public string $correlationId;

    public string $occurredAt;

    public function __construct()
    {
        $this->correlationId = request()->correlationId ?? request()->header('X-Correlation-ID') ?? '';
        $this->occurredAt = now()->toIso8601String();
    }

    /**
     * Get the event payload.
     */
    abstract public function getPayload(): array;
}
