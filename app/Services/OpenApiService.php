<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\OpenApiServiceContract;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class OpenApiService implements OpenApiServiceContract
{
    /**
     * Parse OpenAPI YAML specification and return as array.
     *
     * @param  string  $version  API version (e.g., 'v1', 'v2')
     *
     * @throws ApiException
     */
    public function parseSpecification(string $version): array
    {
        if (! $this->specificationExists($version)) {
            throw new ApiException(
                "OpenAPI specification for version {$version} not found",
                404
            );
        }

        try {
            $yamlContent = $this->readSpecificationFile($version);
            $specification = $this->parseYaml($yamlContent);

            return $this->applyDynamicConfiguration($specification);
        } catch (ParseException $e) {
            throw new ApiException(
                'Failed to parse OpenAPI specification: '.$e->getMessage(),
                500
            );
        }
    }

    /**
     * Check if OpenAPI specification file exists.
     *
     * @param  string  $version  API version (e.g., 'v1', 'v2')
     */
    public function specificationExists(string $version): bool
    {
        return File::exists($this->getSpecificationPath($version));
    }

    /**
     * Get the full path to the OpenAPI specification file.
     *
     * @param  string  $version  API version (e.g., 'v1', 'v2')
     */
    private function getSpecificationPath(string $version): string
    {
        return base_path("docs/openapi/{$version}.yaml");
    }

    /**
     * Read the specification file content.
     *
     * @param  string  $version  API version (e.g., 'v1', 'v2')
     */
    private function readSpecificationFile(string $version): string
    {
        return File::get($this->getSpecificationPath($version));
    }

    /**
     * Parse YAML content to array.
     *
     * @throws ParseException
     */
    private function parseYaml(string $content): array
    {
        return Yaml::parse($content);
    }

    /**
     * Apply dynamic configuration to the specification.
     */
    private function applyDynamicConfiguration(array $specification): array
    {
        $specification = $this->replaceServiceMetadata($specification);
        $specification = $this->updateServerUrls($specification);

        return $specification;
    }

    /**
     * Replace service metadata placeholders.
     */
    private function replaceServiceMetadata(array $specification): array
    {
        if (isset($specification['info']['title'])) {
            $specification['info']['title'] = str_replace(
                '{{ service_name }}',
                config('service.service_name', 'Laravel Microservice'),
                $specification['info']['title']
            );
        }

        if (isset($specification['info']['version'])) {
            $specification['info']['version'] = config(
                'service.service_version',
                $specification['info']['version']
            );
        }

        return $specification;
    }

    /**
     * Update server URLs based on current environment.
     */
    private function updateServerUrls(array $specification): array
    {
        if (! isset($specification['servers'])) {
            return $specification;
        }

        $appUrl = config('app.url', 'http://localhost:8000');

        foreach ($specification['servers'] as $key => $server) {
            if ($this->isLocalhostServer($server)) {
                $specification['servers'][$key]['url'] = str_replace(
                    'http://localhost:8000',
                    $appUrl,
                    $server['url']
                );
            }
        }

        return $specification;
    }

    /**
     * Check if server URL contains localhost.
     */
    private function isLocalhostServer(array $server): bool
    {
        return isset($server['url']) && str_contains($server['url'], 'localhost');
    }
}
