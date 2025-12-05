<?php

namespace App\Http\Middleware;

use App\Traits\LogsActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    use LogsActivity;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log specific actions
        if (Auth::check()) {
            $this->logRouteActivity($request);
        }

        return $next($request);
    }

    /**
     * Log route-based activity
     */
    private function logRouteActivity(Request $request)
    {
        $routeName = $request->route()->getName();
        $user = Auth::user();

        // Map routes to actions
        $routeActions = [
            'dashboard' => ['action' => 'view_dashboard', 'description' => 'Viewed dashboard'],
            'profile.show' => ['action' => 'view_profile', 'description' => 'Viewed profile'],
            'profile.update' => ['action' => 'update_profile', 'description' => 'Updated profile'],
            'profile.password' => ['action' => 'change_password', 'description' => 'Changed password'],
            'settings.index' => ['action' => 'view_settings', 'description' => 'Viewed system settings'],
            'settings.update' => ['action' => 'update_settings', 'description' => 'Updated system settings'],
            'users.index' => ['action' => 'view_users', 'description' => 'Viewed user list'],
            'users.create' => ['action' => 'create_user_form', 'description' => 'Opened create user form'],
            'users.store' => ['action' => 'create_user', 'description' => 'Created new user'],
            'users.edit' => ['action' => 'edit_user_form', 'description' => 'Opened edit user form'],
            'users.update' => ['action' => 'update_user', 'description' => 'Updated user'],
            'users.destroy' => ['action' => 'delete_user', 'description' => 'Deleted user'],
            'devices.index' => ['action' => 'view_devices', 'description' => 'Viewed device list'],
            'devices.create' => ['action' => 'create_device_form', 'description' => 'Opened create device form'],
            'devices.store' => ['action' => 'create_device', 'description' => 'Created new device'],
            'devices.edit' => ['action' => 'edit_device_form', 'description' => 'Opened edit device form'],
            'devices.update' => ['action' => 'update_device', 'description' => 'Updated device'],
            'devices.destroy' => ['action' => 'delete_device', 'description' => 'Deleted device'],
            'earthquake-events.index' => ['action' => 'view_earthquake_events', 'description' => 'Viewed earthquake events'],
            'earthquake-events.create' => ['action' => 'create_earthquake_event_form', 'description' => 'Opened create earthquake event form'],
            'earthquake-events.store' => ['action' => 'create_earthquake_event', 'description' => 'Created earthquake event'],
            'earthquake-events.edit' => ['action' => 'edit_earthquake_event_form', 'description' => 'Opened edit earthquake event form'],
            'earthquake-events.update' => ['action' => 'update_earthquake_event', 'description' => 'Updated earthquake event'],
            'earthquake-events.destroy' => ['action' => 'delete_earthquake_event', 'description' => 'Deleted earthquake event'],
            'activity-logs.index' => ['action' => 'view_activity_logs', 'description' => 'Viewed activity logs'],
        ];

        if (isset($routeActions[$routeName])) {
            $action = $routeActions[$routeName];
            $this->logActivity($action['action'], $action['description'], $user, [
                'route' => $routeName,
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
        }
    }
}
