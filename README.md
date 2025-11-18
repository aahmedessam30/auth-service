# Auth Service - Laravel Microservice

<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
</p>

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel 12">
<img src="https://img.shields.io/badge/PHP-8.3-blue" alt="PHP 8.3">
<img src="https://img.shields.io/badge/Architecture-Clean-green" alt="Clean Architecture">
<img src="https://img.shields.io/badge/Docker-Ready-blue" alt="Docker">
<img src="https://img.shields.io/badge/JWT-RS256-orange" alt="JWT RS256">
<img src="https://img.shields.io/badge/License-MIT-green" alt="License">
</p>

## 🔐 Authentication Microservice

A **production-ready** authentication microservice built with **Laravel 12**, implementing **JWT RS256 token generation**, **user registration**, **login**, and **profile management** following **Clean Architecture** principles.

This service is the **central authentication authority** for your microservice ecosystem, issuing JWT tokens that other services can verify using the distributed public key.

---

## ✨ Features

### 🔐 Authentication Features

-   ✅ **User Registration** - Secure account creation with validation
-   ✅ **User Login** - Credential verification with JWT issuance
-   ✅ **JWT Token Generation** - RS256 asymmetric signing algorithm with JTI claims
-   ✅ **Profile Management** - Retrieve authenticated user information
-   ✅ **Token Revocation** - Blacklist-based token invalidation with Redis caching
-   ✅ **Refresh Tokens** - Long-lived tokens (30 days) for seamless token renewal
-   ✅ **Token Versioning** - Invalidate all user tokens on security events
-   ✅ **Logout Support** - Complete token invalidation (access + refresh tokens)
-   ✅ **Event-Driven** - UserRegisteredEvent for downstream processing

### 🏗️ Architecture

-   ✅ **Clean Architecture** - Separation of concerns with clear layer boundaries
-   ✅ **Action Pattern** - Use-case implementations (RegisterAction, LoginAction)
-   ✅ **Repository Pattern** - Data access abstraction (UserRepository)
-   ✅ **DTO Pattern** - Type-safe data transfer (RegisterDTO, LoginDTO)
-   ✅ **Form Request Validation** - Laravel validation with custom messages
-   ✅ **API Resources** - Consistent response transformation (UserResource, AuthResource)

### 🔧 Core Features

-   ✅ **Unified Response Format** - Standardized API responses
-   ✅ **Correlation ID Tracking** - Request tracing across services
-   ✅ **Comprehensive Exception Handling** - Centralized error management
-   ✅ **Health & Version Endpoints** - Service monitoring
-   ✅ **JWT Middleware** - Token verification with blacklist and version checks
-   ✅ **Redis Caching** - JWT public key, user profiles, and token blacklist
-   ✅ **Automated Cleanup** - Daily scheduled task for expired tokens

### 🐳 DevOps & Tools

-   ✅ **Docker & Docker Compose** - Containerized environment (nginx, mysql, redis, phpmyadmin)
-   ✅ **Makefile** - Simplified command execution
-   ✅ **Laravel Pint** - Code formatting
-   ✅ **PHPStan Level 6** - Static analysis
-   ✅ **PHPUnit Testing** - Comprehensive test suite

---

## 📋 Table of Contents

-   [Quick Start](#-quick-start)
-   [JWT Authentication](#-jwt-authentication)
-   [API Endpoints](#-api-endpoints)
-   [Project Structure](#-project-structure)
-   [Development](#-development)
-   [Testing](#-testing)
-   [Deployment](#-deployment)

---

## 🚀 Quick Start

### Prerequisites

-   Docker & Docker Compose
-   Git
-   Make (optional but recommended)

### Installation

```bash
# Clone the repository
git clone https://github.com/aahmedessam30/auth-service.git auth-service
cd auth-service

# Copy environment file
cp .env.example .env

# Generate JWT keys (4096-bit RSA)
make generate-keys

# Install dependencies and start containers (using Make)
make setup

# Or manually:
composer install
openssl genrsa -out keys/private.pem 4096
openssl rsa -in keys/private.pem -pubout -out keys/public.pem
docker-compose up -d --build
docker-compose exec app php artisan migrate
```

### Verify Installation

```bash
# Health check
curl http://localhost:8010/api/health

# Version info
curl http://localhost:8010/api/version

# Test registration
curl -X POST http://localhost:8010/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

---

## 🔐 JWT Authentication

This service implements **JWT RS256 token generation** as the central authentication authority for your microservice ecosystem.

### JWT Token Flow

```
┌──────────┐         ┌──────────────┐         ┌─────────────┐
│  Client  │         │ Auth Service │         │Other Service│
└────┬─────┘         └──────┬───────┘         └─────┬───────┘
     │                      │                        │
     │  1. Register/Login   │                        │
     │─────────────────────>│                        │
     │                      │                        │
     │  2. JWT Token        │                        │
     │<─────────────────────│                        │
     │                      │                        │
     │  3. Request + Token  │                        │
     │───────────────────────────────────────────────>│
     │                      │                        │
     │                      │  4. Verify with public key
     │                      │                        │
     │  5. Response         │                        │
     │<───────────────────────────────────────────────│
```

### JWT Token Structure

After successful authentication, the service issues a JWT containing:

```json
{
    "sub": "1",
    "email": "user@example.com",
    "name": "John Doe",
    "iat": 1700000000,
    "exp": 1700003600,
    "jti": "550e8400-e29b-41d4-a716-446655440000",
    "token_version": 1,
    "service_name": "auth-service",
    "correlation_id": "550e8400-e29b-41d4-a716-446655440001"
}
```

| Field            | Description                               |
| ---------------- | ----------------------------------------- |
| `sub`            | User ID (subject)                         |
| `email`          | User email address                        |
| `name`           | User display name                         |
| `iat`            | Issued at timestamp                       |
| `exp`            | Expiration timestamp                      |
| `jti`            | JWT ID (unique identifier for revocation) |
| `token_version`  | User's token version (for invalidation)   |
| `service_name`   | Issuing service identifier                |
| `correlation_id` | Request tracking ID                       |

### JWT Key Management

#### Generating Keys

```bash
# Generate JWT RSA key pair (4096-bit) with proper permissions
make generate-keys

# Or manually:
# Generate private key (4096-bit RSA) - KEEP SECURE!
openssl genrsa -out keys/private.pem 4096

# Extract public key (distribute to other services)
openssl rsa -in keys/private.pem -pubout -out keys/public.pem

# Set proper permissions
chmod 600 keys/private.pem
chmod 644 keys/public.pem
```

#### Key Security

-   ⚠️ **Private Key** (`keys/private.pem`):

    -   Must NEVER be committed to version control
    -   Keep in secure secrets management (AWS Secrets Manager, Vault, etc.)
    -   Only accessible by this auth service
    -   Rotate periodically (every 90 days recommended)

-   ✅ **Public Key** (`keys/public.pem`):
    -   Can be safely distributed to all services
    -   Used by other services to verify JWT signatures
    -   No security risk if exposed

#### Key Rotation Process

1. Generate new key pair:

    ```bash
    openssl genrsa -out keys/private_new.pem 4096
    openssl rsa -in keys/private_new.pem -pubout -out keys/public_new.pem
    ```

2. Deploy new private key to auth-service

3. Distribute new public key to all services

4. Update environment variables:

    ```env
    JWT_PRIVATE_KEY_PATH=./keys/private_new.pem
    JWT_PUBLIC_KEY_PATH=./keys/public_new.pem
    ```

5. Restart auth-service

6. Monitor for any verification failures

7. After grace period, remove old keys

### Configuration

```env
JWT_PRIVATE_KEY_PATH=./keys/private.pem
JWT_PUBLIC_KEY_PATH=./keys/public.pem
JWT_TTL=3600  # Access token lifetime in seconds (1 hour)

# Token Management
CACHE_DRIVER=redis  # Required for token blacklist caching
```

### Token Usage in Other Services

Other services should:

1. **Obtain the public key** from this auth service
2. **Verify JWT signatures** using the public key (RS256 algorithm)
3. **Extract user information** from verified JWT payload
4. **Check expiration** (`exp` claim)

**Example verification** (pseudo-code):

```php
$publicKey = file_get_contents('keys/public_auth_service.pem');
$decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
$userId = $decoded->sub;
$userEmail = $decoded->email;
```

---

## 🏗️ Architecture

This service follows **Clean Architecture** principles with the following layers:

```
┌─────────────────────────────────────────┐
│             Controllers (HTTP)              │
├─────────────────────────────────────────┤
│            Actions (Use Cases)              │
├─────────────────────────────────────────┤
│          Services (Business Logic)          │
├─────────────────────────────────────────┤
│          Repositories (Data Access)         │
├─────────────────────────────────────────┤
│          Models (Database Entities)         │
└─────────────────────────────────────────┘
```

### Layer Responsibilities

| Layer            | Purpose                        | Example            |
| ---------------- | ------------------------------ | ------------------ |
| **Controllers**  | Handle HTTP requests/responses | `HealthController` |
| **Actions**      | Implement use cases            | `YourAction`       |
| **DTOs**         | Transfer data between layers   | `YourDTO`          |
| **Services**     | Business logic orchestration   | `BaseService`      |
| **Repositories** | Data access abstraction        | `BaseRepository`   |
| **Models**       | Database entities              | `User`             |
| **Contracts**    | Interface definitions          | `ActionContract`   |

📖 **Detailed Architecture**: See [docs/architecture.md](docs/architecture.md)

---

## 📁 Project Structure

```
app/
├── Actions/Auth/              # Use-case implementations
│   ├── RegisterAction.php     # User registration logic
│   ├── LoginAction.php        # User login logic
│   ├── LogoutAction.php       # Logout + token revocation
│   └── RefreshTokenAction.php # Token refresh logic
├── Contracts/
│   ├── Repositories/
│   │   ├── UserRepositoryContract.php
│   │   ├── TokenBlacklistRepositoryContract.php
│   │   └── RefreshTokenRepositoryContract.php
│   ├── Security/
│   │   ├── JwtSignerContract.php
│   │   └── JwtVerifierContract.php
│   └── Services/
│       └── AuthServiceContract.php
├── DTO/Auth/                  # Data Transfer Objects
│   ├── RegisterDTO.php        # Registration data
│   └── LoginDTO.php           # Login data
├── Events/                    # Domain events
│   └── UserRegisteredEvent.php
├── Http/
│   ├── Controllers/Api/V1/Auth/
│   │   ├── RegisterController.php
│   │   ├── LoginController.php
│   │   ├── ProfileController.php
│   │   └── LogoutController.php
│   ├── Middleware/
│   │   ├── CorrelationIdMiddleware.php
│   │   └── JwtAuthMiddleware.php
│   ├── Requests/Auth/
│   │   ├── RegisterRequest.php
│   │   └── LoginRequest.php
│   └── Resources/Auth/
│       ├── UserResource.php
│       └── AuthResource.php
├── Models/
│   ├── User.php
│   ├── TokenBlacklist.php
│   └── RefreshToken.php
├── Providers/
│   └── AuthServiceProvider.php
├── Repositories/
│   ├── UserRepository.php
│   ├── TokenBlacklistRepository.php
│   └── RefreshTokenRepository.php
├── Security/
│   ├── JwtSigner.php          # JWT token generation
│   └── JwtVerifier.php        # JWT token verification
└── Services/
    └── AuthService.php        # Authentication business logic

config/
├── service.php               # Service configuration
├── jwt.php                   # JWT configuration
└── api.php                   # API versioning config

docker/
├── Dockerfile                # Multi-stage PHP-FPM
└── nginx/                    # Nginx configuration

keys/
├── private.pem               # JWT private key (NEVER commit!)
└── public.pem                # JWT public key (distribute to services)

routes/
├── api.php                   # Core routes (health, version)
└── api_v1.php                # Version 1 auth routes
```

📖 **Full Structure**: See [docs/folder-structure.md](docs/folder-structure.md)

---

## 🌐 API Endpoints

### Base URL

```
http://localhost:8010/api
```

### Core Endpoints (Non-Versioned)

| Method | Endpoint       | Description                      |
| ------ | -------------- | -------------------------------- |
| GET    | `/api/health`  | Service health check with status |
| GET    | `/api/version` | Service and version information  |

### Authentication Endpoints (v1)

| Method | Endpoint                | Auth Required | Description                             |
| ------ | ----------------------- | ------------- | --------------------------------------- |
| POST   | `/api/v1/auth/register` | No            | Register new user + get tokens          |
| POST   | `/api/v1/auth/login`    | No            | Login + get JWT + refresh token         |
| GET    | `/api/v1/auth/profile`  | Yes           | Get authenticated user info             |
| POST   | `/api/v1/auth/logout`   | Yes           | Logout + blacklist token                |
| POST   | `/api/v1/auth/refresh`  | No            | Refresh access token with refresh token |

---

### Register

Create a new user account and receive JWT token.

**Endpoint**: `POST /api/v1/auth/register`

**Request Body**:

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Success Response** (201 Created):

```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600,
        "refresh_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-11-17T10:00:00Z",
            "updated_at": "2025-11-17T10:00:00Z"
        }
    },
    "message": "User registered successfully",
    "code": 201,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Validation Errors** (422):

```json
{
    "success": false,
    "data": null,
    "message": "Validation failed",
    "code": 422,
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password confirmation does not match."]
    },
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**cURL Example**:

```bash
curl -X POST http://localhost:8010/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

---

### Login

Authenticate with credentials and receive JWT token.

**Endpoint**: `POST /api/v1/auth/login`

**Request Body**:

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Success Response** (200 OK):

```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600,
        "refresh_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-11-17T10:00:00Z",
            "updated_at": "2025-11-17T10:00:00Z"
        }
    },
    "message": "Login successful",
    "code": 200,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Invalid Credentials** (401):

```json
{
    "success": false,
    "data": null,
    "message": "Invalid credentials",
    "code": 401,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**cURL Example**:

```bash
curl -X POST http://localhost:8010/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

---

### Get Profile (Protected)

Retrieve authenticated user information.

**Endpoint**: `GET /api/v1/auth/profile`

**Headers**:

```
Authorization: Bearer <your-jwt-token>
```

**Success Response** (200 OK):

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-11-17T10:00:00Z",
        "updated_at": "2025-11-17T10:00:00Z"
    },
    "message": "Profile retrieved successfully",
    "code": 200,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Unauthorized** (401):

```json
{
    "success": false,
    "data": null,
    "message": "Unauthorized",
    "code": 401,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**cURL Example**:

```bash
curl -X GET http://localhost:8010/api/v1/auth/profile \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

---

### Logout (Protected)

Logout the authenticated user and blacklist the current token. Also deletes all refresh tokens for the user.

**Endpoint**: `POST /api/v1/auth/logout`

**Headers**:

```
Authorization: Bearer <your-jwt-token>
```

**Success Response** (200 OK):

```json
{
    "success": true,
    "data": null,
    "message": "Logout successful",
    "code": 200,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**cURL Example**:

```bash
curl -X POST http://localhost:8010/api/v1/auth/logout \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

---

### Refresh Token

Get a new access token using a refresh token.

**Endpoint**: `POST /api/v1/auth/refresh`

**Request Body**:

```json
{
    "refresh_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6..."
}
```

**Success Response** (200 OK):

```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600,
        "refresh_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-11-17T10:00:00Z",
            "updated_at": "2025-11-17T10:00:00Z"
        }
    },
    "message": "Token refreshed successfully",
    "code": 200,
    "errors": [],
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

**Error Responses**:

-   **401**: Refresh token expired or invalid
-   **422**: Validation failed (missing refresh_token)

**cURL Example**:

```bash
curl -X POST http://localhost:8010/api/v1/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{
    "refresh_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6..."
  }'
```

### Response Format

All endpoints return a standardized JSON response:

```json
{
    "success": true,
    "data": {},
    "message": "",
    "code": 200,
    "errors": [],
    "correlation_id": "uuid"
}
```

---

## 💻 Development

### Using Make Commands

```bash
make help          # Show all available commands
make install       # Install dependencies
make generate-keys # Generate JWT RSA key pair (4096-bit)
make start         # Start Docker containers
make stop          # Stop containers
make restart       # Restart containers
make test          # Run tests
make format        # Format code with Pint
make analyse       # Run PHPStan analysis
make logs          # View container logs
make shell         # Access PHP container
make clean         # Clear cache
```

### Manual Commands

```bash
# Start containers
docker-compose up -d

# Run tests
docker-compose exec app php artisan test

# Format code
docker-compose exec app ./vendor/bin/pint

# Static analysis
docker-compose exec app ./vendor/bin/phpstan analyse

# Access container
docker-compose exec app bash

# View logs
docker-compose logs -f app
docker-compose logs -f nginx
```

### Environment Variables

Key environment variables for auth service:

```env
# Service Identity
APP_NAME=auth-service
SERVICE_NAME=auth-service
SERVICE_VERSION=1.0.0
APP_PORT=8010

# Database
DB_DATABASE=auth_service_db
DB_USERNAME=auth_service_user
DB_PASSWORD=your_secure_password

# JWT Configuration
JWT_PRIVATE_KEY_PATH=./keys/private.pem
JWT_PUBLIC_KEY_PATH=./keys/public.pem
JWT_TTL=3600

# Cache
CACHE_PREFIX=auth_service_cache

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
```

---

## 🧪 Testing

### Run All Tests

```bash
make test
# or
php artisan test
```

### Run Specific Test

```bash
php artisan test --filter=ExampleTest
```

### With Coverage

```bash
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/           # Feature/integration tests
│   └── ExampleTest.php
└── Unit/             # Unit tests
    └── ExampleTest.php
```

---

## 🚢 Deployment

### Docker Production Build

```bash
# Build production image
docker build -t auth-service:latest -f docker/Dockerfile --target production .

# Run container
docker run -d -p 8010:9000 \
  -e JWT_PRIVATE_KEY_PATH=/run/secrets/jwt_private_key \
  -v /secure/path/to/private.pem:/run/secrets/jwt_private_key:ro \
  auth-service:latest
```

### Environment Configuration

```bash
# Copy and configure .env for production
cp .env.example .env.production

# Update these critical variables:
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generate-with-php-artisan-key:generate>
DB_HOST=<production-db-host>
DB_DATABASE=auth_service_db
DB_USERNAME=auth_service_user
DB_PASSWORD=<strong-secure-password>
REDIS_HOST=<production-redis-host>
JWT_PRIVATE_KEY_PATH=<path-from-secrets-manager>
JWT_TTL=3600
```

### Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Deployment Checklist

-   [ ] **Generate strong JWT key pair** (4096-bit RSA)
-   [ ] **Secure private key** in secrets manager (NEVER commit!)
-   [ ] **Distribute public key** to all services that verify tokens
-   [ ] Update `.env` for production with strong passwords
-   [ ] Run database migrations
-   [ ] Configure secrets management for JWT keys
-   [ ] Set up monitoring/logging
-   [ ] Configure CORS if needed
-   [ ] Enable HTTPS
-   [ ] Configure rate limiting on auth endpoints
-   [ ] Set up backups (database + keys)
-   [ ] Test all endpoints
-   [ ] Document key rotation procedures

---

## 🔧 Configuration

### Service Configuration

Edit `config/service.php`:

```php
return [
    'service_name' => env('SERVICE_NAME', 'auth-service'),
    'service_version' => env('SERVICE_VERSION', '1.0.0'),
];
```

### JWT Configuration

Edit `config/jwt.php`:

```php
return [
    'private_key_path' => env('JWT_PRIVATE_KEY_PATH', './keys/private.pem'),
    'public_key_path' => env('JWT_PUBLIC_KEY_PATH', './keys/public.pem'),
    'ttl' => (int) env('JWT_TTL', 3600), // 1 hour (access token)
    'leeway' => 60,
    'algorithms' => ['RS256'],
];
```

### Token Management

The service implements comprehensive token management:

-   **Token Blacklist**: Revoked tokens are stored in database and cached in Redis
-   **Refresh Tokens**: Long-lived tokens (30 days) for renewing access tokens
-   **Token Versioning**: User token version incremented on logout to invalidate all tokens
-   **Automated Cleanup**: Scheduled task runs daily at 2:00 AM to remove expired tokens

### Correlation ID

All requests automatically receive a correlation ID for distributed tracing:

-   Generated if not provided
-   Logged with every log entry
-   Included in all responses
-   Forwarded to JWT payload

---

## 📚 Documentation

### Available Documentation

-   **[Architecture Overview](docs/architecture.md)** - System design and patterns
-   **[Folder Structure](docs/folder-structure.md)** - Project organization
-   **[OpenAPI Specification](docs/openapi/v1.yaml)** - API v1 specification

### API Documentation

OpenAPI specification available at:

-   `docs/openapi/v1.yaml` - Auth API v1 specification

The specification includes:

-   All authentication endpoints (register, login, profile, logout)
-   Request/response schemas
-   Error responses
-   JWT authentication requirements

---

## 📝 License

This service is open-sourced software licensed under the [MIT license](LICENSE).

---

## 🙏 Credits

Built with:

-   [Laravel 12](https://laravel.com) - PHP Framework
-   [PHP 8.3](https://www.php.net) - Programming Language
-   [firebase/php-jwt](https://github.com/firebase/php-jwt) - JWT Implementation
-   [Docker](https://www.docker.com) - Containerization

---

## 📞 Support

For issues and questions:

-   Check the [documentation](docs/)
-   Review the [architecture guide](docs/architecture.md)
-   Consult [Laravel documentation](https://laravel.com/docs)

---

**Ready to build your microservice? 🚀**

```bash
make setup && make start
```
