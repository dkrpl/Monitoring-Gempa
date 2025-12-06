<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\EarthquakeEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Display user dashboard
     */
    public function index()
    {
        return view('users.dashboard');
    }

    /**
     * Get dashboard data for AJAX requests
     */
    public function getDashboardData()
    {
        $activeDevices = Device::where('status', 'aktif')->count();
        $todayEvents = EarthquakeEvent::where('status', '!=', 'normal')
            ->whereDate('occurred_at', today())
            ->count();

        $activeAlerts = EarthquakeEvent::where('status', 'danger')
            ->where('occurred_at', '>=', now()->subHours(24))
            ->count();

        // Get active devices
        $devices = Device::where('status', 'aktif')
            ->orderBy('last_seen', 'desc')
            ->limit(5)
            ->get()
            ->map(function($device) {
                return [
                    'id' => $device->id,
                    'nama_device' => $device->nama_device,
                    'lokasi' => $device->lokasi,
                    'status' => $device->status,
                    'last_seen' => $device->last_seen
                ];
            });

        // Get recent events (only warning and danger)
        $recentEvents = EarthquakeEvent::with('device')
            ->where('status', '!=', 'normal')
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'magnitude' => $event->magnitude,
                    'status' => $event->status,
                    'occurred_at' => $event->occurred_at,
                    'device_location' => $event->device->lokasi,
                    'device_name' => $event->device->nama_device
                ];
            });

        // Get chart data for last 24 hours
        $chartData = $this->getChartData();

        // Check for recent alerts
        $alerts = EarthquakeEvent::with('device')
            ->where('status', 'danger')
            ->where('occurred_at', '>=', now()->subHours(1))
            ->get()
            ->map(function($event) {
                return [
                    'type' => 'Earthquake Alert',
                    'magnitude' => $event->magnitude,
                    'location' => $event->device->lokasi,
                    'device_name' => $event->device->nama_device,
                    'time' => $event->occurred_at
                ];
            });

        return response()->json([
            'success' => true,
            'activeDevices' => $activeDevices,
            'todayEvents' => $todayEvents,
            'activeAlerts' => $activeAlerts,
            'devices' => $devices,
            'recentEvents' => $recentEvents,
            'chartData' => $chartData,
            'alerts' => $alerts
        ]);
    }

    /**
     * Check for new alerts
     */
    public function checkAlerts()
    {
        $alerts = EarthquakeEvent::with('device')
            ->where('status', 'danger')
            ->where('occurred_at', '>=', now()->subHours(1))
            ->get()
            ->map(function($event) {
                return [
                    'type' => 'Earthquake Alert',
                    'magnitude' => $event->magnitude,
                    'location' => $event->device->lokasi,
                    'device_name' => $event->device->nama_device,
                    'time' => $event->occurred_at
                ];
            });

        return response()->json([
            'success' => true,
            'hasAlerts' => $alerts->count() > 0,
            'alerts' => $alerts
        ]);
    }

    /**
     * Get recent earthquake events for user
     */
    public function getRecentEvents()
    {
        $events = EarthquakeEvent::with('device')
            ->where('status', '!=', 'normal')
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'magnitude' => $event->magnitude,
                    'status' => $event->status,
                    'occurred_at' => $event->occurred_at,
                    'device_location' => $event->device->lokasi,
                    'device_name' => $event->device->nama_device,
                    'time_ago' => $event->occurred_at->diffForHumans()
                ];
            });

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        $labels = [];
        $magnitudes = [];

        // Last 24 hours data
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $labels[] = $hour->format('H:00');

            $magnitude = EarthquakeEvent::where('status', '!=', 'normal')
                ->whereBetween('occurred_at', [
                    $hour->copy()->startOfHour(),
                    $hour->copy()->endOfHour()
                ])
                ->max('magnitude');

            $magnitudes[] = $magnitude ?? 0;
        }

        return [
            'labels' => $labels,
            'magnitudes' => $magnitudes
        ];
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics()
    {
        $user = Auth::user();

        $statistics = [
            'total_events_viewed' => $this->getUserEventViews($user),
            'last_login' => $user->last_login_at ? Carbon::parse($user->last_login_at)->diffForHumans() : 'Never',
            'account_created' => $user->created_at->format('F d, Y'),
            'notification_preferences' => $this->getUserNotificationPreferences($user),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }

    private function getUserEventViews($user)
    {
        // In a real application, you might track this in a separate table
        // For now, we'll return a placeholder
        return rand(5, 50);
    }

    private function getUserNotificationPreferences($user)
    {
        // In a real application, get from user settings
        return [
            'email_alerts' => true,
            'push_notifications' => true,
            'sms_alerts' => false,
            'alert_threshold' => 'warning' // warning or danger
        ];
    }
}
