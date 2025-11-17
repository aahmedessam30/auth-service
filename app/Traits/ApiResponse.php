<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Return a successful JSON response.
     */
    protected function success(mixed $data = null, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'code' => $code,
            'errors' => [],
            'correlation_id' => request()->correlationId ?? null,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(string $message, int $code = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
            'correlation_id' => request()->correlationId ?? null,
        ], $code);
    }

    /**
     * Return a paginated JSON response.
     */
    protected function paginated(LengthAwarePaginator|JsonResource $resource): JsonResponse
    {
        if ($resource instanceof JsonResource) {
            return $resource->additional([
                'success' => true,
                'message' => '',
                'code' => 200,
                'errors' => [],
                'correlation_id' => request()->correlationId ?? null,
            ])->response();
        }

        return response()->json([
            'success' => true,
            'data' => $resource->items(),
            'message' => '',
            'code' => 200,
            'errors' => [],
            'correlation_id' => request()->correlationId ?? null,
            'meta' => [
                'current_page' => $resource->currentPage(),
                'from' => $resource->firstItem(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'to' => $resource->lastItem(),
                'total' => $resource->total(),
            ],
            'links' => [
                'first' => $resource->url(1),
                'last' => $resource->url($resource->lastPage()),
                'prev' => $resource->previousPageUrl(),
                'next' => $resource->nextPageUrl(),
            ],
        ], 200);
    }

    /**
     * Return a validation error response.
     */
    protected function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => 'Validation failed',
            'code' => 422,
            'errors' => $errors,
            'correlation_id' => request()->correlationId ?? null,
        ], 422);
    }
}
