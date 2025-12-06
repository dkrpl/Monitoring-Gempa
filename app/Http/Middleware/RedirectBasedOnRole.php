<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Jika user sudah login dan mengakses homepage/landing
            if ($request->is('/') || $request->routeIs('landing')) {
                if ($user->role === 'admin') {
                    return redirect()->route('dashboard');
                } else {
                    return redirect()->route('user.dashboard');
                }
            }

            // Redirect ke dashboard yang sesuai berdasarkan role
            if ($request->routeIs('dashboard')) {
                if ($user->role === 'user') {
                    return redirect()->route('user.dashboard');
                }
            }

            // User biasa tidak bisa akses admin routes
            if ($user->role === 'user') {
                $adminRoutes = [
                    'users.*',
                    'devices.*',
                    'earthquake-events.*',
                    'dashboard' // sudah dihandle di atas
                ];

                foreach ($adminRoutes as $route) {
                    if ($request->routeIs($route)) {
                        abort(403, 'Unauthorized access. Admin privileges required.');
                    }
                }
            }
        }

        return $next($request);
    }
}
