<?php

namespace App\Http\Controllers;

use App\Models\EarthquakeEvent;
use App\Models\Device;
use App\Models\DeviceLog; // Tambahkan ini
use App\Models\Threshold;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EarthquakeEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = EarthquakeEvent::with('device')
            ->orderBy('occurred_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => EarthquakeEvent::count(),
            'today' => EarthquakeEvent::whereDate('occurred_at', today())->count(),
            'warning' => EarthquakeEvent::where('status', 'warning')->count(),
            'danger' => EarthquakeEvent::where('status', 'danger')->count(),
        ];

        return view('earthquake-events.index', compact('events', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $devices = Device::where('status', 'aktif')->get();
        $thresholds = Threshold::orderBy('min_value')->get();

        return view('earthquake-events.create', compact('devices', 'thresholds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'magnitude' => ['required', 'numeric', 'min:0', 'max:10'],
            'occurred_at' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'depth' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        // Determine status based on thresholds
        $thresholds = Threshold::orderBy('min_value', 'desc')->get();
        $status = 'normal';

        foreach ($thresholds as $threshold) {
            if ($request->magnitude >= $threshold->min_value) {
                $status = $threshold->description;
                break;
            }
        }

        $device = Device::find($request->device_id);

        $eventData = [
            'device_id' => $request->device_id,
            'magnitude' => $request->magnitude,
            'status' => $status,
            'occurred_at' => $request->occurred_at,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'depth' => $request->depth,
            'description' => $request->description,
        ];

        $event = EarthquakeEvent::create($eventData);

        // Log earthquake event creation to DeviceLog
        $this->logEarthquakeEvent($device, $event, 'event_created', [
            'event_id' => $event->id,
            'magnitude' => $event->magnitude,
            'status' => $event->status,
            'occurred_at' => $event->occurred_at->format('Y-m-d H:i:s'),
            'description' => $event->description
        ]);

        return redirect()->route('earthquake-events.index')
            ->with('success', 'Earthquake event recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EarthquakeEvent $earthquakeEvent)
    {
        $earthquakeEvent->load('device');

        $similarEvents = EarthquakeEvent::where('device_id', $earthquakeEvent->device_id)
            ->whereDate('occurred_at', $earthquakeEvent->occurred_at)
            ->where('id', '!=', $earthquakeEvent->id)
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get();

        $deviceStats = [
            'total_events' => $earthquakeEvent->device->earthquakeEvents()->count(),
            'max_magnitude' => $earthquakeEvent->device->earthquakeEvents()->max('magnitude'),
            'avg_magnitude' => $earthquakeEvent->device->earthquakeEvents()->avg('magnitude'),
            'today_events' => $earthquakeEvent->device->earthquakeEvents()->whereDate('occurred_at', today())->count(),
        ];

        return view('earthquake-events.show', compact('earthquakeEvent', 'similarEvents', 'deviceStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EarthquakeEvent $earthquakeEvent)
    {
        $devices = Device::where('status', 'aktif')->get();
        $thresholds = Threshold::orderBy('min_value')->get();

        return view('earthquake-events.edit', compact('earthquakeEvent', 'devices', 'thresholds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EarthquakeEvent $earthquakeEvent)
    {
        $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'magnitude' => ['required', 'numeric', 'min:0', 'max:10'],
            'occurred_at' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'depth' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $thresholds = Threshold::orderBy('min_value', 'desc')->get();
        $status = 'normal';

        foreach ($thresholds as $threshold) {
            if ($request->magnitude >= $threshold->min_value) {
                $status = $threshold->description;
                break;
            }
        }

        // Simpan data sebelum diubah untuk logging
        $oldData = [
            'device_id' => $earthquakeEvent->device_id,
            'magnitude' => $earthquakeEvent->magnitude,
            'status' => $earthquakeEvent->status,
            'occurred_at' => $earthquakeEvent->occurred_at,
            'description' => $earthquakeEvent->description,
        ];

        $eventData = [
            'device_id' => $request->device_id,
            'magnitude' => $request->magnitude,
            'status' => $status,
            'occurred_at' => $request->occurred_at,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'depth' => $request->depth,
            'description' => $request->description,
        ];

        // Get device (could be changed)
        $device = Device::find($request->device_id);

        $earthquakeEvent->update($eventData);

        // Log earthquake event update to DeviceLog
        $changes = $this->getEventChanges($oldData, $eventData);
        $this->logEarthquakeEvent($device, $earthquakeEvent, 'event_updated', [
            'event_id' => $earthquakeEvent->id,
            'changes' => $changes,
            'old_magnitude' => $oldData['magnitude'],
            'new_magnitude' => $eventData['magnitude'],
            'old_status' => $oldData['status'],
            'new_status' => $eventData['status']
        ]);

        return redirect()->route('earthquake-events.show', $earthquakeEvent)
            ->with('success', 'Earthquake event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EarthquakeEvent $earthquakeEvent)
    {
        $device = $earthquakeEvent->device;

        // Log earthquake event deletion to DeviceLog
        $this->logEarthquakeEvent($device, $earthquakeEvent, 'event_deleted', [
            'event_id' => $earthquakeEvent->id,
            'magnitude' => $earthquakeEvent->magnitude,
            'status' => $earthquakeEvent->status,
            'occurred_at' => $earthquakeEvent->occurred_at->format('Y-m-d H:i:s'),
            'description' => $earthquakeEvent->description
        ]);

        $earthquakeEvent->delete();

        return redirect()->route('earthquake-events.index')
            ->with('success', 'Earthquake event deleted successfully.');
    }

    /**
     * Get events for chart (last 30 days) - API
     */
    public function chartData()
    {
        try {
            $dates = [];
            $warningData = [];
            $dangerData = [];

            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $dates[] = $date->format('M d');

                $warningCount = EarthquakeEvent::where('status', 'warning')
                    ->whereDate('occurred_at', $dateStr)
                    ->count();

                $dangerCount = EarthquakeEvent::where('status', 'danger')
                    ->whereDate('occurred_at', $dateStr)
                    ->count();

                $warningData[] = $warningCount;
                $dangerData[] = $dangerCount;
            }

            return response()->json([
                'success' => true,
                'dates' => $dates,
                'warning' => $warningData,
                'danger' => $dangerData,
                'total_warning' => array_sum($warningData),
                'total_danger' => array_sum($dangerData)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading chart data',
                'dates' => [],
                'warning' => [],
                'danger' => []
            ], 500);
        }
    }

    /**
     * Get recent events (last 24 hours) - API
     */
    public function recentEvents()
    {
        try {
            $events = EarthquakeEvent::with('device')
                ->where('occurred_at', '>=', Carbon::now()->subHours(24))
                ->orderBy('occurred_at', 'desc')
                ->limit(5)
                ->get();

            $formattedEvents = $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'device' => [
                        'nama_device' => $event->device->nama_device ?? 'Unknown Device',
                        'lokasi' => $event->device->lokasi ?? 'Unknown Location'
                    ],
                    'magnitude' => $event->magnitude,
                    'status' => $event->status,
                    'occurred_at' => $event->occurred_at->toISOString(),
                    'time_ago' => $event->occurred_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $formattedEvents,
                'count' => $events->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading recent events',
                'events' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get earthquake statistics - API
     */
    public function statistics()
    {
        try {
            $today = today();
            $yesterday = today()->subDay();

            $stats = [
                'total' => EarthquakeEvent::count(),
                'today' => EarthquakeEvent::whereDate('occurred_at', $today)->count(),
                'yesterday' => EarthquakeEvent::whereDate('occurred_at', $yesterday)->count(),
                'warning' => EarthquakeEvent::where('status', 'warning')->count(),
                'danger' => EarthquakeEvent::where('status', 'danger')->count(),
                'max_magnitude' => EarthquakeEvent::max('magnitude') ?? 0,
                'avg_magnitude' => EarthquakeEvent::avg('magnitude') ?? 0,
                'most_active_device' => $this->getMostActiveDevice(),
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics'
            ], 500);
        }
    }

    private function getMostActiveDevice()
    {
        $device = Device::withCount('earthquakeEvents')
            ->orderBy('earthquake_events_count', 'desc')
            ->first();

        return $device ? [
            'id' => $device->id,
            'name' => $device->nama_device,
            'location' => $device->lokasi,
            'event_count' => $device->earthquake_events_count
        ] : null;
    }

    /**
     * Get events by device
     */
    public function byDevice($deviceId)
    {
        $events = EarthquakeEvent::where('device_id', $deviceId)
            ->orderBy('occurred_at', 'desc')
            ->paginate(10);

        $device = Device::findOrFail($deviceId);

        return view('earthquake-events.device-events', compact('events', 'device'));
    }

    /**
     * Simulate earthquake event
     */
    public function simulate()
    {
        try {
            $devices = Device::where('status', 'aktif')->get();

            if ($devices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active devices found.'
                ], 404);
            }

            $device = $devices->random();
            $magnitude = rand(20, 80) / 10; // 2.0 to 8.0
            $thresholds = Threshold::orderBy('min_value', 'desc')->get();
            $status = 'normal';

            foreach ($thresholds as $threshold) {
                if ($magnitude >= $threshold->min_value) {
                    $status = $threshold->description;
                    break;
                }
            }

            $event = EarthquakeEvent::create([
                'device_id' => $device->id,
                'magnitude' => $magnitude,
                'status' => $status,
                'occurred_at' => now(),
                'description' => 'Simulated earthquake event for testing.'
            ]);

            // Log simulated earthquake event to DeviceLog
            $this->logEarthquakeEvent($device, $event, 'event_simulated', [
                'event_id' => $event->id,
                'magnitude' => $event->magnitude,
                'status' => $event->status,
                'simulated' => true,
                'action' => 'earthquake_simulation'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Earthquake event simulated successfully.',
                'event' => $event,
                'alert' => $status !== 'normal'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error simulating event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export events to CSV
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:warning,danger,all']
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

        $events = $query->orderBy('occurred_at', 'desc')->get();

        // Generate CSV content
        $csvData = "ID,Device Name,Location,Magnitude,Status,Occurred At,Latitude,Longitude,Depth,Description\n";

        foreach ($events as $event) {
            $csvData .= sprintf(
                "%d,%s,%s,%.1f,%s,%s,%s,%s,%s,%s\n",
                $event->id,
                $event->device->nama_device,
                $event->device->lokasi,
                $event->magnitude,
                $event->status,
                $event->occurred_at->format('Y-m-d H:i:s'),
                $event->latitude ?? 'N/A',
                $event->longitude ?? 'N/A',
                $event->depth ?? 'N/A',
                str_replace(',', ';', $event->description ?? 'N/A')
            );
        }

        $filename = 'earthquake-events-' . date('Y-m-d-H-i-s') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Log earthquake event to DeviceLog
     * Mirip dengan fungsi simulate() di DeviceLog
     */
    private function logEarthquakeEvent(Device $device, EarthquakeEvent $event, string $action, array $metadata = [])
    {
        // Status untuk earthquake event selalu "online" karena ini adalah event aktif
        $status = 'online';

        // Gunakan magnitude dari earthquake event untuk DeviceLog
        $magnitude = $event->magnitude;

        // Log event ke DeviceLog
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $status,
            'magnitude' => $magnitude,
            'logged_at' => now(),
            'action' => $action,
            'metadata' => json_encode($metadata),
        ]);

        // Update device last_seen karena ada aktivitas baru
        $device->update(['last_seen' => now()]);

        return $log;
    }

    /**
     * Get changes between old and new earthquake event data
     */
    private function getEventChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($oldData as $key => $oldValue) {
            $newValue = $newData[$key] ?? null;

            // Format dates for comparison
            if ($key === 'occurred_at') {
                $oldValue = $oldValue instanceof \Carbon\Carbon ? $oldValue->format('Y-m-d H:i:s') : $oldValue;
                $newValue = $newValue instanceof \Carbon\Carbon ? $newValue->format('Y-m-d H:i:s') : $newValue;
            }

            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'from' => $oldValue,
                    'to' => $newValue
                ];
            }
        }

        return $changes;
    }
}
