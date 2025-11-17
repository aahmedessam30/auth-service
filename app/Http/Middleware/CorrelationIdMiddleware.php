<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->header('X-Correlation-ID') ?? (string) Str::uuid();

        $request->merge(['correlationId' => $correlationId]);
        $request->attributes->set('correlationId', $correlationId);

        Log::shareContext([
            'correlation_id' => $correlationId,
        ]);

        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Correlation-ID', $correlationId);
        }

        return $response;
    }
}
