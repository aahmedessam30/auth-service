<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class HealthController extends BaseController
{
    /**
     * Health check endpoint.
     *
     * Checks the readiness of database, cache, and queue connections.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
        ];

        $healthy = collect($checks)->every(fn ($check) => $check['status'] === 'ok');

        return $this->success([
            'service' => config('service.service_name'),
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Check database connection.
     */
    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check cache connection.
     */
    protected function checkCache(): array
    {
        try {
            $key = 'health_check_'.time();
            Cache::put($key, 'test', 1);
            $value = Cache::get($key);
            Cache::forget($key);

            return [
                'status' => $value === 'test' ? 'ok' : 'error',
                'message' => $value === 'test' ? 'Cache working correctly' : 'Cache read/write failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check queue connection.
     */
    protected function checkQueue(): array
    {
        try {
            $size = Queue::size();

            return [
                'status' => 'ok',
                'message' => 'Queue connection successful',
                'queue_size' => $size,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Queue connection failed: '.$e->getMessage(),
            ];
        }
    }
}
