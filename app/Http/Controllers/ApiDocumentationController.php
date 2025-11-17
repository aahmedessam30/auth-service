<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\OpenApiServiceContract;
use App\Exceptions\ApiException;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ApiDocumentationController extends BaseController
{
    public function __construct(
        private readonly OpenApiServiceContract $openApiService
    ) {}

    /**
     * Serve Swagger UI for API documentation.
     *
     * @param  string  $version  API version (default: 'v1')
     */
    public function docs(string $version = 'v1'): View
    {
        return view('docs.swagger', ['version' => $version]);
    }

    /**
     * Return OpenAPI specification as JSON.
     *
     * @param  string  $version  API version (default: 'v1')
     */
    public function json(string $version = 'v1'): JsonResponse
    {
        try {
            $specification = $this->openApiService->parseSpecification($version);

            return response()->json($specification);
        } catch (ApiException $e) {
            return $this->error(
                $e->getMessage(),
                $e->getCode(),
                $e->getErrors()
            );
        }
    }
}
