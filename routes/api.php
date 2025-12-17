<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceDataController;

// Pastikan menggunakan middleware 'api'
Route::middleware('api')->prefix('v1')->group(function () {

    // Device endpoints
    Route::post('/devices/{uuid}/data', [DeviceDataController::class, 'receiveData']);
    Route::post('/devices/{uuid}/bulk-upload', [DeviceDataController::class, 'bulkUpload']);
    Route::post('/devices/{uuid}/heartbeat', [DeviceDataController::class, 'heartbeat']);
    Route::get('/devices/{uuid}/info', [DeviceDataController::class, 'getDeviceInfo']);
    Route::get('/devices/{uuid}/logs', [DeviceDataController::class, 'getDeviceLogs']);
    Route::get('/devices/{uuid}/significant-logs', [DeviceDataController::class, 'getSignificantLogs']);
    Route::post('/devices/{uuid}/test-detection', [DeviceDataController::class, 'testDetection']);
    Route::get('/devices/{uuid}/detection-stats', [DeviceDataController::class, 'detectionStatistics']);
    Route::post('/devices/register', [DeviceDataController::class, 'registerDevice']);
    Route::get('/thresholds', [DeviceDataController::class, 'getThresholds']);

    // System status
    Route::get('/status', function () {
        return response()->json([
            'status' => 'online',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    });

    // Test endpoint
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API working without session',
            'session_driver' => config('session.driver')
        ]);
    });
});
