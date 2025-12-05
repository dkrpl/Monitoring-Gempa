<?php

namespace App\Http\Controllers;

use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThresholdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $thresholds = Threshold::orderBy('min_value')->get();

        return view('thresholds.index', compact('thresholds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('thresholds.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_value' => [
                'required',
                'numeric',
                'min:0',
                'max:10',
                Rule::unique('thresholds', 'min_value')
            ],
            'description' => ['required', 'string', 'max:255', 'in:warning,danger,critical'],
            'color' => ['required', 'string', 'max:50'],
            'notification_enabled' => ['boolean'],
            'notification_message' => ['nullable', 'string', 'max:500'],
            'auto_alert' => ['boolean'],
        ]);

        $thresholdData = [
            'min_value' => $request->min_value,
            'description' => $request->description,
            'color' => $request->color,
            'notification_enabled' => $request->boolean('notification_enabled'),
            'notification_message' => $request->notification_message,
            'auto_alert' => $request->boolean('auto_alert'),
        ];

        Threshold::create($thresholdData);

        return redirect()->route('thresholds.index')
            ->with('success', 'Threshold created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Threshold $threshold)
    {
        // Get statistics for this threshold
        $stats = $this->getThresholdStatistics($threshold);

        return view('thresholds.show', compact('threshold', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Threshold $threshold)
    {
        return view('thresholds.edit', compact('threshold'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Threshold $threshold)
    {
        $request->validate([
            'min_value' => [
                'required',
                'numeric',
                'min:0',
                'max:10',
                Rule::unique('thresholds', 'min_value')->ignore($threshold->id)
            ],
            'description' => ['required', 'string', 'max:255', 'in:warning,danger,critical'],
            'color' => ['required', 'string', 'max:50'],
            'notification_enabled' => ['boolean'],
            'notification_message' => ['nullable', 'string', 'max:500'],
            'auto_alert' => ['boolean'],
        ]);

        $thresholdData = [
            'min_value' => $request->min_value,
            'description' => $request->description,
            'color' => $request->color,
            'notification_enabled' => $request->boolean('notification_enabled'),
            'notification_message' => $request->notification_message,
            'auto_alert' => $request->boolean('auto_alert'),
        ];

        $threshold->update($thresholdData);

        return redirect()->route('thresholds.index')
            ->with('success', 'Threshold updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Threshold $threshold)
    {
        // Prevent deletion if this is the only threshold
        $count = Threshold::count();
        if ($count <= 1) {
            return redirect()->route('thresholds.index')
                ->with('error', 'Cannot delete the last threshold. At least one threshold must exist.');
        }

        $threshold->delete();

        return redirect()->route('thresholds.index')
            ->with('success', 'Threshold deleted successfully.');
    }

    /**
     * Update threshold order (priority)
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'thresholds' => ['required', 'array'],
            'thresholds.*.id' => ['required', 'exists:thresholds,id'],
            'thresholds.*.order' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->thresholds as $item) {
            Threshold::where('id', $item['id'])->update(['priority' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Threshold order updated successfully.'
        ]);
    }

    /**
     * Reset to default thresholds
     */
    public function resetToDefault()
    {
        // Delete all existing thresholds
        Threshold::truncate();

        // Create default thresholds
        $defaultThresholds = [
            [
                'min_value' => 3.0,
                'description' => 'warning',
                'color' => '#ffc107',
                'notification_enabled' => true,
                'notification_message' => 'Warning: Earthquake magnitude {magnitude} detected at {location}',
                'auto_alert' => false,
                'priority' => 1
            ],
            [
                'min_value' => 5.0,
                'description' => 'danger',
                'color' => '#e74a3b',
                'notification_enabled' => true,
                'notification_message' => 'DANGER: Strong earthquake magnitude {magnitude} detected! Take immediate safety measures.',
                'auto_alert' => true,
                'priority' => 2
            ],
            [
                'min_value' => 7.0,
                'description' => 'critical',
                'color' => '#dc3545',
                'notification_enabled' => true,
                'notification_message' => 'CRITICAL: Major earthquake magnitude {magnitude} detected! EVACUATE IMMEDIATELY!',
                'auto_alert' => true,
                'priority' => 3
            ]
        ];

        foreach ($defaultThresholds as $threshold) {
            Threshold::create($threshold);
        }

        return redirect()->route('thresholds.index')
            ->with('success', 'Thresholds reset to default values successfully.');
    }

    /**
     * Test threshold notification
     */
    public function testNotification(Threshold $threshold)
    {
        // Simulate a test notification
        $testData = [
            'magnitude' => $threshold->min_value + 0.5,
            'location' => 'Test Location',
            'device' => 'Test Device',
            'time' => now()->format('Y-m-d H:i:s')
        ];

        // Replace placeholders in notification message
        $message = $threshold->notification_message;
        foreach ($testData as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test notification generated successfully.',
            'notification' => [
                'title' => ucfirst($threshold->description) . ' Alert Test',
                'message' => $message,
                'color' => $threshold->color,
                'enabled' => $threshold->notification_enabled,
                'auto_alert' => $threshold->auto_alert
            ]
        ]);
    }

    /**
     * Get threshold statistics
     */
    private function getThresholdStatistics(Threshold $threshold)
    {
        // Get earthquake events that match this threshold
        $events = \App\Models\EarthquakeEvent::where('magnitude', '>=', $threshold->min_value)
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        // Get next lower threshold
        $nextLower = Threshold::where('min_value', '<', $threshold->min_value)
            ->orderBy('min_value', 'desc')
            ->first();

        // Get next higher threshold
        $nextHigher = Threshold::where('min_value', '>', $threshold->min_value)
            ->orderBy('min_value', 'asc')
            ->first();

        // Calculate range
        $range = [
            'from' => $nextLower ? $nextLower->min_value : 0,
            'to' => $nextHigher ? $nextHigher->min_value : 10
        ];

        // Count events in this threshold range
        $eventCount = \App\Models\EarthquakeEvent::where('magnitude', '>=', $range['from'])
            ->where('magnitude', '<', $range['to'])
            ->count();

        // Get recent events count (last 30 days)
        $recentCount = \App\Models\EarthquakeEvent::where('magnitude', '>=', $range['from'])
            ->where('magnitude', '<', $range['to'])
            ->where('occurred_at', '>=', now()->subDays(30))
            ->count();

        return [
            'events' => $events,
            'range' => $range,
            'event_count' => $eventCount,
            'recent_count' => $recentCount,
            'next_lower' => $nextLower,
            'next_higher' => $nextHigher
        ];
    }

    /**
     * Get threshold effectiveness report
     */
    public function effectivenessReport()
    {
        $thresholds = Threshold::orderBy('min_value')->get();

        $report = [];
        foreach ($thresholds as $threshold) {
            $stats = $this->getThresholdStatistics($threshold);

            // Calculate effectiveness metrics
            $totalEvents = \App\Models\EarthquakeEvent::count();
            $thresholdEvents = $stats['event_count'];
            $effectiveness = $totalEvents > 0 ? ($thresholdEvents / $totalEvents * 100) : 0;

            $report[] = [
                'threshold' => $threshold,
                'statistics' => $stats,
                'effectiveness' => round($effectiveness, 2),
                'average_magnitude' => \App\Models\EarthquakeEvent::where('magnitude', '>=', $threshold->min_value)->avg('magnitude') ?? 0,
                'max_magnitude' => \App\Models\EarthquakeEvent::where('magnitude', '>=', $threshold->min_value)->max('magnitude') ?? 0
            ];
        }

        return response()->json([
            'success' => true,
            'report' => $report,
            'generated_at' => now()->toDateTimeString()
        ]);
    }
}
