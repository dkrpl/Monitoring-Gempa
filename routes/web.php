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
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserEventController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\DeviceDataController;

Route::controller(LandingController::class)->group(function () {
    Route::get('/', 'index')->name('landing');
    Route::get('/about', 'about')->name('about');
    Route::get('/features', 'features')->name('features');
    Route::get('/contact', 'contact')->name('contact');
});

// Auth Routes (from Breeze)
Route::get('/dashboard', function () {
    // Redirect based on role
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return view('dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==================== PUBLIC ROUTES ====================

// ==================== AUTHENTICATED USER ROUTES (ALL USERS) ====================
Route::middleware(['auth'])->group(function () {
    // Profile Routes (accessible to all authenticated users)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
        Route::delete('/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    });
});

// ==================== USER-SPECIFIC ROUTES (NON-ADMIN) ====================
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Dashboard for regular users
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Events (read-only for users)
    Route::get('/events', [UserEventController::class, 'index'])->name('events.index');
    Route::get('/events/map', [UserEventController::class, 'map'])->name('events.map');
    Route::get('/events/alerts', [UserEventController::class, 'alerts'])->name('events.alerts');
    Route::get('/events/statistics', [UserEventController::class, 'statistics'])->name('events.statistics');
    Route::get('/events/{id}', [UserEventController::class, 'show'])->name('events.show');

    // User Profile (separate from admin profile)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
        Route::delete('/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    });

   // API endpoints for user dashboard
    Route::get('/dashboard-data', [UserDashboardController::class, 'getDashboardData']);
    Route::get('/check-alerts', [UserDashboardController::class, 'checkAlerts']);
    Route::get('/recent-events', [UserDashboardController::class, 'getRecentEvents']);
    Route::get('/statistics', [UserDashboardController::class, 'getUserStatistics']);

});

// ==================== ADMIN-ONLY ROUTES ====================
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    // User Management Routes
    Route::resource('users', UserController::class);
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
    Route::resource('devices', DeviceController::class)->except('updateStatus', 'heartbeat', 'generateQrCode', 'offlineDevices');

    // Earthquake Events Routes
    Route::get('/earthquake-events/device/{device}', [EarthquakeEventController::class, 'byDevice'])
        ->name('earthquake-events.by-device');
    Route::post('/earthquake-events/simulate', [EarthquakeEventController::class, 'simulate'])
        ->name('earthquake-events.simulate');
    Route::get('/earthquake-events/export', [EarthquakeEventController::class, 'export'])
        ->name('earthquake-events.export');
    Route::get('/earthquake-events/chart-data', [EarthquakeEventController::class, 'chartData'])
        ->name('earthquake-events.chart-data');
    Route::get('/earthquake-events/recent', [EarthquakeEventController::class, 'recentEvents'])
        ->name('earthquake-events.recent');
    Route::get('/earthquake-events/statistics', [EarthquakeEventController::class, 'statistics'])
        ->name('earthquake-events.statistics');
    Route::resource('earthquake-events', EarthquakeEventController::class)->except('byDevice', 'simulate', 'export', 'chartData', 'recentEvents', 'statistics');

    // Device Logs Routes

    Route::get('/device-logs/device/{device}', [DeviceLogController::class, 'byDevice'])->name('device-logs.by-device');
    Route::post('/device-logs/clear-old', [DeviceLogController::class, 'clearOldLogs'])->name('device-logs.clear-old');
    Route::get('/device-logs/export', [DeviceLogController::class, 'export'])->name('device-logs.export');
    Route::post('/device-logs/simulate', [DeviceLogController::class, 'simulate'])->name('device-logs.simulate');
    Route::get('/device-logs/chart-data/{device?}', [DeviceLogController::class, 'chartData'])->name('device-logs.chart-data');
    Route::get('/device-logs/recent', [DeviceLogController::class, 'recentLogs'])->name('device-logs.recent');
    Route::get('/device-logs/statistics', [DeviceLogController::class, 'statistics'])->name('device-logs.statistics');
    Route::get('/device-logs/device/{device}/health', [DeviceLogController::class, 'deviceHealth'])->name('device-logs.health');
    Route::resource('device-logs', DeviceLogController::class)->except('byDevice', 'clearOldLogs', 'export', 'simulate', 'chartData', 'recentLogs', 'statistics', 'deviceHealth');

    // Threshold Settings Routes
    Route::post('/thresholds/order', [ThresholdController::class, 'updateOrder'])
        ->name('thresholds.update-order');
    Route::post('/thresholds/reset', [ThresholdController::class, 'resetToDefault'])
        ->name('thresholds.reset');
    Route::get('/thresholds/{threshold}/test-notification', [ThresholdController::class, 'testNotification'])
        ->name('thresholds.test-notification');
    Route::get('/thresholds/effectiveness-report', [ThresholdController::class, 'effectivenessReport'])
        ->name('thresholds.effectiveness-report');
    Route::resource('thresholds', ThresholdController::class)->except('updateOrder', 'resetToDefault', 'testNotification', 'effectivenessReport');

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

    // Analytics Routes
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

// ==================== ERROR TESTING ROUTES (DEV ONLY) ====================
if (app()->environment('local')) {
    Route::prefix('_test')->group(function () {
        Route::get('/error/400', function () { abort(400); });
        Route::get('/error/401', function () { abort(401); });
        Route::get('/error/403', function () { abort(403); });
        Route::get('/error/404', function () { abort(404); });
        Route::get('/error/405', function () { abort(405); });
        Route::get('/error/419', function () { abort(419); });
        Route::get('/error/422', function () { abort(422); });
        Route::get('/error/429', function () { abort(429); });
        Route::get('/error/500', function () { throw new Exception('Test 500 error'); });
        Route::get('/error/503', function () { abort(503); });
        Route::get('/error/model', function () { \App\Models\User::findOrFail(999999); });
        Route::get('/error/validation', function () {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['The email field is required.'],
                'password' => ['The password must be at least 8 characters.']
            ]);
        });
    });
}

require __DIR__.'/auth.php';
