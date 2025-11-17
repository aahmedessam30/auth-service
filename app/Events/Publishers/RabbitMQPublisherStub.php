<?php

namespace App\Events\Publishers;

use App\Contracts\Events\EventPublisherContract;
use Illuminate\Support\Facades\Log;

class RabbitMQPublisherStub implements EventPublisherContract
{
    /**
     * Publish an event (stub implementation).
     *
     * In production, this would connect to RabbitMQ and publish the event.
     * For now, it just logs the event.
     */
    public function publish(string $eventName, array $payload): void
    {
        Log::info('Event Published (Stub)', [
            'event_name' => $eventName,
            'payload' => $payload,
            'correlation_id' => $payload['correlation_id'] ?? null,
        ]);

        // TODO: Implement actual RabbitMQ publishing logic
    }
}
