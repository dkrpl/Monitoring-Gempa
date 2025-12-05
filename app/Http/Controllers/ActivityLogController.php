<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by search term
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20);
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct('action')->pluck('action');

        // Statistics
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::today()->count(),
            'users' => ActivityLog::distinct('user_id')->count(),
            'actions' => ActivityLog::distinct('action')->count(),
        ];

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'stats', 'request'));
    }

    /**
     * Show activity log details
     */
    public function show(ActivityLog $activityLog)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $activityLog->load('user', 'model');

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Clear old activity logs
     */
    public function clearOldLogs()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $days = 30; // Default retention period
        $cutoffDate = now()->subDays($days);

        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        // Log this activity
        ActivityLog::logActivity(
            'logs_cleared',
            'Old activity logs cleared',
            null,
            ['days' => $days, 'deleted_count' => $deleted]
        );

        return redirect()->route('activity-logs.index')
            ->with('success', "Cleared {$deleted} old activity logs.");
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $query = ActivityLog::with('user');

        // Apply filters if present
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $csvData = "ID,Timestamp,User,IP Address,Action,Description,Details\n";

        foreach ($logs as $log) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->email : 'System',
                $log->ip_address,
                $log->action,
                str_replace(',', ';', $log->description),
                $log->details ? str_replace(',', ';', json_encode($log->details)) : ''
            );
        }

        $filename = 'activity-logs-' . date('Y-m-d-H-i-s') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get activity statistics
     */
    public function statistics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::today()->count(),
            'unique_users' => ActivityLog::distinct('user_id')->count(),
            'unique_actions' => ActivityLog::distinct('action')->count(),
            'top_users' => $this->getTopUsers(),
            'recent_actions' => $this->getRecentActions(),
            'activity_by_hour' => $this->getActivityByHour(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    private function getTopUsers($limit = 5)
    {
        return ActivityLog::selectRaw('user_id, count(*) as activity_count')
            ->whereNotNull('user_id')
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->user ? $item->user->name : 'Unknown',
                    'count' => $item->activity_count
                ];
            });
    }

    private function getRecentActions($limit = 10)
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->name : 'System',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans()
                ];
            });
    }

    private function getActivityByHour()
    {
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $count = ActivityLog::whereRaw('HOUR(created_at) = ?', [$i])
                ->whereDate('created_at', today())
                ->count();
            $hours[$hour] = $count;
        }

        return $hours;
    }
}
