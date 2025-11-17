<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Available API Versions
    |--------------------------------------------------------------------------
    |
    | List of all available API versions. Each version should have a
    | corresponding route file (e.g., routes/api_v1.php).
    |
    */
    'available_versions' => ['v1'],

    /*
    |--------------------------------------------------------------------------
    | Default API Version
    |--------------------------------------------------------------------------
    |
    | The default version to use when no version is specified in the request.
    | This is also used in version information responses.
    |
    */
    'default_version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Version Header Name
    |--------------------------------------------------------------------------
    |
    | The header name used to specify the API version in requests.
    | Example: X-API-Version: v1
    |
    */
    'version_header' => 'X-API-Version',

    /*
    |--------------------------------------------------------------------------
    | Deprecation Notice
    |--------------------------------------------------------------------------
    |
    | Versions marked as deprecated will include a deprecation warning in
    | response headers. Format: version => deprecation_date
    |
    */
    'deprecated_versions' => [
        // 'v1' => '2026-01-01',
    ],
];
