<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

class VersionController extends BaseController
{
    /**
     * Version information endpoint.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->success([
            'service_name' => config('service.service_name'),
            'service_version' => config('service.service_version'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
        ]);
    }
}
