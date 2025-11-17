<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\RefreshTokenRepositoryContract;
use App\Contracts\Repositories\TokenBlacklistRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Security\JwtSignerContract;
use App\Contracts\Services\AuthServiceContract;
use App\Repositories\RefreshTokenRepository;
use App\Repositories\TokenBlacklistRepository;
use App\Repositories\UserRepository;
use App\Security\JwtSigner;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(JwtSignerContract::class, JwtSigner::class);
        $this->app->singleton(UserRepositoryContract::class, UserRepository::class);
        $this->app->singleton(TokenBlacklistRepositoryContract::class, TokenBlacklistRepository::class);
        $this->app->singleton(RefreshTokenRepositoryContract::class, RefreshTokenRepository::class);
        $this->app->singleton(AuthServiceContract::class, AuthService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
