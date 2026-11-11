<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('books', BookController::class);
});


Route::get('/health', function () {

    $checks = [];

    // Check database connection
    try {
        DB::select('SELECT 1');

        $checks['database'] = 'healthy';

    } catch (\Throwable $e) {

        $checks['database'] = 'unhealthy';

        Log::error('health_check.failed', [
            'actor' => 'system',
            'action' => 'health_check',
            'component' => 'database',
            'reason' => 'database_connection_failed',
            'exception_class' => get_class($e),
        ]);
    }

    // Check cache read/write
    try {
        $key = 'health-check:' . Str::uuid();

        Cache::put($key, 'ok', 10);

        $cacheWorks = Cache::get($key) === 'ok';

        Cache::forget($key);

        if (!$cacheWorks) {
            throw new \RuntimeException('Cache read/write verification failed');
        }

        $checks['cache'] = 'healthy';

    } catch (\Throwable $e) {

        $checks['cache'] = 'unhealthy';

        Log::error('health_check.failed', [
            'actor' => 'system',
            'action' => 'health_check',
            'component' => 'cache',
            'reason' => 'cache_read_write_failed',
            'exception_class' => get_class($e),
        ]);
    }

    $healthy = !in_array('unhealthy', $checks, true);

    $failedComponents = array_keys(
        array_filter($checks, fn ($status) => $status === 'unhealthy')
    );

    return response()->json([
        'status' => $healthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'failed_components' => $failedComponents,
    ], $healthy ? 200 : 503);
});

