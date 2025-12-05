<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::withCount(['earthquakeEvents', 'logs'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('devices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_device' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $deviceData = [
            'uuid' => Str::uuid(),
            'nama_device' => $request->nama_device,
            'lokasi' => $request->lokasi,
            'status' => $request->status,
            'last_seen' => $request->status === 'aktif' ? now() : null,
        ];

        Device::create($deviceData);

        return redirect()->route('devices.index')
            ->with('success', 'Device created successfully.');
    }

    /**
     * Display the specified resource.
     */
    /**
 * Display the specified resource.
 */
    public function show(Device $device)
    {
        // Load relationships with proper ordering
        $device->load([
            'earthquakeEvents' => function($query) {
                $query->orderBy('occurred_at', 'desc')->limit(10);
            },
            'logs' => function($query) {
                $query->orderBy('logged_at', 'desc')->limit(10);
            }
        ]);

        // Get device statistics
        $statistics = [
            'total_events' => $device->earthquakeEvents()->count(),
            'warning_events' => $device->earthquakeEvents()->where('status', 'warning')->count(),
            'danger_events' => $device->earthquakeEvents()->where('status', 'danger')->count(),
            'today_logs' => $device->logs()->whereDate('logged_at', today())->count(),
            'last_event' => $device->earthquakeEvents()->orderBy('occurred_at', 'desc')->first(),
            'last_log' => $device->logs()->orderBy('logged_at', 'desc')->first(),
        ];

        // Get events for chart (last 7 days)
        $chartData = $this->getDeviceChartData($device);

        return view('devices.show', compact('device', 'statistics', 'chartData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        $request->validate([
            'nama_device' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $deviceData = [
            'nama_device' => $request->nama_device,
            'lokasi' => $request->lokasi,
            'status' => $request->status,
            'last_seen' => $request->status === 'aktif' ? now() : null,
        ];

        $device->update($deviceData);

        return redirect()->route('devices.index')
            ->with('success', 'Device updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    /**
     * Update device status
     */
    public function updateStatus(Request $request, Device $device)
    {
        $request->validate([
            'status' => ['required', 'in:aktif,nonaktif']
        ]);

        $device->update([
            'status' => $request->status,
            'last_seen' => $request->status === 'aktif' ? now() : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device status updated successfully.',
            'device' => $device
        ]);
    }

    /**
     * Simulate device heartbeat (update last_seen)
     */
    public function heartbeat(Device $device)
    {
        $device->update(['last_seen' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Device heartbeat recorded.',
            'last_seen' => $device->last_seen
        ]);
    }

    /**
     * Generate QR Code for device
     */
    public function generateQrCode(Device $device)
    {
        $qrData = [
            'device_id' => $device->id,
            'uuid' => $device->uuid,
            'name' => $device->nama_device,
            'location' => $device->lokasi
        ];

        // In a real app, you would generate an actual QR code
        // For this example, we'll just return the data
        return response()->json([
            'success' => true,
            'qr_data' => json_encode($qrData),
            'download_url' => '#'
        ]);
    }

    /**
     * Get device statistics for chart
     */
    private function getDeviceChartData(Device $device)
    {
        $dates = [];
        $warningData = [];
        $dangerData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('M d');

            $warningCount = $device->earthquakeEvents()
                ->where('status', 'warning')
                ->whereDate('occurred_at', $date)
                ->count();

            $dangerCount = $device->earthquakeEvents()
                ->where('status', 'danger')
                ->whereDate('occurred_at', $date)
                ->count();

            $warningData[] = $warningCount;
            $dangerData[] = $dangerCount;
        }

        return [
            'dates' => $dates,
            'warning' => $warningData,
            'danger' => $dangerData
        ];
    }

    /**
     * Get offline devices
     */
    public function offlineDevices()
    {
        $offlineThreshold = now()->subMinutes(5); // Consider offline if no heartbeat for 5 minutes

        $offlineDevices = Device::where('status', 'aktif')
            ->where(function($query) use ($offlineThreshold) {
                $query->whereNull('last_seen')
                      ->orWhere('last_seen', '<', $offlineThreshold);
            })
            ->get();

        return response()->json([
            'success' => true,
            'count' => $offlineDevices->count(),
            'devices' => $offlineDevices
        ]);
    }
}
