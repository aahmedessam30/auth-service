# Architecture Overview

## Introduction

This Laravel microservice template follows **Clean Architecture** principles and **Microservices best practices** to provide a production-ready, scalable, and maintainable foundation for building microservices.

## Architectural Layers

### 1. Contracts Layer (`app/Contracts/`)

The Contracts layer defines **interfaces** (contracts) for all major components. This follows the **Dependency Inversion Principle** and allows for easy testing and swapping of implementations.

**Sub-directories:**

-   `Actions/` - Action contracts (use-case interfaces)
-   `Services/` - Service contracts (business logic interfaces)
-   `Repositories/` - Repository contracts (data access interfaces)
-   `Clients/` - HTTP client contracts
-   `Events/` - Event publisher contracts

**Benefits:**

-   Loose coupling between layers
-   Easy mocking for unit tests
-   Flexibility to swap implementations

### 2. DTO Layer (`app/DTO/`)

**Data Transfer Objects** are immutable objects used to transfer data between layers.

**Key Features:**

-   Type-safe data structures
-   Validation at boundaries
-   Serialization support
-   Extends `BaseDTO` for common functionality

**Example:**

```php
$dto = new YourDTO(data: 'value');
$array = $dto->toArray();
```

### 3. Actions Layer (`app/Actions/`)

Actions represent **use cases** in the application. Each action encapsulates a single business operation.

**Characteristics:**

-   One action = one use case
-   Accepts DTOs as input
-   Returns arrays or DTOs
-   Calls services and repositories
-   Implements `ActionContract`

**Flow:**

```
Controller → Action → Service/Repository → Response
```

### 4. Services Layer (`app/Services/`)

Services contain **business logic** that doesn't belong to a single entity. They orchestrate complex operations across multiple repositories or external services.

**Responsibilities:**

-   Complex business logic
-   Orchestration of multiple repositories
-   External API integrations
-   Domain rules enforcement

### 5. Repositories Layer (`app/Repositories/`)

Repositories provide **data access abstraction**. They encapsulate database queries and data persistence logic.

**Features:**

-   Extends `BaseRepository`
-   Implements `RepositoryContract`
-   CRUD operations
-   Query building
-   Pagination support

### 6. Clients Layer (`app/Clients/`)

HTTP clients for **external service communication**.

**Features:**

-   Automatic correlation ID attachment
-   Retry logic (3 attempts)
-   Error handling and formatting
-   Request/response logging

### 7. Events Layer (`app/Events/`)

Domain events for **event-driven architecture**.

**Components:**

-   `BaseDomainEvent` - Base class for all events
-   `Publishers/` - Event publisher implementations
-   Automatic correlation ID inclusion

## Response Format

All API responses follow a **standardized format**:

```json
{
  "success": true|false,
  "data": {},
  "message": "",
  "code": 200,
  "errors": [],
  "correlation_id": "uuid"
}
```

**Implementation:**

-   `ApiResponse` trait in controllers
-   Response macros: `response()->success()`, `response()->error()`
-   Unified exception handling

## Correlation ID Flow

Correlation IDs enable **distributed tracing** across microservices.

**Flow:**

1. Middleware generates/retrieves correlation ID
2. Stored in request object
3. Added to all log entries
4. Attached to outgoing HTTP requests
5. Included in response headers and body
6. Added to event payloads

**Benefits:**

-   Track requests across services
-   Debug distributed systems
-   Monitor request flow

## Error Handling

Centralized error handling using Laravel 12 standards:

-   Exception handling registered in `bootstrap/app.php` using `withExceptions()` callback
-   `ApiExceptionHandler` handles all API exception logic with specialized handlers
-   **Validation errors** → 422 with field errors
-   **ModelNotFoundException** → 404 formatted error
-   **ApiException** → Custom error with metadata
-   **DomainException** → Business logic errors
-   **Generic exceptions** → 500 with debug info (if enabled)
-   All responses include correlation ID for distributed tracing

## Event-Driven Integration

Events enable **loose coupling** between services:

1. **Domain Events** - Represent state changes
2. **Event Publishers** - Send events to message brokers
3. **Event Consumers** - Listen and react to events

**Example:**

```php
$event = new ExampleDomainEvent(eventData: 'test');
$publisher->publish('example.event', $event->getPayload());
```

## Security Considerations

-   **CORS** configured via middleware
-   **Rate limiting** on API routes
-   **Input validation** via Form Requests
-   **SQL injection protection** via Eloquent
-   **XSS protection** headers in Nginx
-   **Environment variables** for secrets

## Scalability Features

-   **Stateless design** - No session data
-   **Horizontal scaling** - Run multiple instances
-   **Queue support** - Async processing
-   **Cache layer** - Redis integration
-   **Database connection pooling**
-   **Microservice isolation**

## Testing Strategy

-   **Unit tests** - Test individual classes
-   **Feature tests** - Test complete workflows
-   **Integration tests** - Test external services
-   **Contract tests** - Verify interface implementations

## Monitoring & Observability

-   **Health endpoint** - `/api/health`
-   **Version endpoint** - `/api/version`
-   **Correlation IDs** - Request tracing
-   **Structured logging** - JSON logs
-   **APM integration** - Ready for New Relic, Datadog, etc.
