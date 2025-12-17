<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceDataController;

// Pastikan menggunakan middleware 'api'
Route::middleware('api')->prefix('v1')->group(function () {

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
            'version' => '1.0.0',
            'detection_threshold' => 3.0,
            'endpoints' => [
                'device_data' => '/api/v1/devices/{uuid}/data',
                'device_info' => '/api/v1/devices/{uuid}/info',
                'device_heartbeat' => '/api/v1/devices/{uuid}/heartbeat',
                'significant_logs' => '/api/v1/devices/{uuid}/significant-logs',
                'test_detection' => '/api/v1/devices/{uuid}/test-detection',
                'detection_stats' => '/api/v1/devices/{uuid}/detection-stats',
                'device_registration' => '/api/v1/devices/register',
                'thresholds' => '/api/v1/thresholds'
            ]
        ]);
    });

    // Activity endpoints
    Route::get('/activity/recent', function () {
        try {
            $activities = \App\Models\ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'user_id' => $activity->user_id,
                        'user' => $activity->user ? [
                            'name' => $activity->user->name,
                            'email' => $activity->user->email,
                            'role' => $activity->user->role
                        ] : null,
                        'action' => $activity->action,
                        'description' => $activity->description,
                        'model_type' => $activity->model_type,
                        'model_id' => $activity->model_id,
                        'ip_address' => $activity->ip_address,
                        'user_agent' => $activity->user_agent,
                        'details' => $activity->details,
                        'created_at' => $activity->created_at->toISOString(),
                        'time_ago' => $activity->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'count' => $activities->count(),
                'total' => \App\Models\ActivityLog::count(),
                'today' => \App\Models\ActivityLog::today()->count()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching recent activities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load activities',
                'error' => $e->getMessage()
            ], 500);
        }
    });
});
