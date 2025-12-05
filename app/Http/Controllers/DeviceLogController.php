<?php

namespace App\Http\Controllers;

use App\Models\DeviceLog;
use App\Models\Device;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeviceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DeviceLog::with('device');

        // Apply filters
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('logged_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('logged_at', '<=', $request->date_to);
        }

        if ($request->filled('magnitude_min')) {
            $query->where('magnitude', '>=', $request->magnitude_min);
        }

        if ($request->filled('magnitude_max')) {
            $query->where('magnitude', '<=', $request->magnitude_max);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'logged_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => DeviceLog::count(),
            'today' => DeviceLog::whereDate('logged_at', today())->count(),
            'online' => DeviceLog::where('status', 'online')->count(),
            'offline' => DeviceLog::where('status', 'offline')->count(),
            'avg_magnitude' => DeviceLog::avg('magnitude') ?? 0,
            'max_magnitude' => DeviceLog::max('magnitude') ?? 0,
            'min_magnitude' => DeviceLog::min('magnitude') ?? 0,
        ];

        $devices = Device::where('status', 'aktif')->get();

        return view('device-logs.index', compact('logs', 'stats', 'devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $devices = Device::where('status', 'aktif')->get();
        return view('device-logs.create', compact('devices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'status' => ['required', 'in:online,offline'],
            'magnitude' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'logged_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $logData = [
            'device_id' => $request->device_id,
            'status' => $request->status,
            'magnitude' => $request->magnitude,
            'logged_at' => $request->logged_at,
        ];

        // Update device last_seen if status is online
        if ($request->status === 'online') {
            $device = Device::find($request->device_id);
            $device->update(['last_seen' => $request->logged_at]);
        }

        DeviceLog::create($logData);

        return redirect()->route('device-logs.index')
            ->with('success', 'Device log created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceLog $deviceLog)
    {
        $deviceLog->load('device');

        // Get similar logs (same device, same day)
        $similarLogs = DeviceLog::where('device_id', $deviceLog->device_id)
            ->whereDate('logged_at', $deviceLog->logged_at)
            ->where('id', '!=', $deviceLog->id)
            ->orderBy('logged_at', 'desc')
            ->limit(10)
            ->get();

        // Get statistics for this device
        $deviceStats = [
            'total_logs' => DeviceLog::where('device_id', $deviceLog->device_id)->count(),
            'today_logs' => DeviceLog::where('device_id', $deviceLog->device_id)
                ->whereDate('logged_at', today())
                ->count(),
            'avg_magnitude' => DeviceLog::where('device_id', $deviceLog->device_id)
                ->avg('magnitude') ?? 0,
            'max_magnitude' => DeviceLog::where('device_id', $deviceLog->device_id)
                ->max('magnitude') ?? 0,
        ];

        // Get previous and next logs
        $previousLog = DeviceLog::where('device_id', $deviceLog->device_id)
            ->where('logged_at', '<', $deviceLog->logged_at)
            ->orderBy('logged_at', 'desc')
            ->first();

        $nextLog = DeviceLog::where('device_id', $deviceLog->device_id)
            ->where('logged_at', '>', $deviceLog->logged_at)
            ->orderBy('logged_at', 'asc')
            ->first();

        return view('device-logs.show', compact('deviceLog', 'similarLogs', 'deviceStats', 'previousLog', 'nextLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeviceLog $deviceLog)
    {
        $devices = Device::where('status', 'aktif')->get();
        return view('device-logs.edit', compact('deviceLog', 'devices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeviceLog $deviceLog)
    {
        $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'status' => ['required', 'in:online,offline'],
            'magnitude' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'logged_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $deviceLog->status;

        $logData = [
            'device_id' => $request->device_id,
            'status' => $request->status,
            'magnitude' => $request->magnitude,
            'logged_at' => $request->logged_at,
        ];

        $deviceLog->update($logData);

        // Update device last_seen if status changed to online
        if ($oldStatus !== 'online' && $request->status === 'online') {
            $device = Device::find($request->device_id);
            $device->update(['last_seen' => $request->logged_at]);
        }

        return redirect()->route('device-logs.show', $deviceLog)
            ->with('success', 'Device log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceLog $deviceLog)
    {
        $deviceLog->delete();

        return redirect()->route('device-logs.index')
            ->with('success', 'Device log deleted successfully.');
    }

    /**
     * Bulk delete logs
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'log_ids' => ['required', 'array'],
            'log_ids.*' => ['exists:device_logs,id']
        ]);

        $deletedCount = DeviceLog::whereIn('id', $request->log_ids)->delete();

        return redirect()->route('device-logs.index')
            ->with('success', "Successfully deleted {$deletedCount} logs.");
    }

    /**
     * Clear old logs (older than specified days)
     */
    public function clearOldLogs(Request $request)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365']
        ]);

        $date = now()->subDays($request->days);
        $deletedCount = DeviceLog::where('logged_at', '<', $date)->delete();

        return redirect()->route('device-logs.index')
            ->with('success', "Successfully deleted {$deletedCount} logs older than {$request->days} days.");
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $request->validate([
            'device_id' => ['nullable', 'exists:devices,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:online,offline,all']
        ]);

        $query = DeviceLog::with('device');

        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->start_date) {
            $query->whereDate('logged_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('logged_at', '<=', $request->end_date);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('logged_at', 'desc')->get();

        // Generate CSV content
        $csvData = "ID,Device Name,Device Location,Status,Magnitude,Logged At,Device Status\n";

        foreach ($logs as $log) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%.2f,%s,%s\n",
                $log->id,
                $log->device->nama_device,
                $log->device->lokasi,
                $log->status,
                $log->magnitude ?? 0,
                $log->logged_at->format('Y-m-d H:i:s'),
                $log->device->status
            );
        }

        $filename = 'device-logs-' . date('Y-m-d-H-i-s') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get logs for chart (last 24 hours)
     */
    public function chartData($deviceId = null)
    {
        $query = DeviceLog::query();

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        $query->where('logged_at', '>=', now()->subHours(24))
              ->orderBy('logged_at');

        $logs = $query->get();

        $timestamps = [];
        $magnitudes = [];
        $statuses = [];

        foreach ($logs as $log) {
            $timestamps[] = $log->logged_at->format('H:i');
            $magnitudes[] = $log->magnitude ?? 0;
            $statuses[] = $log->status === 'online' ? 1 : 0;
        }

        return response()->json([
            'timestamps' => $timestamps,
            'magnitudes' => $magnitudes,
            'statuses' => $statuses,
            'avg_magnitude' => $logs->avg('magnitude') ?? 0,
            'online_percentage' => $logs->where('status', 'online')->count() / max($logs->count(), 1) * 100,
            'max_magnitude' => $logs->max('magnitude') ?? 0,
            'min_magnitude' => $logs->min('magnitude') ?? 0
        ]);
    }

    /**
     * Get recent logs (last 1 hour)
     */
    public function recentLogs()
    {
        $logs = DeviceLog::with('device')
            ->where('logged_at', '>=', now()->subHour())
            ->orderBy('logged_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'count' => $logs->count()
        ]);
    }

    /**
     * Get log statistics
     */
    public function statistics()
    {
        $today = today();
        $yesterday = today()->subDay();

        $stats = [
            'total' => DeviceLog::count(),
            'today' => DeviceLog::whereDate('logged_at', $today)->count(),
            'yesterday' => DeviceLog::whereDate('logged_at', $yesterday)->count(),
            'online' => DeviceLog::where('status', 'online')->count(),
            'offline' => DeviceLog::where('status', 'offline')->count(),
            'avg_magnitude' => DeviceLog::avg('magnitude') ?? 0,
            'max_magnitude' => DeviceLog::max('magnitude') ?? 0,
            'min_magnitude' => DeviceLog::min('magnitude') ?? 0,
            'most_active_device' => $this->getMostActiveDevice(),
            'recent_activity' => $this->getRecentActivity(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Get device health status
     */
    public function deviceHealth($deviceId)
    {
        $device = Device::findOrFail($deviceId);

        $logs = DeviceLog::where('device_id', $deviceId)
            ->where('logged_at', '>=', now()->subHours(24))
            ->orderBy('logged_at', 'desc')
            ->get();

        $health = [
            'device' => $device,
            'total_logs_24h' => $logs->count(),
            'online_logs' => $logs->where('status', 'online')->count(),
            'offline_logs' => $logs->where('status', 'offline')->count(),
            'uptime_percentage' => $logs->count() > 0 ? ($logs->where('status', 'online')->count() / $logs->count() * 100) : 0,
            'avg_magnitude' => $logs->avg('magnitude') ?? 0,
            'last_log' => $logs->first(),
            'recommendations' => $this->generateHealthRecommendations($logs, $device)
        ];

        return response()->json([
            'success' => true,
            'health' => $health
        ]);
    }

    /**
     * Simulate device log (for testing)
     */
    public function simulate(Request $request)
    {
        $request->validate([
            'device_id' => ['nullable', 'exists:devices,id'],
            'status' => ['nullable', 'in:online,offline'],
            'magnitude' => ['nullable', 'numeric', 'min:0', 'max:10']
        ]);

        $devices = Device::where('status', 'aktif')->get();

        if ($devices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active devices found.'
            ], 404);
        }

        $device = $request->device_id
            ? Device::find($request->device_id)
            : $devices->random();

        $status = $request->status ?? (rand(1, 10) > 2 ? 'online' : 'offline');
        $magnitude = $status === 'online'
            ? ($request->magnitude ?? rand(0, 100) / 10)
            : null;

        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $status,
            'magnitude' => $magnitude,
            'logged_at' => now(),
        ]);

        // Update device last_seen if online
        if ($status === 'online') {
            $device->update(['last_seen' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device log simulated successfully.',
            'log' => $log,
            'device_updated' => $status === 'online'
        ]);
    }

    /**
     * Get logs for data table (AJAX)
     */
    public function datatable(Request $request)
    {
        $query = DeviceLog::with('device');

        // Search
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhereHas('device', function($q2) use ($search) {
                      $q2->where('nama_device', 'like', "%{$search}%")
                         ->orWhere('lokasi', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by device
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('logged_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('logged_at', '<=', $request->date_to);
        }

        // Total records
        $total = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');
        $orderColumns = ['id', 'device_id', 'status', 'magnitude', 'logged_at'];
        $orderBy = $orderColumns[$orderColumn] ?? 'logged_at';

        $query->orderBy($orderBy, $orderDirection);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $logs = $query->offset($start)->limit($length)->get();

        // Format data for DataTables
        $data = $logs->map(function($log) {
            return [
                'id' => $log->id,
                'device' => [
                    'name' => $log->device->nama_device,
                    'location' => $log->device->lokasi,
                    'status' => $log->device->status
                ],
                'status' => $log->status,
                'magnitude' => $log->magnitude,
                'logged_at' => [
                    'formatted' => $log->logged_at->format('Y-m-d H:i:s'),
                    'timestamp' => $log->logged_at->timestamp,
                    'diff' => $log->logged_at->diffForHumans()
                ],
                'actions' => [
                    'view' => route('device-logs.show', $log),
                    'edit' => route('device-logs.edit', $log),
                    'delete' => route('device-logs.destroy', $log)
                ]
            ];
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    private function getMostActiveDevice()
    {
        $device = Device::withCount('logs')
            ->orderBy('logs_count', 'desc')
            ->first();

        return $device ? [
            'id' => $device->id,
            'name' => $device->nama_device,
            'location' => $device->lokasi,
            'log_count' => $device->logs_count
        ] : null;
    }

    private function getRecentActivity()
    {
        return DeviceLog::with('device')
            ->orderBy('logged_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'device_name' => $log->device->nama_device,
                    'status' => $log->status,
                    'magnitude' => $log->magnitude,
                    'time' => $log->logged_at->diffForHumans()
                ];
            });
    }

    private function generateHealthRecommendations($logs, $device)
    {
        $recommendations = [];

        // Check device status
        if ($device->status === 'nonaktif') {
            $recommendations[] = 'Device is marked as inactive. Consider activating it for monitoring.';
        }

        // Check last seen
        if (!$device->last_seen || $device->last_seen->diffInHours(now()) > 1) {
            $recommendations[] = 'Device has not sent data recently. Check connectivity or power supply.';
        }

        // Check offline logs
        $offlineCount = $logs->where('status', 'offline')->count();
        if ($offlineCount > 5) {
            $recommendations[] = "Device has been offline {$offlineCount} times in the last 24 hours. Investigate connection issues.";
        }

        // Check magnitude readings
        $avgMagnitude = $logs->avg('magnitude') ?? 0;
        if ($avgMagnitude > 3.0) {
            $recommendations[] = "Average magnitude is high ({$avgMagnitude}). Consider checking sensor calibration.";
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Device health is good. No issues detected.';
        }

        return $recommendations;
    }

    public function byDevice($deviceId)
{
    $device = Device::findOrFail($deviceId);

    $logs = DeviceLog::where('device_id', $deviceId)
        ->orderBy('logged_at', 'desc')
        ->paginate(20);

    $stats = [
        'total' => $logs->total(),
        'today' => DeviceLog::where('device_id', $deviceId)->whereDate('logged_at', today())->count(),
        'avg_magnitude' => DeviceLog::where('device_id', $deviceId)->avg('magnitude') ?? 0,
        'max_magnitude' => DeviceLog::where('device_id', $deviceId)->max('magnitude') ?? 0,
        'last_log' => DeviceLog::where('device_id', $deviceId)->latest('logged_at')->first(),
    ];

    return view('device-logs.device-logs', compact('logs', 'device', 'stats'));
}
}
