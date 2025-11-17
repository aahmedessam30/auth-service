<?php

namespace App\Contracts\Clients;

interface HttpClientContract
{
    /**
     * Send a GET request.
     */
    public function get(string $url, array $queryParams = []): array;

    /**
     * Send a POST request.
     */
    public function post(string $url, array $data = []): array;

    /**
     * Send a PUT request.
     */
    public function put(string $url, array $data = []): array;

    /**
     * Send a PATCH request.
     */
    public function patch(string $url, array $data = []): array;

    /**
     * Send a DELETE request.
     */
    public function delete(string $url): array;
}
