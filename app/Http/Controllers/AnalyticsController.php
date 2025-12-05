<?php

namespace App\Http\Controllers;

use App\Models\EarthquakeEvent;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index()
{
    // Basic statistics
    $stats = $this->getBasicStats();

    // Get data for charts
    $chartData = [
        'events_by_day' => $this->getEventsByDay(30),
        'events_by_device' => $this->getEventsByDevice(),
        'magnitude_distribution' => $this->getMagnitudeDistribution(),
        'status_distribution' => $this->getStatusDistribution(),
        'hourly_activity' => $this->getHourlyActivity(),
        'device_activity' => $this->getDeviceActivity(),
    ];

    // Recent events
    $recentEvents = EarthquakeEvent::with('device')
        ->orderBy('occurred_at', 'desc')
        ->limit(10)
        ->get();

    // Top active devices - use the device_activity data
    $topDevices = $chartData['device_activity']->take(5);

    return view('analytics.index', compact('stats', 'chartData', 'recentEvents', 'topDevices'));
}

    /**
     * Get basic statistics.
     */
    private function getBasicStats()
    {
        $today = today();
        $yesterday = today()->subDay();
        $lastWeek = today()->subWeek();

        return [
            // Total counts
            'total_events' => EarthquakeEvent::count(),
            'total_devices' => Device::count(),
            'total_users' => User::count(),
            'active_devices' => Device::where('status', 'aktif')->count(),

            // Today's stats
            'today_events' => EarthquakeEvent::whereDate('occurred_at', $today)->count(),
            'today_warning' => EarthquakeEvent::whereDate('occurred_at', $today)->where('status', 'warning')->count(),
            'today_danger' => EarthquakeEvent::whereDate('occurred_at', $today)->where('status', 'danger')->count(),

            // Yesterday's stats
            'yesterday_events' => EarthquakeEvent::whereDate('occurred_at', $yesterday)->count(),

            // Last week stats
            'last_week_events' => EarthquakeEvent::where('occurred_at', '>=', $lastWeek)->count(),

            // Averages
            'avg_magnitude' => round(EarthquakeEvent::avg('magnitude') ?? 0, 2),
            'max_magnitude' => round(EarthquakeEvent::max('magnitude') ?? 0, 2),
            'min_magnitude' => round(EarthquakeEvent::min('magnitude') ?? 0, 2),

            // Status distribution
            'warning_count' => EarthquakeEvent::where('status', 'warning')->count(),
            'danger_count' => EarthquakeEvent::where('status', 'danger')->count(),

            // Device stats
            'device_uptime' => $this->calculateDeviceUptime(),
            'avg_events_per_device' => $this->calculateAvgEventsPerDevice(),
        ];
    }

    /**
     * Get events by day for the last N days.
     */
    private function getEventsByDay($days = 30)
    {
        $data = EarthquakeEvent::select(
            DB::raw('DATE(occurred_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning'),
            DB::raw('SUM(CASE WHEN status = "danger" THEN 1 ELSE 0 END) as danger')
        )
        ->where('occurred_at', '>=', now()->subDays($days))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $dates = [];
        $totals = [];
        $warnings = [];
        $dangers = [];

        // Fill missing dates
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dateLabel = now()->subDays($i)->format('M d');

            $record = $data->firstWhere('date', $date);

            $dates[] = $dateLabel;
            $totals[] = $record ? $record->count : 0;
            $warnings[] = $record ? $record->warning : 0;
            $dangers[] = $record ? $record->danger : 0;
        }

        return [
            'dates' => $dates,
            'totals' => $totals,
            'warnings' => $warnings,
            'dangers' => $dangers,
        ];
    }

    /**
     * Get events by device.
     */
    private function getEventsByDevice()
{
    // Get device statistics
    $deviceStats = EarthquakeEvent::select(
        'device_id',
        DB::raw('COUNT(*) as event_count'),
        DB::raw('AVG(magnitude) as avg_magnitude'),
        DB::raw('MAX(magnitude) as max_magnitude')
    )
    ->groupBy('device_id')
    ->orderBy('event_count', 'desc')
    ->limit(10)
    ->get()
    ->keyBy('device_id');

    // Get device names
    $deviceIds = $deviceStats->keys()->toArray();
    $devices = Device::whereIn('id', $deviceIds)->get()->keyBy('id');

    $deviceNames = [];
    $counts = [];
    $avgMagnitudes = [];
    $maxMagnitudes = [];

    foreach ($deviceStats as $deviceId => $stats) {
        $device = $devices->get($deviceId);
        if ($device) {
            $deviceNames[] = $device->nama_device;
            $counts[] = $stats->event_count;
            $avgMagnitudes[] = round($stats->avg_magnitude, 2);
            $maxMagnitudes[] = round($stats->max_magnitude, 2);
        }
    }

    return [
        'devices' => $deviceNames,
        'counts' => $counts,
        'avg_magnitudes' => $avgMagnitudes,
        'max_magnitudes' => $maxMagnitudes,
    ];
}

    /**
     * Get magnitude distribution.
     */
    private function getMagnitudeDistribution()
    {
        $ranges = [
            ['min' => 0, 'max' => 2.9, 'label' => '0-2.9'],
            ['min' => 3.0, 'max' => 3.9, 'label' => '3.0-3.9'],
            ['min' => 4.0, 'max' => 4.9, 'label' => '4.0-4.9'],
            ['min' => 5.0, 'max' => 5.9, 'label' => '5.0-5.9'],
            ['min' => 6.0, 'max' => 6.9, 'label' => '6.0-6.9'],
            ['min' => 7.0, 'max' => 10.0, 'label' => '7.0+'],
        ];

        $labels = [];
        $counts = [];
        $colors = [];

        foreach ($ranges as $range) {
            $count = EarthquakeEvent::whereBetween('magnitude', [$range['min'], $range['max']])->count();

            $labels[] = $range['label'];
            $counts[] = $count;

            // Assign colors based on magnitude range
            if ($range['min'] >= 5.0) {
                $colors[] = '#e74a3b'; // Red for dangerous
            } elseif ($range['min'] >= 3.0) {
                $colors[] = '#f6c23e'; // Yellow for warning
            } else {
                $colors[] = '#1cc88a'; // Green for normal
            }
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'colors' => $colors,
        ];
    }

    /**
     * Get status distribution.
     */
    private function getStatusDistribution()
    {
        $data = EarthquakeEvent::select(
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('AVG(magnitude) as avg_magnitude')
        )
        ->groupBy('status')
        ->get();

        $labels = [];
        $counts = [];
        $colors = [];
        $avgMagnitudes = [];

        foreach ($data as $item) {
            $labels[] = ucfirst($item->status);
            $counts[] = $item->count;
            $avgMagnitudes[] = round($item->avg_magnitude, 2);

            // Assign colors
            $colors[] = $item->status === 'danger' ? '#e74a3b' :
                       ($item->status === 'warning' ? '#f6c23e' : '#1cc88a');
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'colors' => $colors,
            'avg_magnitudes' => $avgMagnitudes,
        ];
    }

    /**
     * Get hourly activity.
     */
    private function getHourlyActivity()
    {
        $data = EarthquakeEvent::select(
            DB::raw('HOUR(occurred_at) as hour'),
            DB::raw('COUNT(*) as count'),
            DB::raw('AVG(magnitude) as avg_magnitude')
        )
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

        $hours = [];
        $counts = [];
        $avgMagnitudes = [];

        // Fill all 24 hours
        for ($i = 0; $i < 24; $i++) {
            $record = $data->firstWhere('hour', $i);

            $hours[] = sprintf('%02d:00', $i);
            $counts[] = $record ? $record->count : 0;
            $avgMagnitudes[] = $record ? round($record->avg_magnitude, 2) : 0;
        }

        return [
            'hours' => $hours,
            'counts' => $counts,
            'avg_magnitudes' => $avgMagnitudes,
        ];
    }

    /**
     * Get device activity.
     */
    private function getDeviceActivity()
{
    // Get device IDs with their counts first
    $deviceStats = EarthquakeEvent::select(
        'device_id',
        DB::raw('COUNT(*) as event_count'),
        DB::raw('MAX(occurred_at) as last_event'),
        DB::raw('AVG(magnitude) as avg_magnitude')
    )
    ->groupBy('device_id')
    ->orderBy('event_count', 'desc')
    ->get()
    ->keyBy('device_id');

    // Get all devices
    $devices = Device::all();

    // Merge device data with stats
    $devices->each(function ($device) use ($deviceStats) {
        $stats = $deviceStats->get($device->id);
        $device->event_count = $stats ? $stats->event_count : 0;
        $device->last_event = $stats ? $stats->last_event : null;
        $device->avg_magnitude = $stats ? round($stats->avg_magnitude, 2) : 0;
    });

    // Sort by event count descending
    return $devices->sortByDesc('event_count')->values();
}

    /**
     * Calculate device uptime percentage.
     */
    private function calculateDeviceUptime()
    {
        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'aktif')->count();

        if ($totalDevices === 0) {
            return 0;
        }

        return round(($activeDevices / $totalDevices) * 100, 2);
    }

    /**
     * Calculate average events per device.
     */
   private function calculateAvgEventsPerDevice()
    {
        $totalEvents = EarthquakeEvent::count();
        $totalDevices = Device::count();

        if ($totalDevices === 0) {
            return 0;
        }

        return round($totalEvents / $totalDevices, 2);
    }

    /**
     * Get custom analytics report.
     */
    public function customReport(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'report_type' => ['required', 'in:daily,weekly,monthly,device_summary']
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = EarthquakeEvent::query();

        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }

        $query->whereBetween('occurred_at', [$startDate, $endDate]);

        switch ($request->report_type) {
            case 'daily':
                $data = $this->generateDailyReport($query, $startDate, $endDate);
                break;
            case 'weekly':
                $data = $this->generateWeeklyReport($query, $startDate, $endDate);
                break;
            case 'monthly':
                $data = $this->generateMonthlyReport($query, $startDate, $endDate);
                break;
            case 'device_summary':
                $data = $this->generateDeviceSummaryReport($query);
                break;
            default:
                $data = [];
        }

        return response()->json([
            'success' => true,
            'report_type' => $request->report_type,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'data' => $data
        ]);
    }

    private function generateDailyReport($query, $startDate, $endDate)
    {
        $data = $query->select(
            DB::raw('DATE(occurred_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning'),
            DB::raw('SUM(CASE WHEN status = "danger" THEN 1 ELSE 0 END) as danger'),
            DB::raw('AVG(magnitude) as avg_magnitude'),
            DB::raw('MAX(magnitude) as max_magnitude')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return $data;
    }

    private function generateWeeklyReport($query, $startDate, $endDate)
    {
        $data = $query->select(
            DB::raw('YEARWEEK(occurred_at, 1) as week'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning'),
            DB::raw('SUM(CASE WHEN status = "danger" THEN 1 ELSE 0 END) as danger'),
            DB::raw('AVG(magnitude) as avg_magnitude'),
            DB::raw('MAX(magnitude) as max_magnitude')
        )
        ->groupBy('week')
        ->orderBy('week')
        ->get()
        ->map(function ($item) {
            $year = substr($item->week, 0, 4);
            $week = substr($item->week, 4);
            $item->week_label = "Week {$week}, {$year}";
            return $item;
        });

        return $data;
    }

    private function generateMonthlyReport($query, $startDate, $endDate)
    {
        $data = $query->select(
            DB::raw('DATE_FORMAT(occurred_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning'),
            DB::raw('SUM(CASE WHEN status = "danger" THEN 1 ELSE 0 END) as danger'),
            DB::raw('AVG(magnitude) as avg_magnitude'),
            DB::raw('MAX(magnitude) as max_magnitude')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->map(function ($item) {
            $item->month_label = Carbon::createFromFormat('Y-m', $item->month)->format('F Y');
            return $item;
        });

        return $data;
    }

    private function generateDeviceSummaryReport($query)
{
    // First get the aggregated stats
    $stats = $query->select(
        'device_id',
        DB::raw('COUNT(*) as total'),
        DB::raw('SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning'),
        DB::raw('SUM(CASE WHEN status = "danger" THEN 1 ELSE 0 END) as danger'),
        DB::raw('AVG(magnitude) as avg_magnitude'),
        DB::raw('MAX(magnitude) as max_magnitude'),
        DB::raw('MIN(magnitude) as min_magnitude')
    )
    ->groupBy('device_id')
    ->orderBy('total', 'desc')
    ->get();

    // Get device details
    $deviceIds = $stats->pluck('device_id')->toArray();
    $devices = Device::whereIn('id', $deviceIds)->get()->keyBy('id');

    // Merge stats with device details
    $data = $stats->map(function ($stat) use ($devices) {
        $device = $devices->get($stat->device_id);
        if (!$device) {
            return null;
        }

        return [
            'device_id' => $stat->device_id,
            'nama_device' => $device->nama_device,
            'lokasi' => $device->lokasi,
            'total' => $stat->total,
            'warning' => $stat->warning,
            'danger' => $stat->danger,
            'avg_magnitude' => round($stat->avg_magnitude, 2),
            'max_magnitude' => round($stat->max_magnitude, 2),
            'min_magnitude' => round($stat->min_magnitude, 2),
        ];
    })->filter()->values();

    return $data;
}

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:events_summary,devices_summary,full_report'],
            'format' => ['required', 'in:csv,json']
        ]);

        switch ($request->type) {
            case 'events_summary':
                $data = $this->getEventsSummaryData();
                $filename = 'events-summary-' . date('Y-m-d-H-i-s');
                break;
            case 'devices_summary':
                $data = $this->getDevicesSummaryData();
                $filename = 'devices-summary-' . date('Y-m-d-H-i-s');
                break;
            case 'full_report':
                $data = $this->getFullReportData();
                $filename = 'full-analytics-report-' . date('Y-m-d-H-i-s');
                break;
            default:
                $data = [];
                $filename = 'report-' . date('Y-m-d-H-i-s');
        }

        if ($request->format === 'csv') {
            return $this->exportToCSV($data, $filename . '.csv');
        } else {
            return response()->json([
                'success' => true,
                'filename' => $filename . '.json',
                'data' => $data,
                'generated_at' => now()->toDateTimeString()
            ]);
        }
    }

    private function getEventsSummaryData()
    {
        return EarthquakeEvent::select(
            'earthquake_events.*',
            'devices.nama_device as device_name',
            'devices.lokasi as device_location'
        )
        ->join('devices', 'earthquake_events.device_id', '=', 'devices.id')
        ->orderBy('occurred_at', 'desc')
        ->limit(1000)
        ->get();
    }

    private function getDevicesSummaryData()
{
    // Get device stats from earthquake events
    $stats = EarthquakeEvent::select(
        'device_id',
        DB::raw('COUNT(*) as total_events'),
        DB::raw('SUM(CASE WHEN status = "warning" THEN 1 ELSE 0 END) as warning_events'),
        DB::raw('SUM(CASE WHEN status = "danger" THEN 1 ELSE 0 END) as danger_events'),
        DB::raw('AVG(magnitude) as avg_magnitude'),
        DB::raw('MAX(magnitude) as max_magnitude'),
        DB::raw('MAX(occurred_at) as last_event_at')
    )
    ->groupBy('device_id')
    ->get()
    ->keyBy('device_id');

    // Get all devices
    $devices = Device::all();

    // Merge stats with devices
    return $devices->map(function ($device) use ($stats) {
        $stat = $stats->get($device->id);

        return [
            'id' => $device->id,
            'uuid' => $device->uuid,
            'nama_device' => $device->nama_device,
            'lokasi' => $device->lokasi,
            'status' => $device->status,
            'last_seen' => $device->last_seen,
            'created_at' => $device->created_at,
            'updated_at' => $device->updated_at,
            'total_events' => $stat ? $stat->total_events : 0,
            'warning_events' => $stat ? $stat->warning_events : 0,
            'danger_events' => $stat ? $stat->danger_events : 0,
            'avg_magnitude' => $stat ? round($stat->avg_magnitude, 2) : 0,
            'max_magnitude' => $stat ? round($stat->max_magnitude, 2) : 0,
            'last_event_at' => $stat ? $stat->last_event_at : null,
        ];
    });
}

    private function getFullReportData()
    {
        return [
            'summary' => $this->getBasicStats(),
            'recent_events' => EarthquakeEvent::with('device')->orderBy('occurred_at', 'desc')->limit(50)->get(),
            'device_stats' => $this->getDevicesSummaryData(),
            'hourly_pattern' => $this->getHourlyActivity(),
            'magnitude_distribution' => $this->getMagnitudeDistribution(),
            'generated_at' => now()->toDateTimeString()
        ];
    }

    private function exportToCSV($data, $filename)
    {
        if (empty($data)) {
            $csv = "No data available\n";
        } else {
            // Convert to array if it's a collection
            if (is_object($data) && method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }

            if (isset($data[0])) {
                // Get headers from first item
                $headers = array_keys((array) $data[0]);
                $csv = implode(',', $headers) . "\n";

                foreach ($data as $row) {
                    $row = (array) $row;
                    $csv .= implode(',', array_map(function($value) {
                        return '"' . str_replace('"', '""', $value) . '"';
                    }, $row)) . "\n";
                }
            } else {
                $csv = "Invalid data format\n";
            }
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get real-time statistics.
     */
    public function realTimeStats()
    {
        $stats = [
            'total_events_today' => EarthquakeEvent::whereDate('occurred_at', today())->count(),
            'warning_events_today' => EarthquakeEvent::whereDate('occurred_at', today())->where('status', 'warning')->count(),
            'danger_events_today' => EarthquakeEvent::whereDate('occurred_at', today())->where('status', 'danger')->count(),
            'active_devices' => Device::where('status', 'aktif')->count(),
            'inactive_devices' => Device::where('status', 'nonaktif')->count(),
            'last_hour_events' => EarthquakeEvent::where('occurred_at', '>=', now()->subHour())->count(),
            'last_event' => EarthquakeEvent::with('device')->orderBy('occurred_at', 'desc')->first(),
            'system_status' => 'operational',
            'last_updated' => now()->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timestamp' => now()->timestamp
        ]);
    }


}
