<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\Device;
use App\Models\EarthquakeEvent;
use App\Models\DeviceLog;
use App\Models\Threshold;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Export Users Data
     */
    public function exportUsers(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'role' => ['nullable', 'in:admin,user,all'],
            'status' => ['nullable', 'in:active,inactive,all']
        ]);

        $query = User::query();

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Note: Users don't have status field in your schema
        // If you add status field later, uncomment this:
        // if ($request->status && $request->status !== 'all') {
        //     $query->where('status', $request->status);
        // }

        $users = $query->orderBy('created_at', 'desc')->get();

        $format = $request->format ?? 'csv';

        if ($format === 'json') {
            return $this->exportUsersJson($users);
        } elseif ($format === 'excel') {
            return $this->exportUsersExcel($users);
        } else {
            return $this->exportUsersCsv($users);
        }
    }

    /**
     * Export Devices Data
     */
    public function exportDevices(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'in:aktif,nonaktif,all'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'format' => ['nullable', 'in:csv,json,excel']
        ]);

        $query = Device::withCount(['earthquakeEvents', 'logs']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $devices = $query->orderBy('created_at', 'desc')->get();

        $format = $request->format ?? 'csv';

        if ($format === 'json') {
            return $this->exportDevicesJson($devices);
        } elseif ($format === 'excel') {
            return $this->exportDevicesExcel($devices);
        } else {
            return $this->exportDevicesCsv($devices);
        }
    }

    /**
     * Export Earthquake Events Data
     */
    public function exportEarthquakeEvents(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:warning,danger,normal,all'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'min_magnitude' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'max_magnitude' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'format' => ['nullable', 'in:csv,json,excel']
        ]);

        $query = EarthquakeEvent::with('device');

        if ($request->start_date) {
            $query->whereDate('occurred_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('occurred_at', '<=', $request->end_date);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->min_magnitude) {
            $query->where('magnitude', '>=', $request->min_magnitude);
        }

        if ($request->max_magnitude) {
            $query->where('magnitude', '<=', $request->max_magnitude);
        }

        $events = $query->orderBy('occurred_at', 'desc')->get();

        $format = $request->format ?? 'csv';

        if ($format === 'json') {
            return $this->exportEventsJson($events);
        } elseif ($format === 'excel') {
            return $this->exportEventsExcel($events);
        } else {
            return $this->exportEventsCsv($events);
        }
    }

    /**
     * Export Device Logs Data
     */
    public function exportDeviceLogs(Request $request)
    {
        $request->validate([
            'device_id' => ['nullable', 'exists:devices,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string'],
            'format' => ['nullable', 'in:csv,json,excel']
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

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('logged_at', 'desc')->get();

        $format = $request->format ?? 'csv';

        if ($format === 'json') {
            return $this->exportLogsJson($logs);
        } elseif ($format === 'excel') {
            return $this->exportLogsExcel($logs);
        } else {
            return $this->exportLogsCsv($logs);
        }
    }

    /**
     * Export All Data (Combined)
     */
    public function exportAllData(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'format' => ['nullable', 'in:csv,json,excel'],
            'include' => ['nullable', 'array'],
            'include.*' => ['in:users,devices,events,logs,thresholds']
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format ?? 'csv';
        $include = $request->include ?? ['users', 'devices', 'events', 'logs', 'thresholds'];

        $data = [];

        if (in_array('users', $include)) {
            $query = User::query();
            if ($startDate) $query->whereDate('created_at', '>=', $startDate);
            if ($endDate) $query->whereDate('created_at', '<=', $endDate);
            $data['users'] = $query->orderBy('created_at', 'desc')->get();
        }

        if (in_array('devices', $include)) {
            $query = Device::withCount(['earthquakeEvents', 'logs']);
            if ($startDate) $query->whereDate('created_at', '>=', $startDate);
            if ($endDate) $query->whereDate('created_at', '<=', $endDate);
            $data['devices'] = $query->orderBy('created_at', 'desc')->get();
        }

        if (in_array('events', $include)) {
            $query = EarthquakeEvent::with('device');
            if ($startDate) $query->whereDate('occurred_at', '>=', $startDate);
            if ($endDate) $query->whereDate('occurred_at', '<=', $endDate);
            $data['events'] = $query->orderBy('occurred_at', 'desc')->get();
        }

        if (in_array('logs', $include)) {
            $query = DeviceLog::with('device');
            if ($startDate) $query->whereDate('logged_at', '>=', $startDate);
            if ($endDate) $query->whereDate('logged_at', '<=', $endDate);
            $data['logs'] = $query->orderBy('logged_at', 'desc')->get();
        }

        if (in_array('thresholds', $include)) {
            $data['thresholds'] = Threshold::all();
        }

        if ($format === 'json') {
            return $this->exportAllJson($data);
        } elseif ($format === 'excel') {
            return $this->exportAllExcel($data);
        } else {
            return $this->exportAllCsv($data);
        }
    }

    /**
     * Export Statistics Report
     */
    public function exportStatistics(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'report_type' => ['required', 'in:daily,weekly,monthly,yearly'],
            'format' => ['nullable', 'in:csv,json,excel']
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $reportType = $request->report_type;
        $format = $request->format ?? 'csv';

        $statistics = $this->generateStatistics($startDate, $endDate, $reportType);

        if ($format === 'json') {
            return $this->exportStatisticsJson($statistics, $reportType, $startDate, $endDate);
        } elseif ($format === 'excel') {
            return $this->exportStatisticsExcel($statistics, $reportType, $startDate, $endDate);
        } else {
            return $this->exportStatisticsCsv($statistics, $reportType, $startDate, $endDate);
        }
    }

    /**
     * Generate Statistics Data
     */
    private function generateStatistics($startDate, $endDate, $reportType)
    {
        $data = [];

        switch ($reportType) {
            case 'daily':
                $period = $startDate->toPeriod($endDate, '1 day');
                foreach ($period as $date) {
                    $data[] = $this->getDailyStats($date);
                }
                break;

            case 'weekly':
                $period = $startDate->toPeriod($endDate, '1 week');
                foreach ($period as $date) {
                    $data[] = $this->getWeeklyStats($date);
                }
                break;

            case 'monthly':
                $period = $startDate->toPeriod($endDate, '1 month');
                foreach ($period as $date) {
                    $data[] = $this->getMonthlyStats($date);
                }
                break;

            case 'yearly':
                $period = $startDate->toPeriod($endDate, '1 year');
                foreach ($period as $date) {
                    $data[] = $this->getYearlyStats($date);
                }
                break;
        }

        return $data;
    }

    private function getDailyStats($date)
    {
        return [
            'period' => $date->format('Y-m-d'),
            'total_events' => EarthquakeEvent::whereDate('occurred_at', $date)->count(),
            'warning_events' => EarthquakeEvent::whereDate('occurred_at', $date)->where('status', 'warning')->count(),
            'danger_events' => EarthquakeEvent::whereDate('occurred_at', $date)->where('status', 'danger')->count(),
            'max_magnitude' => EarthquakeEvent::whereDate('occurred_at', $date)->max('magnitude') ?? 0,
            'avg_magnitude' => EarthquakeEvent::whereDate('occurred_at', $date)->avg('magnitude') ?? 0,
            'device_logs' => DeviceLog::whereDate('logged_at', $date)->count(),
            'active_devices' => Device::where('status', 'aktif')->count(),
        ];
    }

    private function getWeeklyStats($date)
    {
        $weekStart = $date->copy()->startOfWeek();
        $weekEnd = $date->copy()->endOfWeek();

        return [
            'period' => $weekStart->format('Y-m-d') . ' to ' . $weekEnd->format('Y-m-d'),
            'total_events' => EarthquakeEvent::whereBetween('occurred_at', [$weekStart, $weekEnd])->count(),
            'warning_events' => EarthquakeEvent::whereBetween('occurred_at', [$weekStart, $weekEnd])->where('status', 'warning')->count(),
            'danger_events' => EarthquakeEvent::whereBetween('occurred_at', [$weekStart, $weekEnd])->where('status', 'danger')->count(),
            'max_magnitude' => EarthquakeEvent::whereBetween('occurred_at', [$weekStart, $weekEnd])->max('magnitude') ?? 0,
            'avg_magnitude' => EarthquakeEvent::whereBetween('occurred_at', [$weekStart, $weekEnd])->avg('magnitude') ?? 0,
            'device_logs' => DeviceLog::whereBetween('logged_at', [$weekStart, $weekEnd])->count(),
            'active_devices' => Device::where('status', 'aktif')->count(),
        ];
    }

    private function getMonthlyStats($date)
    {
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        return [
            'period' => $date->format('Y-m'),
            'total_events' => EarthquakeEvent::whereBetween('occurred_at', [$monthStart, $monthEnd])->count(),
            'warning_events' => EarthquakeEvent::whereBetween('occurred_at', [$monthStart, $monthEnd])->where('status', 'warning')->count(),
            'danger_events' => EarthquakeEvent::whereBetween('occurred_at', [$monthStart, $monthEnd])->where('status', 'danger')->count(),
            'max_magnitude' => EarthquakeEvent::whereBetween('occurred_at', [$monthStart, $monthEnd])->max('magnitude') ?? 0,
            'avg_magnitude' => EarthquakeEvent::whereBetween('occurred_at', [$monthStart, $monthEnd])->avg('magnitude') ?? 0,
            'device_logs' => DeviceLog::whereBetween('logged_at', [$monthStart, $monthEnd])->count(),
            'active_devices' => Device::where('status', 'aktif')->count(),
            'new_devices' => Device::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'new_users' => User::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
        ];
    }

    private function getYearlyStats($date)
    {
        $yearStart = $date->copy()->startOfYear();
        $yearEnd = $date->copy()->endOfYear();

        return [
            'period' => $date->format('Y'),
            'total_events' => EarthquakeEvent::whereBetween('occurred_at', [$yearStart, $yearEnd])->count(),
            'warning_events' => EarthquakeEvent::whereBetween('occurred_at', [$yearStart, $yearEnd])->where('status', 'warning')->count(),
            'danger_events' => EarthquakeEvent::whereBetween('occurred_at', [$yearStart, $yearEnd])->where('status', 'danger')->count(),
            'max_magnitude' => EarthquakeEvent::whereBetween('occurred_at', [$yearStart, $yearEnd])->max('magnitude') ?? 0,
            'avg_magnitude' => EarthquakeEvent::whereBetween('occurred_at', [$yearStart, $yearEnd])->avg('magnitude') ?? 0,
            'device_logs' => DeviceLog::whereBetween('logged_at', [$yearStart, $yearEnd])->count(),
            'total_devices' => Device::whereBetween('created_at', [$yearStart, $yearEnd])->count(),
            'total_users' => User::whereBetween('created_at', [$yearStart, $yearEnd])->count(),
        ];
    }

    /**
     * CSV Export Methods
     */
    private function exportUsersCsv($users)
    {
        $csvData = "ID,Name,Email,Role,Created At,Last Updated\n";

        foreach ($users as $user) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s\n",
                $user->id,
                $this->escapeCsv($user->name),
                $this->escapeCsv($user->email),
                $this->escapeCsv($user->role),
                $user->created_at->format('Y-m-d H:i:s'),
                $user->updated_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'users-export-' . date('Y-m-d-H-i-s') . '.csv';

        return $this->downloadCsv($csvData, $filename);
    }

    private function exportDevicesCsv($devices)
    {
        $csvData = "ID,UUID,Name,Location,Status,Last Seen,Total Events,Total Logs,Created At\n";

        foreach ($devices as $device) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%d,%d,%s\n",
                $device->id,
                $device->uuid,
                $this->escapeCsv($device->nama_device),
                $this->escapeCsv($device->lokasi),
                $device->status,
                $device->last_seen ? $device->last_seen->format('Y-m-d H:i:s') : 'N/A',
                $device->earthquake_events_count,
                $device->logs_count,
                $device->created_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'devices-export-' . date('Y-m-d-H-i-s') . '.csv';

        return $this->downloadCsv($csvData, $filename);
    }

    private function exportEventsCsv($events)
    {
        $csvData = "ID,Device Name,Device Location,Magnitude,Status,Occurred At,Latitude,Longitude,Depth,Description\n";

        foreach ($events as $event) {
            $csvData .= sprintf(
                "%d,%s,%s,%.1f,%s,%s,%s,%s,%s,%s\n",
                $event->id,
                $this->escapeCsv($event->device->nama_device),
                $this->escapeCsv($event->device->lokasi),
                $event->magnitude,
                $event->status,
                $event->occurred_at->format('Y-m-d H:i:s'),
                $event->latitude ?? 'N/A',
                $event->longitude ?? 'N/A',
                $event->depth ?? 'N/A',
                $this->escapeCsv($event->description ?? 'N/A')
            );
        }

        $filename = 'earthquake-events-export-' . date('Y-m-d-H-i-s') . '.csv';

        return $this->downloadCsv($csvData, $filename);
    }

    private function exportLogsCsv($logs)
    {
        $csvData = "ID,Device Name,Device Location,Status,Magnitude,Logged At\n";

        foreach ($logs as $log) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s\n",
                $log->id,
                $this->escapeCsv($log->device->nama_device),
                $this->escapeCsv($log->device->lokasi),
                $log->status ?? 'N/A',
                $log->magnitude ?? 'N/A',
                $log->logged_at->format('Y-m-d H:i:s')
            );
        }

        $filename = 'device-logs-export-' . date('Y-m-d-H-i-s') . '.csv';

        return $this->downloadCsv($csvData, $filename);
    }

    private function exportAllCsv($data)
    {
        $zipFilename = 'eqmonitor-full-export-' . date('Y-m-d-H-i-s') . '.zip';
        $tempDir = sys_get_temp_dir() . '/export-' . uniqid();

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Export each section to separate CSV files
        if (isset($data['users'])) {
            $this->createCsvFile($tempDir . '/users.csv', $this->exportUsersCsv($data['users'])->getContent());
        }

        if (isset($data['devices'])) {
            $this->createCsvFile($tempDir . '/devices.csv', $this->exportDevicesCsv($data['devices'])->getContent());
        }

        if (isset($data['events'])) {
            $this->createCsvFile($tempDir . '/earthquake-events.csv', $this->exportEventsCsv($data['events'])->getContent());
        }

        if (isset($data['logs'])) {
            $this->createCsvFile($tempDir . '/device-logs.csv', $this->exportLogsCsv($data['logs'])->getContent());
        }

        if (isset($data['thresholds'])) {
            $csvData = "ID,Min Value,Description,Created At\n";
            foreach ($data['thresholds'] as $threshold) {
                $csvData .= sprintf(
                    "%d,%.1f,%s,%s\n",
                    $threshold->id,
                    $threshold->min_value,
                    $threshold->description,
                    $threshold->created_at->format('Y-m-d H:i:s')
                );
            }
            $this->createCsvFile($tempDir . '/thresholds.csv', $csvData);
        }

        // Create README file
        $readme = "Earthquake Monitoring System - Data Export\n";
        $readme .= "Generated on: " . date('Y-m-d H:i:s') . "\n";
        $readme .= "Files included:\n";
        $readme .= "- users.csv: User accounts data\n";
        $readme .= "- devices.csv: Device information and statistics\n";
        $readme .= "- earthquake-events.csv: Earthquake event records\n";
        $readme .= "- device-logs.csv: Device status logs\n";
        $readme .= "- thresholds.csv: System threshold settings\n";
        $this->createCsvFile($tempDir . '/README.txt', $readme);

        // Create zip file
        $zip = new \ZipArchive();
        $zipPath = $tempDir . '/' . $zipFilename;

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $zip->addFile($file, basename($file));
                }
            }
            $zip->close();
        }

        // Send zip file
        $response = Response::download($zipPath, $zipFilename);

        // Cleanup
        $this->deleteDirectory($tempDir);

        return $response;
    }

    private function exportStatisticsCsv($statistics, $reportType, $startDate, $endDate)
    {
        $headers = [
            'daily' => 'Date,Total Events,Warning Events,Danger Events,Max Magnitude,Avg Magnitude,Device Logs,Active Devices',
            'weekly' => 'Week,Total Events,Warning Events,Danger Events,Max Magnitude,Avg Magnitude,Device Logs,Active Devices',
            'monthly' => 'Month,Total Events,Warning Events,Danger Events,Max Magnitude,Avg Magnitude,Device Logs,Active Devices,New Devices,New Users',
            'yearly' => 'Year,Total Events,Warning Events,Danger Events,Max Magnitude,Avg Magnitude,Device Logs,Total Devices,Total Users'
        ];

        $csvData = "Earthquake Monitoring System - Statistics Report\n";
        $csvData .= "Report Type: " . ucfirst($reportType) . "\n";
        $csvData .= "Period: " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d') . "\n";
        $csvData .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $csvData .= $headers[$reportType] . "\n";

        foreach ($statistics as $stat) {
            if ($reportType === 'daily') {
                $csvData .= sprintf(
                    "%s,%d,%d,%d,%.1f,%.1f,%d,%d\n",
                    $stat['period'],
                    $stat['total_events'],
                    $stat['warning_events'],
                    $stat['danger_events'],
                    $stat['max_magnitude'],
                    $stat['avg_magnitude'],
                    $stat['device_logs'],
                    $stat['active_devices']
                );
            } elseif ($reportType === 'weekly') {
                $csvData .= sprintf(
                    "%s,%d,%d,%d,%.1f,%.1f,%d,%d\n",
                    $this->escapeCsv($stat['period']),
                    $stat['total_events'],
                    $stat['warning_events'],
                    $stat['danger_events'],
                    $stat['max_magnitude'],
                    $stat['avg_magnitude'],
                    $stat['device_logs'],
                    $stat['active_devices']
                );
            } elseif ($reportType === 'monthly') {
                $csvData .= sprintf(
                    "%s,%d,%d,%d,%.1f,%.1f,%d,%d,%d,%d\n",
                    $stat['period'],
                    $stat['total_events'],
                    $stat['warning_events'],
                    $stat['danger_events'],
                    $stat['max_magnitude'],
                    $stat['avg_magnitude'],
                    $stat['device_logs'],
                    $stat['active_devices'],
                    $stat['new_devices'] ?? 0,
                    $stat['new_users'] ?? 0
                );
            } elseif ($reportType === 'yearly') {
                $csvData .= sprintf(
                    "%s,%d,%d,%d,%.1f,%.1f,%d,%d,%d\n",
                    $stat['period'],
                    $stat['total_events'],
                    $stat['warning_events'],
                    $stat['danger_events'],
                    $stat['max_magnitude'],
                    $stat['avg_magnitude'],
                    $stat['device_logs'],
                    $stat['total_devices'] ?? 0,
                    $stat['total_users'] ?? 0
                );
            }
        }

        $filename = 'statistics-' . $reportType . '-' . date('Y-m-d-H-i-s') . '.csv';

        return $this->downloadCsv($csvData, $filename);
    }

    /**
     * JSON Export Methods
     */
    private function exportUsersJson($users)
    {
        $data = [
            'metadata' => [
                'export_type' => 'users',
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => $users->count()
            ],
            'data' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString()
                ];
            })
        ];

        $filename = 'users-export-' . date('Y-m-d-H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportDevicesJson($devices)
    {
        $data = [
            'metadata' => [
                'export_type' => 'devices',
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => $devices->count()
            ],
            'data' => $devices->map(function($device) {
                return [
                    'id' => $device->id,
                    'uuid' => $device->uuid,
                    'name' => $device->nama_device,
                    'location' => $device->lokasi,
                    'status' => $device->status,
                    'last_seen' => $device->last_seen ? $device->last_seen->toISOString() : null,
                    'total_events' => $device->earthquake_events_count,
                    'total_logs' => $device->logs_count,
                    'created_at' => $device->created_at->toISOString(),
                    'updated_at' => $device->updated_at->toISOString()
                ];
            })
        ];

        $filename = 'devices-export-' . date('Y-m-d-H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportEventsJson($events)
    {
        $data = [
            'metadata' => [
                'export_type' => 'earthquake_events',
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => $events->count()
            ],
            'data' => $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'device' => [
                        'id' => $event->device->id,
                        'name' => $event->device->nama_device,
                        'location' => $event->device->lokasi
                    ],
                    'magnitude' => $event->magnitude,
                    'status' => $event->status,
                    'occurred_at' => $event->occurred_at->toISOString(),
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                    'depth' => $event->depth,
                    'description' => $event->description,
                    'created_at' => $event->created_at->toISOString()
                ];
            })
        ];

        $filename = 'earthquake-events-export-' . date('Y-m-d-H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportAllJson($data)
    {
        $exportData = [
            'metadata' => [
                'export_type' => 'full_system',
                'export_date' => date('Y-m-d H:i:s'),
                'files_included' => array_keys($data)
            ]
        ];

        if (isset($data['users'])) {
            $exportData['users'] = $data['users']->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->toISOString()
                ];
            });
        }

        if (isset($data['devices'])) {
            $exportData['devices'] = $data['devices']->map(function($device) {
                return [
                    'id' => $device->id,
                    'uuid' => $device->uuid,
                    'name' => $device->nama_device,
                    'location' => $device->lokasi,
                    'status' => $device->status,
                    'last_seen' => $device->last_seen ? $device->last_seen->toISOString() : null,
                    'total_events' => $device->earthquake_events_count,
                    'total_logs' => $device->logs_count,
                    'created_at' => $device->created_at->toISOString()
                ];
            });
        }

        if (isset($data['events'])) {
            $exportData['earthquake_events'] = $data['events']->map(function($event) {
                return [
                    'id' => $event->id,
                    'device_id' => $event->device_id,
                    'magnitude' => $event->magnitude,
                    'status' => $event->status,
                    'occurred_at' => $event->occurred_at->toISOString(),
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                    'depth' => $event->depth,
                    'description' => $event->description,
                    'created_at' => $event->created_at->toISOString()
                ];
            });
        }

        if (isset($data['logs'])) {
            $exportData['device_logs'] = $data['logs']->map(function($log) {
                return [
                    'id' => $log->id,
                    'device_id' => $log->device_id,
                    'status' => $log->status,
                    'magnitude' => $log->magnitude,
                    'logged_at' => $log->logged_at->toISOString()
                ];
            });
        }

        if (isset($data['thresholds'])) {
            $exportData['thresholds'] = $data['thresholds']->map(function($threshold) {
                return [
                    'id' => $threshold->id,
                    'min_value' => $threshold->min_value,
                    'description' => $threshold->description,
                    'created_at' => $threshold->created_at->toISOString()
                ];
            });
        }

        $filename = 'eqmonitor-full-export-' . date('Y-m-d-H-i-s') . '.json';

        return response()->json($exportData)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportStatisticsJson($statistics, $reportType, $startDate, $endDate)
    {
        $data = [
            'metadata' => [
                'report_type' => $reportType,
                'period_start' => $startDate->toISOString(),
                'period_end' => $endDate->toISOString(),
                'generated_at' => date('Y-m-d H:i:s'),
                'total_periods' => count($statistics)
            ],
            'statistics' => $statistics
        ];

        $filename = 'statistics-' . $reportType . '-' . date('Y-m-d-H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Excel Export Methods (Simple CSV format as Excel)
     */
    private function exportUsersExcel($users)
    {
        return $this->exportUsersCsv($users);
    }

    private function exportDevicesExcel($devices)
    {
        return $this->exportDevicesCsv($devices);
    }

    private function exportEventsExcel($events)
    {
        return $this->exportEventsCsv($events);
    }

    private function exportLogsExcel($logs)
    {
        return $this->exportLogsCsv($logs);
    }

    private function exportAllExcel($data)
    {
        return $this->exportAllCsv($data);
    }

    private function exportStatisticsExcel($statistics, $reportType, $startDate, $endDate)
    {
        return $this->exportStatisticsCsv($statistics, $reportType, $startDate, $endDate);
    }

    /**
     * Helper Methods
     */
    private function escapeCsv($value)
    {
        if (is_null($value)) {
            return '';
        }

        // Escape quotes and wrap in quotes if contains comma, quote, or newline
        if (strpos($value, '"') !== false || strpos($value, ',') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }

    private function downloadCsv($csvData, $filename)
    {
        return Response::make($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function createCsvFile($path, $content)
    {
        file_put_contents($path, $content);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    private function exportLogsJson($logs)
{
    $data = [
        'metadata' => [
            'export_type' => 'device_logs',
            'export_date' => date('Y-m-d H:i:s'),
            'total_records' => $logs->count()
        ],
        'data' => $logs->map(function($log) {
            return [
                'id' => $log->id,
                'device' => [
                    'id' => $log->device->id,
                    'name' => $log->device->nama_device,
                    'location' => $log->device->lokasi
                ],
                'status' => $log->status,
                'magnitude' => $log->magnitude,
                'logged_at' => $log->logged_at->toISOString(),
                'created_at' => $log->created_at ? $log->created_at->toISOString() : null
            ];
        })
    ];

    $filename = 'device-logs-export-' . date('Y-m-d-H-i-s') . '.json';

    return response()->json($data)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}


}
