<?php

declare(strict_types=1);

namespace App\Security;

use App\Contracts\Security\JwtKeyLoaderContract;
use App\Exceptions\ApiException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

abstract class BaseJwtHandler implements JwtKeyLoaderContract
{
    protected string $publicKeyPath;

    protected string $privateKeyPath;

    protected array $algorithms;

    protected int $leeway;

    public function __construct()
    {
        $this->publicKeyPath = $this->resolvePath(config('jwt.public_key_path') ?: base_path('keys/public.pem'));
        $this->privateKeyPath = $this->resolvePath(config('jwt.private_key_path') ?: base_path('keys/private.pem'));
        $this->algorithms = config('jwt.algorithms', ['RS256']);
        $this->leeway = config('jwt.leeway', 60);

        JWT::$leeway = $this->leeway;
    }

    /**
     * Load the private key from the configured path.
     *
     * @throws ApiException
     */
    public function loadPrivateKey(): string
    {
        return $this->loadKey(
            $this->privateKeyPath,
            'JWT private key not found. Please configure JWT_PRIVATE_KEY_PATH.',
            'JWT private key is empty'
        );
    }

    /**
     * Load the public key from the configured path.
     * Uses cache to avoid repeated file I/O.
     *
     * @throws ApiException
     */
    public function loadPublicKey(): string
    {
        return Cache::rememberForever('jwt_public_key', function () {
            return $this->loadKey(
                $this->publicKeyPath,
                'JWT public key not found. Please configure JWT_PUBLIC_KEY_PATH.',
                'JWT public key is empty'
            );
        });
    }

    /**
     * Load a key file from the specified path.
     *
     * @throws ApiException
     */
    private function loadKey(string $path, string $notFoundMessage, string $emptyMessage): string
    {
        if (! File::exists($path)) {
            throw new ApiException($notFoundMessage.' Path: '.$path, 500);
        }

        $key = File::get($path);

        if (empty($key)) {
            throw new ApiException($emptyMessage, 500);
        }

        return $key;
    }

    /**
     * Resolve the absolute path of a key file.
     * Handles relative paths, absolute paths, and URLs.
     */
    private function resolvePath(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if ($this->isAbsolutePath($path)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return storage_path(substr($path, strlen('storage/')));
        }

        return base_path($path);
    }

    /**
     * Check if a path is absolute.
     */
    private function isAbsolutePath(string $path): bool
    {
        if (str_starts_with($path, '/')) {
            return true;
        }

        if (preg_match('/^[a-zA-Z]:[\\\\\/]/', $path)) {
            return true;
        }

        return false;
    }

    /**
     * Convert decoded JWT object to associative array.
     */
    protected function convertToArray(object $decoded): array
    {
        return json_decode(json_encode($decoded), true);
    }

    /**
     * Get the primary algorithm for JWT operations.
     */
    public function getAlgorithm(): string
    {
        return $this->algorithms[0];
    }
}
