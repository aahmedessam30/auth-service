<?php

namespace App\Clients;

use App\Contracts\Clients\HttpClientContract;
use App\Exceptions\ApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServiceHttpClient implements HttpClientContract
{
    protected int $timeout = 30;

    protected int $retryTimes = 3;

    protected int $retryDelay = 100;

    /**
     * Send a GET request.
     */
    public function get(string $url, array $queryParams = []): array
    {
        return $this->sendRequest('GET', $url, [
            'query' => $queryParams,
        ]);
    }

    /**
     * Send a POST request.
     */
    public function post(string $url, array $data = []): array
    {
        return $this->sendRequest('POST', $url, [
            'json' => $data,
        ]);
    }

    /**
     * Send a PUT request.
     */
    public function put(string $url, array $data = []): array
    {
        return $this->sendRequest('PUT', $url, [
            'json' => $data,
        ]);
    }

    /**
     * Send a PATCH request.
     */
    public function patch(string $url, array $data = []): array
    {
        return $this->sendRequest('PATCH', $url, [
            'json' => $data,
        ]);
    }

    /**
     * Send a DELETE request.
     */
    public function delete(string $url): array
    {
        return $this->sendRequest('DELETE', $url);
    }

    /**
     * Send HTTP request with retry logic and correlation ID.
     */
    protected function sendRequest(string $method, string $url, array $options = []): array
    {
        $correlationId = request()->correlationId ?? request()->header('X-Correlation-ID') ?? null;

        try {
            Log::info('HTTP Request', [
                'method' => $method,
                'url' => $url,
                'correlation_id' => $correlationId,
            ]);

            $response = Http::withHeaders([
                'X-Correlation-ID' => $correlationId,
                'Accept' => 'application/json',
            ])
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->send($method, $url, $options);

            $responseData = $response->json() ?? [];

            Log::info('HTTP Response', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'correlation_id' => $correlationId,
            ]);

            if ($response->failed()) {
                throw new ApiException(
                    message: $responseData['message'] ?? 'External service request failed',
                    code: $response->status(),
                    errors: $responseData['errors'] ?? []
                );
            }

            return $responseData;
        } catch (ConnectionException $e) {
            Log::error('HTTP Connection Error', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
                'correlation_id' => $correlationId,
            ]);

            throw new ApiException(
                message: 'Failed to connect to external service',
                code: 503,
                errors: ['connection_error' => $e->getMessage()]
            );
        }
    }
}
