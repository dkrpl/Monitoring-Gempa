<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EarthquakeEventController;
use App\Http\Controllers\DeviceLogController;
use App\Http\Controllers\ThresholdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ActivityLogController;

Route::controller(LandingController::class)->group(function () {
    Route::get('/', 'index')->name('landing');
    Route::get('/about', 'about')->name('about');
    Route::get('/features', 'features')->name('features');
    Route::get('/contact', 'contact')->name('contact');
});

// Auth Routes (from Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes (accessible to all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
        Route::delete('/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    });
});

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    // User Management Routes
    Route::resource('users', UserController::class);

    // Additional user routes
    Route::post('/users/{user}/status', [UserController::class, 'updateStatus'])
        ->name('users.updateStatus');

    // Device Management Routes
    Route::post('/devices/{device}/status', [DeviceController::class, 'updateStatus'])
        ->name('devices.updateStatus');
    Route::post('/devices/{device}/heartbeat', [DeviceController::class, 'heartbeat'])
        ->name('devices.heartbeat');
    Route::get('/devices/{device}/qr-code', [DeviceController::class, 'generateQrCode'])
        ->name('devices.qr-code');
    Route::get('/devices/offline', [DeviceController::class, 'offlineDevices'])
        ->name('devices.offline');
    Route::resource('devices', DeviceController::class)->except([
        'updateStatus', 'heartbeat', 'qr-code', 'offline']);

     // Earthquake Events Routes - TARUH CUSTOM ROUTES SEBELUM RESOURCE!
    Route::get('/earthquake-events/chart-data', [EarthquakeEventController::class, 'chartData'])
        ->name('earthquake-events.chart-data');
    Route::get('/earthquake-events/recent', [EarthquakeEventController::class, 'recentEvents'])
        ->name('earthquake-events.recent');
    Route::get('/earthquake-events/statistics', [EarthquakeEventController::class, 'statistics'])
        ->name('earthquake-events.statistics');
    Route::post('/earthquake-events/simulate', [EarthquakeEventController::class, 'simulate'])
        ->name('earthquake-events.simulate');
    Route::get('/earthquake-events/export', [EarthquakeEventController::class, 'export'])
        ->name('earthquake-events.export');
    Route::post('/earthquake-events/{event}/send-alert', [EarthquakeEventController::class, 'sendAlert'])
        ->name('earthquake-events.send-alert');
    Route::get('/earthquake-events/device/{device}', [EarthquakeEventController::class, 'byDevice'])
        ->name('earthquake-events.by-device');

    // Earthquake Events Resource Route - HARUS DI AKHIR!
    Route::resource('earthquake-events', EarthquakeEventController::class)->except([
        'chart-data', 'recent', 'statistics', 'simulate', 'export', 'send-alert', 'by-device'
    ]);

    // Device Logs Routes
    Route::get('/device-logs/device/{device}', [DeviceLogController::class, 'byDevice'])->name('device-logs.by-device');
    Route::post('/device-logs/clear-old', [DeviceLogController::class, 'clearOldLogs'])->name('device-logs.clear-old');
    Route::get('/device-logs/export', [DeviceLogController::class, 'export'])->name('device-logs.export');
    Route::post('/device-logs/simulate', [DeviceLogController::class, 'simulate'])->name('device-logs.simulate');
    Route::get('/device-logs/chart-data/{device?}', [DeviceLogController::class, 'chartData'])->name('device-logs.chart-data');
    Route::get('/device-logs/recent', [DeviceLogController::class, 'recentLogs'])->name('device-logs.recent');
    Route::get('/device-logs/statistics', [DeviceLogController::class, 'statistics'])->name('device-logs.statistics');
    Route::get('/device-logs/device/{device}/health', [DeviceLogController::class, 'deviceHealth'])->name('device-logs.health');
    // HAPUS ROUTE YANG TIDAK ADA METHOD-NYA:
    // Route::post('/device-logs/bulk-delete', [DeviceLogController::class, 'bulkDelete'])->name('device-logs.bulk-delete');
    // Route::get('/device-logs/datatable', [DeviceLogController::class, 'datatable'])->name('device-logs.datatable');


    Route::resource('device-logs', DeviceLogController::class)->except([
        'by-device', 'bulk-delete', 'clear-old', 'export', 'simulate', 'chart-data', 'recent', 'statistics', 'health', 'datatable'
    ]);

    // Threshold Settings Routes
    Route::post('/thresholds/order', [ThresholdController::class, 'updateOrder'])
        ->name('thresholds.update-order');
    Route::post('/thresholds/reset', [ThresholdController::class, 'resetToDefault'])
        ->name('thresholds.reset');
    Route::get('/thresholds/{threshold}/test-notification', [ThresholdController::class, 'testNotification'])
        ->name('thresholds.test-notification');
    Route::get('/thresholds/effectiveness-report', [ThresholdController::class, 'effectivenessReport'])
        ->name('thresholds.effectiveness-report');
    Route::resource('thresholds', ThresholdController::class)->except([
        'update-order', 'reset', 'test-notification', 'effectiveness-report'
    ]);

     // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/thresholds', [SettingController::class, 'updateThresholds'])->name('updateThresholds');
        Route::post('/system', [SettingController::class, 'updateSystemSettings'])->name('updateSystem');
        Route::post('/notifications', [SettingController::class, 'updateNotificationSettings'])->name('updateNotifications');
        Route::post('/security', [SettingController::class, 'updateSecuritySettings'])->name('updateSecurity');
        Route::get('/export', [SettingController::class, 'export'])->name('export');
        Route::post('/import', [SettingController::class, 'import'])->name('import');
        Route::post('/reset', [SettingController::class, 'reset'])->name('reset');
        Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clearCache');
        Route::get('/system-info', [SettingController::class, 'systemInfo'])->name('systemInfo');
    });

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::post('/analytics/custom-report', [AnalyticsController::class, 'customReport'])->name('analytics.custom-report');
    Route::post('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/analytics/real-time-stats', [AnalyticsController::class, 'realTimeStats'])->name('analytics.real-time-stats');


    // Activity Log Routes
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::post('/clear', [ActivityLogController::class, 'clearOldLogs'])->name('activity-logs.clear');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/statistics', [ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');
    });

    // Export Routes
    Route::prefix('exports')->group(function () {
        Route::get('/users', [ExportController::class, 'exportUsers'])->name('exports.users');
        Route::get('/devices', [ExportController::class, 'exportDevices'])->name('exports.devices');
        Route::get('/earthquake-events', [ExportController::class, 'exportEarthquakeEvents'])->name('exports.earthquake-events');
        Route::get('/device-logs', [ExportController::class, 'exportDeviceLogs'])->name('exports.device-logs');
        Route::get('/all', [ExportController::class, 'exportAllData'])->name('exports.all');
        Route::get('/statistics', [ExportController::class, 'exportStatistics'])->name('exports.statistics');
        Route::get('/dashboard', [ExportController::class, 'exportDashboard'])->name('exports.dashboard');
    });
});

// Public API for devices (for IoT sensors)
// Public API for devices (for IoT sensors)
Route::prefix('api/v1')->group(function () {
    Route::post('/devices/{uuid}/data', [\App\Http\Controllers\Api\DeviceDataController::class, 'receiveData']);
    Route::post('/devices/{uuid}/bulk-upload', [\App\Http\Controllers\Api\DeviceDataController::class, 'bulkUpload']);
    Route::post('/devices/{uuid}/heartbeat', [\App\Http\Controllers\Api\DeviceDataController::class, 'heartbeat']);
    Route::get('/devices/{uuid}/info', [\App\Http\Controllers\Api\DeviceDataController::class, 'getDeviceInfo']);
    Route::get('/devices/{uuid}/logs', [\App\Http\Controllers\Api\DeviceDataController::class, 'getDeviceLogs']);

    // New detection endpoints
    Route::get('/devices/{uuid}/significant-logs', [\App\Http\Controllers\Api\DeviceDataController::class, 'getSignificantLogs']);
    Route::post('/devices/{uuid}/test-detection', [\App\Http\Controllers\Api\DeviceDataController::class, 'testDetection']);
    Route::get('/devices/{uuid}/detection-stats', [\App\Http\Controllers\Api\DeviceDataController::class, 'detectionStatistics']);

    // Device registration
    Route::post('/devices/register', [\App\Http\Controllers\Api\DeviceDataController::class, 'registerDevice']);

    // Thresholds
    Route::get('/thresholds', [\App\Http\Controllers\Api\DeviceDataController::class, 'getThresholds']);

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
});


// Error testing routes (only in local/dev environment)
if (app()->environment('local')) {
    Route::prefix('_test')->group(function () {
        Route::get('/error/400', function () {
            abort(400);
        });

        Route::get('/error/401', function () {
            abort(401);
        });

        Route::get('/error/403', function () {
            abort(403);
        });

        Route::get('/error/404', function () {
            abort(404);
        });

        Route::get('/error/405', function () {
            abort(405);
        });

        Route::get('/error/419', function () {
            abort(419);
        });

        Route::get('/error/422', function () {
            abort(422);
        });

        Route::get('/error/429', function () {
            abort(429);
        });

        Route::get('/error/500', function () {
            throw new Exception('Test 500 error');
        });

        Route::get('/error/503', function () {
            abort(503);
        });

        Route::get('/error/model', function () {
            \App\Models\User::findOrFail(999999);
        });

        Route::get('/error/validation', function () {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['The email field is required.'],
                'password' => ['The password must be at least 8 characters.']
            ]);
        });
    });
}
require __DIR__.'/auth.php';
