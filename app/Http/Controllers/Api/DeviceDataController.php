<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\EarthquakeEvent;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DeviceDataController extends Controller
{
    /**
     * Receive data from IoT device
     */
    public function receiveData(Request $request, $uuid)
    {
        // Find device by UUID
        $device = Device::where('uuid', $uuid)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'vibration' => ['required', 'numeric', 'min:0', 'max:1023'],
            'status' => ['required', 'in:online,offline'],
            'temperature' => ['nullable', 'numeric'],
            'humidity' => ['nullable', 'numeric'],
            'battery' => ['nullable', 'numeric', 'min:0, max:100'],
            'timestamp' => ['nullable', 'date']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Convert vibration to magnitude (0-10 scale)
        $vibration = $request->input('vibration');
        $magnitude = $this->convertToMagnitude($vibration);

        // Create device log
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => $request->input('status'),
            'magnitude' => $magnitude,
            'logged_at' => $request->input('timestamp') ?? now(),
        ]);

        // Update device last_seen
        $device->update([
            'last_seen' => now(),
            'status' => 'aktif'
        ]);

        // Check if earthquake event should be created (magnitude >= 3.0)
        $earthquakeEvent = null;
        $alertType = null;

        if ($magnitude >= 3.0) {
            $earthquakeEvent = $this->createEarthquakeEvent($device, $magnitude);
            $alertType = $this->triggerAlerts($device, $magnitude, $earthquakeEvent);
        }

        // Log the data reception
        Log::info('IoT Data Received', [
            'device' => $device->nama_device,
            'vibration' => $vibration,
            'magnitude' => $magnitude,
            'event_created' => $earthquakeEvent ? 'yes' : 'no',
            'alert_triggered' => $alertType ?: 'none'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data received successfully.',
            'data' => [
                'device_id' => $device->id,
                'device_name' => $device->nama_device,
                'vibration' => $vibration,
                'magnitude' => $magnitude,
                'magnitude_level' => $this->getMagnitudeLevel($magnitude),
                'log_id' => $log->id,
                'earthquake_event' => $earthquakeEvent ? [
                    'id' => $earthquakeEvent->id,
                    'magnitude' => $earthquakeEvent->magnitude,
                    'status' => $earthquakeEvent->status,
                    'alert' => true,
                    'alert_type' => $alertType
                ] : null,
                'device_updated' => true,
                'threshold_exceeded' => $magnitude >= 3.0
            ]
        ]);
    }

    /**
     * Convert vibration reading to magnitude
     */
    private function convertToMagnitude($vibration)
    {
        // SW-420 vibration sensor typically gives 0-1023
        // Convert to 0-10 magnitude scale

        if ($vibration < 50) return 0; // No vibration
        if ($vibration < 200) return $vibration / 200 * 2; // 0-2 magnitude
        if ($vibration < 500) return 2 + ($vibration - 200) / 300 * 3; // 2-5 magnitude
        return 5 + ($vibration - 500) / 523 * 5; // 5-10 magnitude
    }

    /**
     * Create earthquake event if magnitude >= 3.0
     */
    private function createEarthquakeEvent($device, $magnitude)
    {
        // Determine status based on thresholds
        $thresholds = Threshold::orderBy('min_value', 'desc')->get();
        $status = 'normal';

        foreach ($thresholds as $threshold) {
            if ($magnitude >= $threshold->min_value) {
                $status = $threshold->description;
                break;
            }
        }

        // Create earthquake event
        return EarthquakeEvent::create([
            'device_id' => $device->id,
            'magnitude' => $magnitude,
            'status' => $status,
            'occurred_at' => now(),
            'latitude' => $device->latitude ?? null,
            'longitude' => $device->longitude ?? null,
            'description' => 'Automatically detected by SW-420 sensor. Vibration converted to magnitude: ' . number_format($magnitude, 1)
        ]);
    }

    /**
     * Trigger alerts for significant earthquakes
     */
    private function triggerAlerts($device, $magnitude, $earthquakeEvent)
    {
        $alertType = null;

        if ($magnitude >= 5.0) {
            // Danger level - Major alert
            $alertType = 'danger';
            $this->sendEmergencyAlert($device, $magnitude, $earthquakeEvent);
        } elseif ($magnitude >= 3.0) {
            // Warning level - Normal alert
            $alertType = 'warning';
            $this->sendWarningAlert($device, $magnitude, $earthquakeEvent);
        }

        return $alertType;
    }

    /**
     * Send emergency alert for magnitude >= 5.0
     */
    private function sendEmergencyAlert($device, $magnitude, $earthquakeEvent)
    {
        // In a real implementation, this would send:
        // 1. Email alerts to all admins
        // 2. SMS notifications
        // 3. Push notifications
        // 4. Sound alarms on dashboard

        Log::emergency('EMERGENCY EARTHQUAKE ALERT', [
            'device' => $device->nama_device,
            'location' => $device->lokasi,
            'magnitude' => $magnitude,
            'event_id' => $earthquakeEvent->id,
            'timestamp' => now()
        ]);

        // You would implement actual notification sending here
        // Example: Mail::to($admins)->send(new EarthquakeAlert($earthquakeEvent));
    }

    /**
     * Send warning alert for magnitude >= 3.0
     */
    private function sendWarningAlert($device, $magnitude, $earthquakeEvent)
    {
        // Send warning notifications

        Log::warning('EARTHQUAKE WARNING ALERT', [
            'device' => $device->nama_device,
            'location' => $device->lokasi,
            'magnitude' => $magnitude,
            'event_id' => $earthquakeEvent->id,
            'timestamp' => now()
        ]);
    }

    /**
     * Get magnitude level description
     */
    private function getMagnitudeLevel($magnitude)
    {
        if ($magnitude >= 7.0) return 'major';
        if ($magnitude >= 5.0) return 'strong';
        if ($magnitude >= 3.0) return 'moderate';
        if ($magnitude >= 2.0) return 'light';
        return 'minor';
    }

    /**
     * Get device information
     */
    public function getDeviceInfo($uuid)
    {
        $device = Device::where('uuid', $uuid)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        // Get recent earthquake events
        $recentEvents = EarthquakeEvent::where('device_id', $device->id)
            ->where('magnitude', '>=', 3.0)
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'device' => [
                'id' => $device->id,
                'name' => $device->nama_device,
                'location' => $device->lokasi,
                'status' => $device->status,
                'last_seen' => $device->last_seen,
                'created_at' => $device->created_at,
                'total_logs' => $device->logs()->count(),
                'total_events' => $device->earthquakeEvents()->count(),
                'warning_events' => $device->earthquakeEvents()->where('status', 'warning')->count(),
                'danger_events' => $device->earthquakeEvents()->where('status', 'danger')->count(),
                'recent_significant_events' => $recentEvents->map(function($event) {
                    return [
                        'magnitude' => $event->magnitude,
                        'status' => $event->status,
                        'occurred_at' => $event->occurred_at
                    ];
                }),
                'api_endpoint' => url("/api/v1/devices/{$device->uuid}/data")
            ]
        ]);
    }

    /**
     * Get recent significant logs (magnitude >= 3.0)
     */
    public function getSignificantLogs($uuid, Request $request)
    {
        $device = Device::where('uuid', $uuid)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        $limit = min($request->input('limit', 50), 100);

        $logs = DeviceLog::where('device_id', $device->id)
            ->where('magnitude', '>=', 3.0)
            ->orderBy('logged_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'device' => [
                'id' => $device->id,
                'name' => $device->nama_device
            ],
            'significant_logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'status' => $log->status,
                    'magnitude' => $log->magnitude,
                    'magnitude_level' => $this->getMagnitudeLevel($log->magnitude),
                    'logged_at' => $log->logged_at->toISOString(),
                    'time_ago' => $log->logged_at->diffForHumans()
                ];
            }),
            'count' => $logs->count(),
            'threshold' => 3.0
        ]);
    }

    /**
     * Test earthquake detection
     */
    public function testDetection(Request $request, $uuid)
    {
        $device = Device::where('uuid', $uuid)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'test_magnitude' => ['required', 'numeric', 'min:0', 'max:10']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $testMagnitude = $request->input('test_magnitude');

        // Convert magnitude to vibration for testing
        $vibration = $this->magnitudeToVibration($testMagnitude);

        // Create test log
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'status' => 'online',
            'magnitude' => $testMagnitude,
            'logged_at' => now(),
        ]);

        $response = [
            'success' => true,
            'message' => 'Test detection completed.',
            'test_data' => [
                'requested_magnitude' => $testMagnitude,
                'simulated_vibration' => $vibration,
                'log_created' => true,
                'log_id' => $log->id
            ]
        ];

        // Check if earthquake event would be created
        if ($testMagnitude >= 3.0) {
            $thresholds = Threshold::orderBy('min_value', 'desc')->get();
            $status = 'normal';

            foreach ($thresholds as $threshold) {
                if ($testMagnitude >= $threshold->min_value) {
                    $status = $threshold->description;
                    break;
                }
            }

            $response['earthquake_detection'] = [
                'detected' => true,
                'magnitude' => $testMagnitude,
                'status' => $status,
                'alert_level' => $status === 'danger' ? 'high' : ($status === 'warning' ? 'medium' : 'low'),
                'message' => 'Earthquake would be detected and alerts would be triggered'
            ];
        } else {
            $response['earthquake_detection'] = [
                'detected' => false,
                'message' => 'Magnitude below detection threshold (3.0)'
            ];
        }

        return response()->json($response);
    }

    /**
     * Convert magnitude to vibration (reverse conversion)
     */
    private function magnitudeToVibration($magnitude)
    {
        if ($magnitude <= 0) return 0;
        if ($magnitude <= 2) return $magnitude / 2 * 200;
        if ($magnitude <= 5) return 200 + ($magnitude - 2) / 3 * 300;
        return 500 + ($magnitude - 5) / 5 * 523;
    }

    /**
     * Get detection statistics
     */
    public function detectionStatistics($uuid)
    {
        $device = Device::where('uuid', $uuid)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        $today = today();
        $yesterday = today()->subDay();

        $stats = [
            'total_detections' => EarthquakeEvent::where('device_id', $device->id)->count(),
            'today_detections' => EarthquakeEvent::where('device_id', $device->id)
                ->whereDate('occurred_at', $today)
                ->count(),
            'yesterday_detections' => EarthquakeEvent::where('device_id', $device->id)
                ->whereDate('occurred_at', $yesterday)
                ->count(),
            'warning_detections' => EarthquakeEvent::where('device_id', $device->id)
                ->where('status', 'warning')
                ->count(),
            'danger_detections' => EarthquakeEvent::where('device_id', $device->id)
                ->where('status', 'danger')
                ->count(),
            'max_magnitude' => EarthquakeEvent::where('device_id', $device->id)->max('magnitude') ?? 0,
            'avg_magnitude' => EarthquakeEvent::where('device_id', $device->id)->avg('magnitude') ?? 0,
            'detection_threshold' => 3.0,
            'last_detection' => EarthquakeEvent::where('device_id', $device->id)
                ->latest('occurred_at')
                ->first()
        ];

        return response()->json([
            'success' => true,
            'device' => [
                'id' => $device->id,
                'name' => $device->nama_device
            ],
            'detection_statistics' => $stats
        ]);
    }
}
