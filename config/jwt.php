<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Private Key Path
    |--------------------------------------------------------------------------
    |
    | Path to the private key file used for signing JWT tokens.
    | This should NEVER be committed to version control.
    |
    */
    'private_key_path' => env('JWT_PRIVATE_KEY_PATH', base_path('keys/private.pem')),

    /*
    |--------------------------------------------------------------------------
    | JWT Public Key Path
    |--------------------------------------------------------------------------
    |
    | Path to the public key file used for verifying JWT signatures.
    | This key can be shared with other services.
    |
    */
    'public_key_path' => env('JWT_PUBLIC_KEY_PATH', base_path('keys/public.pem')),

    /*
    |--------------------------------------------------------------------------
    | Supported Algorithms
    |--------------------------------------------------------------------------
    |
    | Algorithms allowed for JWT signature verification and signing.
    | Only asymmetric algorithms should be used for microservices.
    |
    */
    'algorithms' => ['RS256'],

    /*
    |--------------------------------------------------------------------------
    | Token TTL (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Number of seconds the JWT token is valid.
    | Default: 3600 seconds (1 hour)
    |
    */
    'ttl' => env('JWT_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Clock Skew Leeway
    |--------------------------------------------------------------------------
    |
    | Number of seconds to allow for clock skew between services.
    | Helps handle small time differences between servers.
    |
    */
    'leeway' => 60,
];
