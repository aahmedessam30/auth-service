<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface OpenApiServiceContract
{
    /**
     * Parse OpenAPI YAML specification and return as array.
     *
     * @param  string  $version  API version (e.g., 'v1', 'v2')
     */
    public function parseSpecification(string $version): array;

    /**
     * Check if OpenAPI specification file exists.
     *
     * @param  string  $version  API version (e.g., 'v1', 'v2')
     */
    public function specificationExists(string $version): bool;
}
