# Folder Structure

## Root Level

```
├── app/                    # Application code
├── bootstrap/              # Framework bootstrap files
├── config/                 # Configuration files
├── database/               # Migrations, factories, seeders
├── docker/                 # Docker configuration
├── docs/                   # Documentation
├── public/                 # Public web root
├── resources/              # Views, assets
├── routes/                 # Route definitions
├── storage/                # Logs, cache, uploads
├── tests/                  # Test files
├── vendor/                 # Composer dependencies
├── .env.example            # Environment variables template
├── .githooks/              # Git hooks
├── composer.json           # PHP dependencies
├── docker-compose.yml      # Docker compose configuration
├── Makefile                # Build automation
├── phpstan.neon            # PHPStan configuration
├── pint.json               # Laravel Pint configuration
└── README.md               # Project documentation
```

## Application Structure (`app/`)

### `app/Actions/`

**Purpose:** Use-case implementations (business operations)

**Contents:**

-   Action classes that implement specific use cases
-   Each action represents a single business operation
-   Implements action contracts from `app/Contracts/Actions/`

**Example:**

```php
YourAction.php
CreateOrderAction.php
ProcessPaymentAction.php
```

### `app/Clients/`

**Purpose:** HTTP clients for external service communication

**Contents:**

-   HTTP client wrappers
-   External API integrations
-   Implements `HttpClientContract`

**Example:**

```php
ServiceHttpClient.php
PaymentGatewayClient.php
NotificationServiceClient.php
```

### `app/Contracts/`

**Purpose:** Interface definitions (PSR-4 contracts)

**Sub-directories:**

-   `Actions/` - Action interfaces
-   `Services/` - Service interfaces
-   `Repositories/` - Repository interfaces
-   `Clients/` - HTTP client interfaces
-   `Events/` - Event publisher interfaces

**Why:** Enables dependency injection, testability, and loose coupling

### `app/DTO/`

**Purpose:** Data Transfer Objects

**Contents:**

-   Immutable data containers
-   Type-safe data structures
-   Used for passing data between layers

**Example:**

```php
BaseDTO.php
YourDTO.php
CreateOrderDTO.php
```

### `app/Events/`

**Purpose:** Domain events and event publishing

**Contents:**

-   `BaseDomainEvent.php` - Base event class
-   Domain event classes
-   `Publishers/` - Event publisher implementations

**Example:**

```php
Events/
├── BaseDomainEvent.php
├── ExampleDomainEvent.php
├── OrderCreatedEvent.php
└── Publishers/
    └── RabbitMQPublisherStub.php
```

### `app/Exceptions/`

**Purpose:** Custom exceptions and exception handling

**Contents:**

-   `ApiExceptionHandler.php` - API exception handling logic
-   `ApiException.php` - API-specific exceptions
-   `DomainException.php` - Business logic exceptions

**Note:** Exception handling is registered in `bootstrap/app.php` following Laravel 12 standards.

### `app/Http/`

**Purpose:** HTTP layer (controllers, middleware)

**Sub-directories:**

-   `Controllers/` - Request handlers
    -   `BaseController.php` - Base controller with ApiResponse trait
    -   Feature controllers
-   `Middleware/` - Request/response middleware
    -   `CorrelationIdMiddleware.php` - Correlation ID handling

### `app/Models/`

**Purpose:** Eloquent models (database entities)

**Contents:**

-   Database model classes
-   Model relationships
-   Accessors/Mutators
-   Scopes

### `app/Providers/`

**Purpose:** Service providers

**Contents:**

-   `AppServiceProvider.php` - Application services
-   `ResponseMacroServiceProvider.php` - Response macros
-   Custom service providers

### `app/Repositories/`

**Purpose:** Data access layer

**Contents:**

-   `BaseRepository.php` - Base repository implementation
-   Concrete repository classes
-   Database query encapsulation

**Example:**

```php
BaseRepository.php
UserRepository.php
OrderRepository.php
```

### `app/Services/`

**Purpose:** Business logic services

**Contents:**

-   `BaseService.php` - Base service class
-   Business logic that spans multiple repositories
-   Complex domain operations

**Example:**

```php
BaseService.php
OrderProcessingService.php
PaymentService.php
```

### `app/Traits/`

**Purpose:** Reusable traits

**Contents:**

-   `ApiResponse.php` - Response formatting trait
-   Other reusable behaviors

## Configuration (`config/`)

### `config/service.php`

Service-specific configuration (name, version)

### Other Configs

-   `app.php` - Application settings
-   `database.php` - Database connections
-   `cache.php` - Cache configuration
-   `queue.php` - Queue configuration

## Docker (`docker/`)

```
docker/
├── Dockerfile              # Multi-stage PHP-FPM image
└── nginx/
    └── conf.d/
        └── default.conf    # Nginx configuration
```

## Documentation (`docs/`)

```
docs/
├── architecture.md         # Architecture overview
├── folder-structure.md     # This file
└── how-to-create-new-service.md
```

## Routes (`routes/`)

### `routes/api.php`

API routes (health, version, business endpoints)

### `routes/console.php`

Artisan console commands

### `routes/web.php`

Web routes (if needed)

## Tests (`tests/`)

```
tests/
├── Feature/               # Feature tests
├── Unit/                  # Unit tests
└── TestCase.php          # Base test class
```

## Storage (`storage/`)

```
storage/
├── app/                   # Application storage
│   ├── private/          # Private files
│   └── public/           # Public files
├── framework/            # Framework cache
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/                 # Application logs
```

## Development Tools

### `.githooks/`

Git hooks for pre-commit checks

### `pint.json`

Laravel Pint code formatter configuration

### `phpstan.neon`

PHPStan static analysis configuration

## Key Files

### `.env.example`

Environment variables template

### `composer.json`

PHP dependencies and autoload configuration

### `docker-compose.yml`

Multi-container Docker setup

### `Makefile`

Build automation and common commands

### `README.md`

Project overview and quick start guide
