<?php

declare(strict_types=1);

if (! function_exists('extractBearerToken')) {
    /**
     * Extract Bearer token from request Authorization header.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    function extractBearerToken($request): ?string
    {
        $header = $request->header('Authorization');

        if (! $header) {
            return null;
        }

        if (! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = substr($header, 7);

        return empty(trim($token)) ? null : trim($token);
    }
}
