<?php

declare(strict_types=1);

namespace App\Events;

class UserRegisteredEvent extends BaseDomainEvent
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        string $correlationId = ''
    ) {
        parent::__construct();
        $this->correlationId = $correlationId ?: request()->get('correlationId');
    }

    /**
     * Get event payload for publishing.
     */
    public function getPayload(): array
    {
        return [
            'event_type' => 'user.registered',
            'user_id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'correlation_id' => $this->correlationId,
            'timestamp' => now()->toIso8601String(),
            'service' => config('service.service_name'),
        ];
    }
}
