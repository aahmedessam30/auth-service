<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use TypeError;

class ApiExceptionHandler
{
    /**
     * Map of exception classes to their handler methods
     *
     * @var array<class-string, string>
     */
    public static array $handlers = [
        AuthenticationException::class => 'handleAuthenticationException',
        AccessDeniedHttpException::class => 'handleAuthenticationException',
        AuthorizationException::class => 'handleAuthorizationException',
        ValidationException::class => 'handleValidationException',
        ModelNotFoundException::class => 'handleNotFoundException',
        NotFoundHttpException::class => 'handleNotFoundException',
        MethodNotAllowedHttpException::class => 'handleMethodNotAllowedException',
        HttpException::class => 'handleHttpException',
        QueryException::class => 'handleQueryException',
        TypeError::class => 'handleTypeErrorException',
        ApiException::class => 'handleApiException',
        DomainException::class => 'handleDomainException',
    ];

    /**
     * Handle authentication exceptions
     */
    public static function handleAuthenticationException(
        AuthenticationException|AccessDeniedHttpException $e,
        Request $request
    ): JsonResponse {
        $debug = self::getDebugData($e);

        return self::jsonResponse(
            'Authentication required. Please provide valid credentials.',
            401,
            [],
            $request,
            $debug
        );
    }

    /**
     * Handle authorization exceptions
     */
    public static function handleAuthorizationException(
        AuthorizationException $e,
        Request $request
    ): JsonResponse {
        $debug = self::getDebugData($e);

        return self::jsonResponse(
            'You do not have permission to perform this action.',
            403,
            [],
            $request,
            $debug
        );
    }

    /**
     * Handle validation exceptions
     */
    public static function handleValidationException(
        ValidationException $e,
        Request $request
    ): JsonResponse {
        $debug = self::getDebugData($e);

        return self::jsonResponse(
            'The provided data is invalid.',
            422,
            $e->errors(),
            $request,
            $debug
        );
    }

    /**
     * Handle not found exceptions
     */
    public static function handleNotFoundException(
        ModelNotFoundException|NotFoundHttpException $e,
        Request $request
    ): JsonResponse {
        $message = $e instanceof ModelNotFoundException
            ? 'The requested resource was not found.'
            : "The requested endpoint '{$request->getRequestUri()}' was not found.";

        $debug = self::getDebugData($e);

        return self::jsonResponse($message, 404, [], $request, $debug);
    }

    /**
     * Handle method not allowed exceptions
     */
    public static function handleMethodNotAllowedException(
        MethodNotAllowedHttpException $e,
        Request $request
    ): JsonResponse {
        $allowedMethods = isset($e->getHeaders()['Allow'])
            ? explode(', ', $e->getHeaders()['Allow'])
            : [];

        $message = "The {$request->method()} method is not allowed for this endpoint.";
        $debug = self::getDebugData($e, ['allowed_methods' => $allowedMethods]);

        return self::jsonResponse($message, 405, [], $request, $debug);
    }

    /**
     * Handle general HTTP exceptions
     */
    public static function handleHttpException(HttpException $e, Request $request): JsonResponse
    {
        $message = $e->getMessage() ?: 'An HTTP error occurred.';
        $debug = self::getDebugData($e);

        return self::jsonResponse($message, $e->getStatusCode(), [], $request, $debug);
    }

    /**
     * Handle database query exceptions
     */
    public static function handleQueryException(QueryException $e, Request $request): JsonResponse
    {
        $errorCode = $e->errorInfo[1] ?? null;
        $debug = self::getDebugData($e, ['error_code' => $errorCode, 'sql' => $e->getSql()]);

        $message = match ($errorCode) {
            1451 => 'Cannot delete this resource because it is referenced by other records.',
            1062 => 'A record with this information already exists.',
            default => 'A database error occurred. Please try again later.',
        };

        $statusCode = in_array($errorCode, [1451, 1062]) ? 409 : 500;

        return self::jsonResponse($message, $statusCode, [], $request, $debug);
    }

    /**
     * Handle type error exceptions
     */
    public static function handleTypeErrorException(
        TypeError $e,
        Request $request
    ): JsonResponse {
        $debug = self::getDebugData($e);

        return self::jsonResponse(
            'Type error occurred.',
            500,
            ['type' => [$e->getMessage()]],
            $request,
            $debug
        );
    }

    /**
     * Handle API exceptions
     */
    public static function handleApiException(ApiException $e, Request $request): JsonResponse
    {
        $debug = self::getDebugData($e);

        return self::jsonResponse(
            $e->getMessage(),
            $e->getCode() ?: 400,
            $e->getErrors(),
            $request,
            $debug
        );
    }

    /**
     * Handle domain exceptions
     */
    public static function handleDomainException(DomainException $e, Request $request): JsonResponse
    {
        $debug = self::getDebugData($e);

        return self::jsonResponse(
            $e->getMessage(),
            $e->getCode() ?: 422,
            $e->getErrors(),
            $request,
            $debug
        );
    }

    /**
     * Handle general exceptions with fallback
     */
    public static function handleGeneralException(Throwable $e, Request $request): JsonResponse
    {
        $debug = self::getDebugData($e);

        $message = config('app.debug')
            ? ($e->getMessage() ?: 'An unexpected error occurred.')
            : 'An unexpected error occurred. Please try again later.';

        return self::jsonResponse($message, 500, [], $request, $debug);
    }

    /**
     * Generate standardized JSON response
     */
    private static function jsonResponse(
        string $message,
        int $code,
        array $errors,
        Request $request,
        ?array $debug = null
    ): JsonResponse {
        $correlationId = $request->correlationId ?? $request->header('X-Correlation-ID') ?? null;

        $response = [
            'success' => false,
            'data' => null,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
            'correlation_id' => $correlationId,
        ];

        if ($debug !== null) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $code);
    }

    /**
     * Generate debug data for exceptions when debug mode is enabled
     */
    private static function getDebugData(Throwable $e, array $additionalData = []): ?array
    {
        if (! config('app.debug')) {
            return null;
        }

        return array_merge([
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'timestamp' => now()->toISOString(),
            'trace' => collect($e->getTrace())->take(5)->map(function ($trace) {
                return [
                    'file' => $trace['file'] ?? 'unknown',
                    'line' => $trace['line'] ?? 0,
                    'function' => $trace['function'] ?? 'unknown',
                    'class' => $trace['class'] ?? null,
                ];
            })->toArray(),
        ], $additionalData);
    }
}
