# How to Create a New Microservice

This guide walks you through creating a new microservice using this template.

## Prerequisites

-   Docker and Docker Compose installed
-   Git installed
-   Composer installed (optional, can use Docker)
-   Make installed (optional, but recommended)

## Step 1: Clone the Template

```bash
# Clone this repository as your new service
git clone https://github.com/your-org/laravel-microservice-template.git my-new-service

# Navigate to the new directory
cd my-new-service

# Remove the old git history
rm -rf .git

# Initialize a new git repository
git init
```

## Step 2: Configure Environment

```bash
# Copy the environment file
cp .env.example .env

# Edit .env file and update these values:
# - APP_NAME=YourServiceName
# - SERVICE_NAME=your-service-name
# - SERVICE_VERSION=1.0.0
# - DB_DATABASE=your_database_name
# - Any other service-specific variables
```

**Important `.env` variables:**

```env
APP_NAME=YourServiceName
APP_ENV=local
APP_DEBUG=true
SERVICE_NAME=your-service-name
SERVICE_VERSION=1.0.0

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
```

## Step 3: Install Dependencies

### Option A: Using Make (Recommended)

```bash
make install
```

### Option B: Manual Installation

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Generate application key
php artisan key:generate
```

## Step 4: Build and Start Docker Containers

### Option A: Using Make

```bash
# Build Docker images
make build

# Start containers
make start
```

### Option B: Using Docker Compose

```bash
# Build and start containers
docker-compose up -d --build

# View logs
docker-compose logs -f
```

## Step 5: Run Migrations

```bash
# Using make
make migrate

# Or using Docker
docker-compose exec php-fpm php artisan migrate

# Or locally (if PHP installed)
php artisan migrate
```

## Step 6: Verify Installation

### Check Health Endpoint

```bash
curl http://localhost:8000/api/health
```

**Expected response:**

```json
{
    "success": true,
    "data": {
        "service": "your-service-name",
        "status": "healthy",
        "checks": {
            "database": {
                "status": "ok",
                "message": "Database connection successful"
            },
            "cache": { "status": "ok", "message": "Cache working correctly" },
            "queue": {
                "status": "ok",
                "message": "Queue connection successful"
            }
        }
    }
}
```

### Check Version Endpoint

```bash
curl http://localhost:8000/api/version
```

## Step 7: Run Tests

```bash
# Using make
make test

# Or using Docker
docker-compose exec php-fpm php artisan test

# Or locally
php artisan test
```

## Step 8: Start Developing

### Create Your First Feature

1. **Create a DTO**

```bash
# Create file: app/DTO/CreateUserDTO.php
php artisan make:class DTO/CreateUserDTO
```

2. **Create a Contract**

```bash
# Create file: app/Contracts/Actions/CreateUserActionContract.php
php artisan make:class Contracts/Actions/CreateUserActionContract
```

3. **Create an Action**

```bash
# Create file: app/Actions/CreateUserAction.php
php artisan make:class Actions/CreateUserAction
```

4. **Create a Controller**

```bash
php artisan make:controller UserController
```

5. **Add Route**
   Edit `routes/api.php` and add your route

6. **Create Tests**

```bash
php artisan make:test UserTest
```

### Example Feature Implementation

**DTO:**

```php
// app/DTO/CreateUserDTO.php
namespace App\DTO;

class CreateUserDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
```

**Contract:**

```php
// app/Contracts/Actions/CreateUserActionContract.php
namespace App\Contracts\Actions;

use App\DTO\CreateUserDTO;
use App\Models\User;

interface CreateUserActionContract extends ActionContract
{
    public function execute(CreateUserDTO $dto): User;
}
```

**Action:**

```php
// app/Actions/CreateUserAction.php
namespace App\Actions;

use App\Contracts\Actions\CreateUserActionContract;
use App\DTO\CreateUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserAction implements CreateUserActionContract
{
    public function execute(CreateUserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
    }
}
```

**Controller:**

```php
// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Actions\CreateUserAction;
use App\DTO\CreateUserDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(
        protected CreateUserAction $createUserAction,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $dto = new CreateUserDTO(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password']
        );

        $user = $this->createUserAction->execute($dto);

        return $this->success($user, 'User created successfully', 201);
    }
}
```

**Route:**

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::post('/users', [UserController::class, 'store']);
});
```

## Step 9: Code Quality

```bash
# Format code
make format

# Run static analysis
make analyse

# Run tests
make test
```

## Step 10: Update Documentation

1. **Update README.md** with service-specific information
2. **Update config/service.php** with service name and version

## Development Workflow

### Daily Development

```bash
# Start services
make start

# Run tests after changes
make test

# Format code
make format

# View logs
make logs
```

### Database Changes

```bash
# Create migration
php artisan make:migration create_orders_table

# Run migrations
make migrate

# Create seeder
php artisan make:seeder OrderSeeder
```

### Adding External Services

1. Add client in `app/Clients/`
2. Create contract in `app/Contracts/Clients/`
3. Register in service provider if needed
4. Use in actions/services

## Deployment Checklist

-   [ ] Update `.env` for production
-   [ ] Set `APP_ENV=production`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Configure database credentials
-   [ ] Set up Redis for cache and queues
-   [ ] Configure queue workers
-   [ ] Set up monitoring (logs, APM)
-   [ ] Configure CORS if needed
-   [ ] Set up CI/CD pipeline
-   [ ] Run `php artisan config:cache`
-   [ ] Run `php artisan route:cache`
-   [ ] Run `php artisan view:cache`

## Useful Make Commands

```bash
make help          # Show all commands
make install       # Install dependencies
make start         # Start containers
make stop          # Stop containers
make restart       # Restart containers
make test          # Run tests
make format        # Format code
make analyse       # Static analysis
make logs          # View logs
make shell         # Access container shell
make clean         # Clear cache
```

## Troubleshooting

### Port Already in Use

Edit `docker-compose.yml` and change `APP_PORT`:

```yaml
ports:
    - "8001:80" # Changed from 8000
```

### Database Connection Failed

Ensure database container is running:

```bash
docker-compose ps
docker-compose logs mysql
```

### Permission Issues

```bash
# Fix storage permissions
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Container Won't Start

```bash
# View logs
docker-compose logs php-fpm

# Rebuild containers
docker-compose down -v
docker-compose up -d --build
```

## Next Steps

1. Implement your business logic in versioned controllers
2. Add authentication if needed (JWT middleware is available)
3. Set up CI/CD pipeline
4. Configure monitoring and logging
5. Write comprehensive tests
6. Update API documentation (docs/openapi/v1.yaml)

## Support

For issues or questions:

-   Check documentation in `docs/`
-   Review existing code in `app/`
-   Refer to Laravel documentation: https://laravel.com/docs

---

**Your microservice is now ready for development! 🚀**
