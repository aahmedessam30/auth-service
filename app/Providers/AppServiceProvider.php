<?php

namespace App\Providers;

use App\Contracts\Security\JwtVerifierContract;
use App\Contracts\Services\OpenApiServiceContract;
use App\Security\JwtVerifier;
use App\Services\OpenApiService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OpenApiServiceContract::class, OpenApiService::class);
        $this->app->bind(JwtVerifierContract::class, JwtVerifier::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // General API rate limit: 60 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->attributes->get('user_id') ?? $request->ip());
        });

        // Strict rate limit for authentication endpoints: 10 requests per minute
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->attributes->get('user_id') ?? $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'data' => null,
                        'message' => 'Too many requests. Please try again later.',
                        'code' => 429,
                        'errors' => [],
                        'correlation_id' => $request->get('correlationId', ''),
                    ], 429, $headers);
                });
        });
    }
}
