<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Service Name
    |--------------------------------------------------------------------------
    |
    | The name of this microservice. Used in logging, monitoring, and
    | version endpoint responses.
    |
    */
    'service_name' => env('SERVICE_NAME', 'auth-service'),

    /*
    |--------------------------------------------------------------------------
    | Service Version
    |--------------------------------------------------------------------------
    |
    | The current version of this microservice. Should follow semantic
    | versioning (e.g., 1.0.0).
    |
    */
    'service_version' => env('SERVICE_VERSION', '1.0.0'),
];
