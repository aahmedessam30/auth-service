<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('success', function (mixed $data = null, string $message = '', int $code = 200): JsonResponse {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $message,
                'code' => $code,
                'errors' => [],
                'correlation_id' => request()->correlationId ?? null,
            ], $code);
        });

        Response::macro('error', function (string $message, int $code = 400, array $errors = []): JsonResponse {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $message,
                'code' => $code,
                'errors' => $errors,
                'correlation_id' => request()->correlationId ?? null,
            ], $code);
        });

        Response::macro('paginated', function (LengthAwarePaginator|JsonResource $resource): JsonResponse {
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
        });

        Response::macro('validationError', function (array $errors): JsonResponse {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Validation failed',
                'code' => 422,
                'errors' => $errors,
                'correlation_id' => request()->correlationId ?? null,
            ], 422);
        });
    }
}
