<?php

namespace App\Http\Controllers;

use App\Models\EarthquakeEvent;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserEventController extends Controller
{
    public function index()
    {
        $events = EarthquakeEvent::with('device')
            ->where('status', '!=', 'normal')
            ->orderBy('occurred_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => EarthquakeEvent::where('status', '!=', 'normal')->count(),
            'today' => EarthquakeEvent::where('status', '!=', 'normal')
                ->whereDate('occurred_at', today())
                ->count(),
            'warning' => EarthquakeEvent::where('status', 'warning')->count(),
            'danger' => EarthquakeEvent::where('status', 'danger')->count(),
        ];

        return view('users.events.index', compact('events', 'stats'));
    }

    public function show($id)
    {
        $event = EarthquakeEvent::with('device')
            ->where('status', '!=', 'normal')
            ->findOrFail($id);

        $safetyInstructions = $this->getSafetyInstructions($event->magnitude);

        // Get similar events
        $similarEvents = EarthquakeEvent::with('device')
            ->where('device_id', $event->device_id)
            ->where('id', '!=', $event->id)
            ->where('status', '!=', 'normal')
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get();

        return view('users.events.show', compact('event', 'safetyInstructions', 'similarEvents'));
    }

    public function map(Request $request)
{
    Log::info('Map method accessed', [
        'url' => $request->fullUrl(),
        'route' => $request->route()->getName(),
        'parameters' => $request->route()->parameters()
    ]);

    $events = EarthquakeEvent::with('device')
        ->where('status', '!=', 'normal')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->orderBy('occurred_at', 'desc')
        ->limit(50)
        ->get();

    Log::info('Events found for map', ['count' => $events->count()]);

    return view('users.events.map', compact('events'));
}

    public function alerts()
    {
        $alerts = EarthquakeEvent::with('device')
            ->where('status', 'danger')
            ->where('occurred_at', '>=', now()->subHours(24))
            ->orderBy('occurred_at', 'desc')
            ->paginate(10);

        return view('users.events.alerts', compact('alerts'));
    }

    public function statistics()
    {
        $stats = [
            'total_events' => EarthquakeEvent::where('status', '!=', 'normal')->count(),
            'today_events' => EarthquakeEvent::where('status', '!=', 'normal')
                ->whereDate('occurred_at', today())
                ->count(),
            'max_magnitude' => EarthquakeEvent::max('magnitude') ?? 0,
            'most_active_location' => $this->getMostActiveLocation(),
            'recent_activity' => EarthquakeEvent::with('device')
                ->where('status', '!=', 'normal')
                ->orderBy('occurred_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        // Chart data for statistics
        $chartData = $this->getStatisticsChartData();

        return view('users.events.statistics', compact('stats', 'chartData'));
    }

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
                    'device_name' => $event->device->nama_device
                ];
            });

        return response()->json([
            'events' => $events
        ]);
    }

    private function getSafetyInstructions($magnitude)
    {
        if ($magnitude >= 5.0) {
            return [
                'title' => 'MAJOR EARTHQUAKE ALERT',
                'instructions' => [
                    'DROP, COVER, AND HOLD ON immediately',
                    'If indoors, stay there. Avoid windows',
                    'If outdoors, move to open area',
                    'Be prepared for aftershocks',
                    'Follow official evacuation orders if issued'
                ],
                'color' => 'danger',
                'icon' => 'fire'
            ];
        } elseif ($magnitude >= 3.0) {
            return [
                'title' => 'EARTHQUAKE WARNING',
                'instructions' => [
                    'Take cover under sturdy furniture',
                    'Stay away from glass and hanging objects',
                    'Do not use elevators',
                    'Be alert for falling objects',
                    'Monitor for further updates'
                ],
                'color' => 'warning',
                'icon' => 'exclamation-triangle'
            ];
        }

        return [
            'title' => 'Minor Seismic Activity',
            'instructions' => [
                'No immediate action required',
                'Stay informed about updates',
                'Review your earthquake preparedness plan'
            ],
            'color' => 'info',
            'icon' => 'info-circle'
        ];
    }

    private function getMostActiveLocation()
    {
        $device = Device::withCount(['earthquakeEvents' => function($query) {
            $query->where('status', '!=', 'normal');
        }])
        ->orderBy('earthquake_events_count', 'desc')
        ->first();

        return $device ? [
            'name' => $device->nama_device,
            'location' => $device->lokasi,
            'count' => $device->earthquake_events_count
        ] : null;
    }

    private function getStatisticsChartData()
    {
        $dates = [];
        $warningData = [];
        $dangerData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('M d');

            $warningCount = EarthquakeEvent::where('status', 'warning')
                ->whereDate('occurred_at', $date)
                ->count();

            $dangerCount = EarthquakeEvent::where('status', 'danger')
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
}
