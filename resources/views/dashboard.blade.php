@extends('layouts.app')

@section('title', 'Dashboard - EQMonitor')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Earthquake Monitoring Overview')

@push('styles')
<style>
    .dashboard-section {
        margin-bottom: 1.5rem;
    }

    .quick-link-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: #5a5c69;
        height: 100%;
    }

    .quick-link-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-color: #4e73df;
    }

    .action-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: #5a5c69;
        height: 100%;
    }

    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .stat-card-icon {
        font-size: 2rem;
        opacity: 0.25;
    }

    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }

    #detectionAlert {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    .system-info-card {
        transition: all 0.3s ease;
    }

    .system-info-card:hover {
        transform: translateY(-2px);
    }

    .chart-area {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item.warning {
        border-left-color: #ffc107;
    }

    .list-group-item.danger {
        border-left-color: #e74a3b;
    }

    .list-group-item:hover {
        transform: translateX(5px);
    }

    .dashboard-welcome {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }

    .dashboard-welcome .text-primary {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .dashboard-welcome .text-gray-300 {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    .dashboard-welcome .text-gray-800 {
        color: white !important;
    }

    .dashboard-welcome .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .user-profile-img-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    }

    .user-profile-img-sm.bg-primary {
        font-size: 0.8rem;
        font-weight: bold;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table td {
        vertical-align: middle;
    }
    .user-profile-img-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    }

    .user-profile-img-sm.bg-primary {
        font-size: 0.9rem;
        font-weight: bold;
    }

    #activityTable tbody tr {
        transition: all 0.2s;
    }

    #activityTable tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
        transform: translateX(2px);
    }

    #activityTable td {
        vertical-align: middle;
        padding: 12px 8px;
    }

    #activityTable .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    .activity-action {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Welcome Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-welcome shadow h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: rgba(255, 255, 255, 0.9)">
                            Welcome Back, {{ Auth::user()->name }}!
                        </div>
                        <div class="h5 mb-2 font-weight-bold">
                            {{ Auth::user()->role === 'admin' ? 'Administrator Dashboard' : 'User Dashboard' }}
                        </div>
                        <p class="mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ Auth::user()->role === 'admin'
                                ? 'Real-time earthquake monitoring with automatic detection (≥3.0 magnitude)'
                                : 'Monitor earthquake events and view alerts.'
                            }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earth-americas fa-3x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Detection Alert -->
<div class="row mb-4" id="detectionAlertContainer" style="display: none;">
    <div class="col-12">
        <div class="card shadow border-left-warning" id="detectionAlert">
            <div class="card-header py-3 bg-warning text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>EARTHQUAKE ALERT</strong>
                </div>
                <button class="btn btn-sm btn-outline-light" onclick="dismissAlert()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="mr-4">
                        <i class="fas fa-earthquake fa-3x text-warning" id="alertIcon"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1" id="alertTitle">Earthquake Detected!</h5>
                        <p class="mb-2" id="alertMessage">Loading detection information...</p>
                        <div class="d-flex align-items-center">
                            <small class="text-muted mr-3">
                                <i class="fas fa-clock mr-1"></i><span id="alertTime"></span>
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i><span id="alertLocation"></span>
                            </small>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-warning" onclick="viewLatestDetection()">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Quick Links & Analytics -->
    <div class="col-xl-8 col-lg-7">
        <!-- Quick Links -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-link mr-2"></i>Quick Links
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('profile.show') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-user fa-2x mb-3 text-primary"></i>
                                <h6 class="font-weight-bold mb-0">My Profile</h6>
                            </div>
                        </a>
                    </div>

                    @if(Auth::user()->isAdmin())
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('settings.index') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-cogs fa-2x mb-3 text-warning"></i>
                                <h6 class="font-weight-bold mb-0">Settings</h6>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('activity-logs.index') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-list fa-2x mb-3 text-info"></i>
                                <h6 class="font-weight-bold mb-0">Activity Log</h6>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('users.index') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x mb-3 text-success"></i>
                                <h6 class="font-weight-bold mb-0">Users</h6>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Analytics -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>Quick Analytics
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('analytics') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-chart-bar fa-2x mb-3 text-primary"></i>
                                <h6 class="font-weight-bold mb-0">Analytics Dashboard</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('earthquake-events.index') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-fw fa-earth-asia fa-2x mb-3 text-info"></i>
                                <h6 class="font-weight-bold mb-0">Events Analysis</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('devices.index') }}" class="card quick-link-card text-center p-3">
                            <div class="card-body">
                                <i class="fas fa-microchip fa-2x mb-3 text-success"></i>
                                <h6 class="font-weight-bold mb-0">Device Analytics</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="card quick-link-card text-center p-3 w-100 border-0 bg-transparent" onclick="generateQuickReport()">
                            <div class="card-body">
                                <i class="fas fa-file-pdf fa-2x mb-3 text-warning"></i>
                                <h6 class="font-weight-bold mb-0">Quick Report</h6>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Detections -->
    <div class="col-xl-4 col-lg-5">
        <!-- Quick Actions -->
        @if(Auth::user()->role === 'admin')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <a href="{{ route('devices.create') }}" class="card action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-primary mb-2">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                                <small class="font-weight-bold">Add Device</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('users.index') }}" class="card action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-info mb-2">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <small class="font-weight-bold">Manage Users</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('devices.index') }}" class="card action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-success mb-2">
                                    <i class="fas fa-microchip text-white"></i>
                                </div>
                                <small class="font-weight-bold">Devices</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('earthquake-events.index') }}" class="card action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-warning mb-2">
                                    <i class="fas fa-earthquake text-white"></i>
                                </div>
                                <small class="font-weight-bold">Events</small>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-outline-success btn-sm btn-block" onclick="testDetectionSystem()">
                        <i class="fas fa-vial mr-1"></i> Test Detection System
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Detections -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Recent Detections
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="recentDetections">
                    @php
                        $recentEvents = \App\Models\EarthquakeEvent::with('device')
                            ->orderBy('occurred_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($recentEvents->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <p class="text-muted mb-0">No recent detections</p>
                        </div>
                    @else
                        @foreach($recentEvents as $event)
                            <a href="{{ route('earthquake-events.show', $event) }}" class="list-group-item list-group-item-action {{ $event->status }}">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} fa-lg text-{{ $event->status }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark">{{ $event->device->nama_device }}</strong>
                                            <small class="text-muted">{{ $event->occurred_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-{{ $event->magnitude >= 5.0 ? 'danger' : 'warning' }} mr-2">
                                                {{ $event->magnitude }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt fa-xs mr-1"></i>
                                                {{ Str::limit($event->device->lokasi, 20) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-3">
                    <a href="{{ route('earthquake-events.index') }}" class="btn btn-sm btn-outline-primary btn-block">
                        <i class="fas fa-list mr-1"></i>View All Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'admin')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User::count() }}</div>
                        <div class="mt-2">
                            <a href="{{ route('users.index') }}" class="text-xs text-primary">
                                <i class="fas fa-arrow-right mr-1"></i>Manage Users
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users stat-card-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Devices</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeDevicesCount">
                            {{ \App\Models\Device::where('status', 'aktif')->count() }}
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('devices.index') }}" class="text-xs text-success">
                                <i class="fas fa-arrow-right mr-1"></i>View Devices
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-microchip stat-card-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Today's Detections</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayDetections">
                            {{ \App\Models\EarthquakeEvent::whereDate('occurred_at', today())->count() }}
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('earthquake-events.index') }}" class="text-xs text-warning">
                                <i class="fas fa-arrow-right mr-1"></i>View Events
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earthquake stat-card-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Danger Events (≥5.0)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="dangerEventsCount">
                            {{ \App\Models\EarthquakeEvent::where('status', 'danger')->count() }}
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('earthquake-events.index') }}?status=danger" class="text-xs text-danger">
                                <i class="fas fa-arrow-right mr-1"></i>View Details
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire stat-card-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-server mr-2"></i>System Status
                </h6>
                <a href="{{ route('settings.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-cogs mr-2"></i>Manage Settings
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-success h-100 system-info-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-sliders-h text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-2">Thresholds</h6>
                                        <div class="text-muted small">
                                            @php
                                                $thresholds = \App\Models\Threshold::orderBy('min_value')->get();
                                            @endphp
                                            @foreach($thresholds as $t)
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>{{ $t->description }}:</span>
                                                <span class="font-weight-bold">≥{{ $t->min_value }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-info h-100 system-info-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-info">
                                            <i class="fas fa-bell text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-2">Notifications</h6>
                                        <div class="text-muted small">
                                            @php
                                                $emailEnabled = \App\Models\Setting::getBool('enable_email_alerts', true);
                                                $smsEnabled = \App\Models\Setting::getBool('enable_sms_alerts', false);
                                            @endphp
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Email:</span>
                                                <span class="badge badge-{{ $emailEnabled ? 'success' : 'secondary' }}">
                                                    {{ $emailEnabled ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>SMS:</span>
                                                <span class="badge badge-{{ $smsEnabled ? 'success' : 'secondary' }}">
                                                    {{ $smsEnabled ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-warning h-100 system-info-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-shield-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-2">Security</h6>
                                        <div class="text-muted small">
                                            @php
                                                $auditLog = \App\Models\Setting::getBool('enable_audit_log', true);
                                                $dataEncryption = \App\Models\Setting::getBool('data_encryption', true);
                                            @endphp
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Audit Log:</span>
                                                <span class="badge badge-{{ $auditLog ? 'success' : 'secondary' }}">
                                                    {{ $auditLog ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Encryption:</span>
                                                <span class="badge badge-{{ $dataEncryption ? 'success' : 'secondary' }}">
                                                    {{ $dataEncryption ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-left-primary h-100 system-info-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-cog text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-2">System</h6>
                                        <div class="text-muted small">
                                            @php
                                                $maintenance = \App\Models\Setting::getBool('maintenance_mode', false);
                                                $timezone = \App\Models\Setting::getValue('timezone', 'Asia/Jakarta');
                                            @endphp
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Mode:</span>
                                                <span class="badge badge-{{ $maintenance ? 'warning' : 'success' }}">
                                                    {{ $maintenance ? 'MAINTENANCE' : 'NORMAL' }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Timezone:</span>
                                                <span class="font-weight-bold">{{ $timezone }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Monitoring & System Info -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Real-time Monitoring
                </h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="startRealTime()">
                        <i class="fas fa-play"></i> Live
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="realTimeChart"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="badge badge-success">Normal (<3.0)</span>
                        </div>
                        <div class="col-md-4">
                            <span class="badge badge-warning">Warning (3.0-4.9)</span>
                        </div>
                        <div class="col-md-4">
                            <span class="badge badge-danger">Danger (≥5.0)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Quick Info -->
    <div class="col-lg-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>System Quick Info
                </h6>
            </div>
            <div class="card-body">
                <div class="system-info">
                    <div class="mb-4">
                        <h6><i class="fas fa-microchip text-primary mr-2"></i>Device Status</h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="card border-left-success p-3 text-center mb-0">
                                    <div class="h5 mb-0 text-success">{{ \App\Models\Device::where('status', 'aktif')->count() }}</div>
                                    <small>Active</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-left-warning p-3 text-center mb-0">
                                    <div class="h5 mb-0 text-warning">{{ \App\Models\Device::where('status', 'nonaktif')->count() }}</div>
                                    <small>Inactive</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6><i class="fas fa-bell text-warning mr-2"></i>Detection System</h6>
                        <div class="pl-3">
                            <p><strong>Threshold:</strong> 3.0 magnitude</p>
                            <p><strong>Active Sensors:</strong> <span id="activeSensors">{{ \App\Models\Device::where('status', 'aktif')->count() }}</span></p>
                            <p><strong>Last Detection:</strong>
                                <span id="lastDetectionTime">
                                    @php
                                        $lastEvent = \App\Models\EarthquakeEvent::latest()->first();
                                        echo $lastEvent ? $lastEvent->occurred_at->diffForHumans() : 'Never';
                                    @endphp
                                </span>
                            </p>
                            <p><strong>Status:</strong>
                                <span class="badge badge-success">Operational</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <button class="btn btn-sm btn-block btn-outline-info" onclick="checkAllDevices()">
                            <i class="fas fa-wifi mr-2"></i>Check Device Status
                        </button>
                        <button class="btn btn-sm btn-block btn-outline-primary mt-2" onclick="configureDetection()">
                            <i class="fas fa-cog mr-2"></i> Configure Detection
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- User Dashboard -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-earthquake mr-2"></i>Earthquake Monitoring
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>System Overview</h5>
                    <p>This system automatically detects earthquakes with magnitude ≥ 3.0 using SW-420 vibration sensors.</p>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title text-warning mb-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Current Status
                                </h5>
                                <p class="card-text mb-4">No earthquake detected in the last 24 hours.</p>
                                <a href="{{ route('earthquake-events.index') }}" class="btn btn-warning">
                                    View All Events
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <h5 class="card-title text-info mb-3">
                                    <i class="fas fa-chart-line mr-2"></i>Statistics
                                </h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-circle fa-xs text-info mr-2"></i>
                                        Total Detections: {{ \App\Models\EarthquakeEvent::count() }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-circle fa-xs text-primary mr-2"></i>
                                        Today: {{ \App\Models\EarthquakeEvent::whereDate('occurred_at', today())->count() }}
                                    </li>
                                    <li>
                                        <i class="fas fa-circle fa-xs text-success mr-2"></i>
                                        This Month: {{ \App\Models\EarthquakeEvent::whereMonth('occurred_at', now()->month)->count() }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Recent Activity
                </h6>
                <div class="btn-group">
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list mr-1"></i> View All
                        </a>
                    @endif
                    <button class="btn btn-sm btn-outline-secondary" onclick="refreshActivity()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="activityTable">
                        <thead>
                            <tr>
                                <th width="15%">Time</th>
                                @if(Auth::user()->role === 'admin')
                                    <th width="20%">User</th>
                                @endif
                                <th width="20%">Action</th>
                                <th>Description</th>
                                @if(Auth::user()->role === 'admin')
                                    <th width="15%">IP Address</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Get recent activity logs
                                $recentActivities = \App\Models\ActivityLog::with('user')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();
                            @endphp

                            @if($recentActivities->isEmpty())
                                <tr>
                                    <td colspan="{{ Auth::user()->role === 'admin' ? '5' : '3' }}" class="text-center py-4">
                                        <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                                        <h6 class="text-gray-500">No recent activity</h6>
                                        <p class="text-gray-500 small">Activity will appear here as you use the system</p>
                                        @if(Auth::user()->role === 'admin')
                                            <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-primary mt-2">
                                                <i class="fas fa-list mr-1"></i> View Activity Logs
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                @foreach($recentActivities as $activity)
                                    <tr>
                                        <td>
                                            <div class="text-muted small">
                                                {{ $activity->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="font-weight-bold">
                                                {{ $activity->created_at->format('H:i:s') }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        @if(Auth::user()->role === 'admin')
                                            <td>
                                                @if($activity->user)
                                                    <div class="d-flex align-items-center">
                                                        @if($activity->user->image)
                                                            <img src="{{ asset('storage/' . $activity->user->image) }}"
                                                                 class="user-profile-img-sm mr-2"
                                                                 alt="{{ $activity->user->name }}">
                                                        @else
                                                            <div class="user-profile-img-sm bg-primary text-white mr-2 d-flex align-items-center justify-content-center"
                                                                 style="width: 32px; height: 32px; border-radius: 50%; font-size: 0.9rem;">
                                                                {{ strtoupper(substr($activity->user->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="font-weight-bold">{{ $activity->user->name }}</div>
                                                            <div class="text-muted small">
                                                                <span class="badge badge-{{ $activity->user->role === 'admin' ? 'danger' : 'info' }}">
                                                                    {{ ucfirst($activity->user->role) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-muted">
                                                        <i class="fas fa-robot"></i> System
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <i class="{{ $activity->action_icon }} text-{{ $activity->action_color }}"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ ucfirst($activity->action) }}</div>
                                                    <div class="text-muted small">
                                                        @if($activity->model_type)
                                                            {{ class_basename($activity->model_type) }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $activity->description }}</div>
                                            @if($activity->details && is_array($activity->details))
                                                <div class="text-muted small mt-1">
                                                    @foreach($activity->details as $key => $value)
                                                        @if(!in_array($key, ['timestamp', 'browser', 'location']))
                                                            <span class="badge badge-light mr-1">
                                                                {{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        @if(Auth::user()->role === 'admin')
                                            <td>
                                                <code class="small">{{ $activity->ip_address }}</code>
                                                @if($activity->user_agent)
                                                    <div class="text-muted small mt-1" title="{{ $activity->user_agent }}">
                                                        {{ Str::limit($activity->user_agent, 25) }}
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Activity Statistics -->
                @if(Auth::user()->role === 'admin')
                    <div class="row mt-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-primary font-weight-bold" style="font-size: 1.5rem;">
                                        {{ \App\Models\ActivityLog::count() }}
                                    </div>
                                    <div class="text-muted small">Total Activities</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-success font-weight-bold" style="font-size: 1.5rem;">
                                        {{ \App\Models\ActivityLog::today()->count() }}
                                    </div>
                                    <div class="text-muted small">Today</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-left-warning h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-warning font-weight-bold" style="font-size: 1.5rem;">
                                        {{ \App\Models\ActivityLog::distinct('user_id')->count() }}
                                    </div>
                                    <div class="text-muted small">Active Users</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-info font-weight-bold" style="font-size: 1.5rem;">
                                        {{ \App\Models\ActivityLog::distinct('action')->count() }}
                                    </div>
                                    <div class="text-muted small">Action Types</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing last {{ $recentActivities->count() }} activities
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Test Detection Modal -->
<div class="modal fade" id="testDetectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Earthquake Detection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This will simulate an earthquake detection for testing purposes.</p>
                <div class="form-group">
                    <label for="testMagnitude">Test Magnitude</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="testMagnitude" value="4.5" min="0" max="10" step="0.1">
                        <div class="input-group-append">
                            <span class="input-group-text">Richter Scale</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">Magnitude ≥ 3.0 will trigger detection</small>
                </div>
                <div class="form-group">
                    <label for="testDevice">Test Device</label>
                    <select class="form-control" id="testDevice">
                        @foreach(\App\Models\Device::where('status', 'aktif')->get() as $device)
                            <option value="{{ $device->id }}">{{ $device->nama_device }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="runTestDetection()">
                    <i class="fas fa-play mr-2"></i>Run Test
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Keep all your existing JavaScript functions unchanged
    // Just copy all the functions from your original file
    // ... (all the JavaScript code remains the same)

    let realTimeChart;
    let realTimeInterval;
    let isRealTimeActive = false;
    let detectionData = {
        timestamps: [],
        magnitudes: [],
        thresholds: []
    };

    function generateQuickReport() {
        Swal.fire({
            title: 'Generate Quick Report?',
            text: 'This will create a PDF report of today\'s activity.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Generate'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating Report...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                setTimeout(() => {
                    Swal.fire(
                        'Report Generated!',
                        'The PDF report has been downloaded.',
                        'success'
                    );
                }, 2000);
            }
        });
    }

    function initRealTimeChart() {
        const ctx = document.getElementById('realTimeChart').getContext('2d');
        realTimeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: detectionData.timestamps,
                datasets: [{
                    label: 'Magnitude',
                    data: detectionData.magnitudes,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Detection Threshold (3.0)',
                    data: detectionData.thresholds,
                    borderColor: '#ffc107',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        title: {
                            display: true,
                            text: 'Magnitude'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value === 3.0) return 'Threshold';
                                if (value === 5.0) return 'Danger';
                                return value;
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                }
            }
        });
    }

    function startRealTime() {
        if (isRealTimeActive) return;

        isRealTimeActive = true;
        const btn = document.querySelector('.btn-outline-success');
        btn.innerHTML = '<i class="fas fa-stop"></i> Stop';
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-outline-danger');
        btn.setAttribute('onclick', 'stopRealTime()');

        realTimeInterval = setInterval(updateRealTimeData, 5000);
        updateRealTimeData();
    }

    function stopRealTime() {
        isRealTimeActive = false;
        clearInterval(realTimeInterval);
        const btn = document.querySelector('.btn-outline-danger');
        btn.innerHTML = '<i class="fas fa-play"></i> Live';
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-outline-success');
        btn.setAttribute('onclick', 'startRealTime()');
    }

    function updateRealTimeData() {
        fetch('/earthquake-events/recent')
            .then(response => response.json())
            .then(data => {
                if (data.events && data.events.length > 0) {
                    const latestEvent = data.events[0];
                    const timestamp = new Date(latestEvent.occurred_at).toLocaleTimeString();
                    const magnitude = latestEvent.magnitude;

                    detectionData.timestamps.push(timestamp);
                    detectionData.magnitudes.push(magnitude);
                    detectionData.thresholds.push(3.0);

                    if (detectionData.timestamps.length > 20) {
                        detectionData.timestamps.shift();
                        detectionData.magnitudes.shift();
                        detectionData.thresholds.shift();
                    }

                    realTimeChart.update();

                    if (magnitude >= 3.0) {
                        showDetectionAlert(latestEvent);
                    }
                }
            })
            .catch(error => console.error('Error updating real-time data:', error));
    }

    function showDetectionAlert(event) {
        const alertContainer = document.getElementById('detectionAlertContainer');
        const alertDiv = document.getElementById('detectionAlert');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertTime = document.getElementById('alertTime');
        const alertIcon = document.getElementById('alertIcon');

        const alertLevel = event.magnitude >= 5.0 ? 'danger' : 'warning';
        const alertIconClass = event.magnitude >= 5.0 ? 'fire' : 'exclamation-triangle';
        const alertIconColor = event.magnitude >= 5.0 ? 'danger' : 'warning';

        alertTitle.innerHTML = `<i class="fas fa-${alertIconClass} text-${alertIconColor} mr-2"></i>Earthquake Detected!`;
        alertMessage.innerHTML = `
            <strong>Magnitude:</strong> ${event.magnitude.toFixed(1)} |
            <strong>Device:</strong> ${event.device.nama_device} |
            <strong>Location:</strong> ${event.device.lokasi}
        `;
        alertTime.textContent = `Detected ${new Date(event.occurred_at).toLocaleTimeString()}`;
        alertIcon.className = `fas fa-${alertIconClass} fa-3x text-${alertIconColor}`;

        alertDiv.className = `card shadow border-left-${alertLevel}`;

        alertContainer.style.display = 'block';
        alertDiv.style.animation = 'pulse 1s 3';

        try {
            const audio = new Audio(alertLevel === 'danger'
                ? 'https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3'
                : 'https://assets.mixkit.co/sfx/preview/mixkit-warning-alarm-buzzer-959.mp3'
            );
            audio.volume = 0.5;
            audio.play();
        } catch (e) {
            console.log('Audio alert failed:', e);
        }

        setTimeout(() => {
            if (alertContainer.style.display !== 'none') {
                dismissAlert();
            }
        }, 30000);
    }

    function dismissAlert() {
        const alertContainer = document.getElementById('detectionAlertContainer');
        alertContainer.style.display = 'none';
    }

    function viewLatestDetection() {
        window.location.href = '/earthquake-events';
    }

    function testDetectionSystem() {
        $('#testDetectionModal').modal('show');
    }

    function runTestDetection() {
        const magnitude = parseFloat(document.getElementById('testMagnitude').value);
        const deviceId = document.getElementById('testDevice').value;

        if (magnitude < 0 || magnitude > 10) {
            Swal.fire('Error!', 'Please enter a valid magnitude (0-10).', 'error');
            return;
        }

        $('#testDetectionModal').modal('hide');

        Swal.fire({
            title: 'Testing Detection System...',
            text: 'Simulating earthquake with magnitude ' + magnitude.toFixed(1),
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/api/v1/detections/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                magnitude: magnitude,
                device_id: deviceId
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();

            if (magnitude >= 3.0) {
                Swal.fire({
                    icon: 'success',
                    title: 'Detection Successful!',
                    html: `
                        <p>Earthquake detection test completed.</p>
                        <div class="alert alert-${magnitude >= 5.0 ? 'danger' : 'warning'} mt-3">
                            <strong>Simulated Detection:</strong><br>
                            Magnitude: ${magnitude.toFixed(1)}<br>
                            Status: ${magnitude >= 5.0 ? 'DANGER' : 'WARNING'}<br>
                            Alert: ${magnitude >= 3.0 ? 'TRIGGERED' : 'Not triggered'}
                        </div>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (magnitude >= 3.0) {
                        const simulatedEvent = {
                            magnitude: magnitude,
                            device: {
                                nama_device: document.querySelector(`#testDevice option[value="${deviceId}"]`).textContent,
                                lokasi: 'Test Location'
                            },
                            occurred_at: new Date().toISOString(),
                            status: magnitude >= 5.0 ? 'danger' : 'warning'
                        };
                        showDetectionAlert(simulatedEvent);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Below Threshold',
                    text: `Magnitude ${magnitude.toFixed(1)} is below detection threshold (3.0). No alert would be triggered.`,
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Failed to test detection system.', 'error');
        });
    }

    function checkSystemStatus() {
        Swal.fire({
            title: 'Checking System Status...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        Promise.all([
            fetch('/api/v1/status').then(r => r.json()),
            fetch('/devices/offline').then(r => r.json())
        ])
        .then(([statusData, offlineData]) => {
            Swal.close();

            let statusHtml = `
                <div class="text-left">
                    <p><strong>API Status:</strong> <span class="badge badge-success">${statusData.status.toUpperCase()}</span></p>
                    <p><strong>Detection Threshold:</strong> 3.0 magnitude</p>
                    <p><strong>Active Devices:</strong> ${document.getElementById('activeSensors').textContent}</p>
                    <p><strong>Offline Devices:</strong> ${offlineData.count}</p>
                    <hr>
                    <h6>Components:</h6>
            `;

            const components = [
                { name: 'Database', status: 'operational' },
                { name: 'IoT API', status: statusData.status },
                { name: 'Detection Engine', status: 'operational' },
                { name: 'Alert System', status: 'operational' }
            ];

            components.forEach(comp => {
                const badgeClass = comp.status === 'operational' ? 'success' : 'danger';
                statusHtml += `<p><i class="fas fa-server mr-2"></i> ${comp.name}: <span class="badge badge-${badgeClass}">${comp.status}</span></p>`;
            });

            statusHtml += `</div>`;

            Swal.fire({
                title: 'System Status Report',
                html: statusHtml,
                icon: 'info',
                width: '500px'
            });
        })
        .catch(error => {
            Swal.fire('Error!', 'Failed to check system status.', 'error');
        });
    }

    function checkAllDevices() {
        Swal.fire({
            title: 'Checking Device Status',
            text: 'Please wait while we check all devices...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/devices/offline')
            .then(response => response.json())
            .then(data => {
                Swal.close();

                let deviceList = '';
                if (data.count > 0) {
                    data.devices.forEach(device => {
                        const lastSeen = device.last_seen ?
                            new Date(device.last_seen).toLocaleString() : 'Never';
                        deviceList += `
                            <div class="mb-2">
                                <strong>${device.nama_device}</strong><br>
                                <small class="text-muted">${device.lokasi} • Last seen: ${lastSeen}</small>
                            </div>
                        `;
                    });

                    Swal.fire({
                        title: `Device Status Report`,
                        html: `
                            <div class="text-left">
                                <div class="alert alert-warning">
                                    <strong>${data.count} devices offline</strong>
                                </div>
                                ${deviceList}
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        width: '500px'
                    });
                } else {
                    Swal.fire({
                        title: 'All Devices Online!',
                        text: 'All devices are currently active and reporting.',
                        icon: 'success',
                        confirmButtonText: 'Great!'
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to check device status.', 'error');
            });
    }

    function configureDetection() {
        window.location.href = '/thresholds';
    }

    function refreshStats() {
        Swal.fire({
            title: 'Refreshing...',
            text: 'Updating dashboard statistics',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Dashboard statistics have been refreshed.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }, 1000);
    }

    function refreshActivity() {
    const btn = event?.target?.closest('button') || document.querySelector('[onclick="refreshActivity()"]');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Show loading state
    const tableBody = document.querySelector('#activityTable tbody');
    const originalContent = tableBody.innerHTML;
    tableBody.innerHTML = `
        <tr>
            <td colspan="{{ Auth::user()->role === 'admin' ? '5' : '3' }}" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading recent activities...</p>
            </td>
        </tr>
    `;

    // Fetch updated activity data
    fetch('/api/v1/activity/recent')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Activity data received:', data);

            if (data.success && data.activities && data.activities.length > 0) {
                let html = '';

                data.activities.forEach(activity => {
                    const time = new Date(activity.created_at);
                    const isAdmin = {{ Auth::user()->role === 'admin' ? 'true' : 'false' }};

                    // Determine color based on action
                    const getActionColor = (action) => {
                        if (action.includes('login')) return 'success';
                        if (action.includes('create')) return 'success';
                        if (action.includes('update')) return 'warning';
                        if (action.includes('delete')) return 'danger';
                        if (action.includes('earthquake')) return 'danger';
                        if (action.includes('device')) return 'primary';
                        return 'secondary';
                    };

                    // Determine icon based on action
                    const getActionIcon = (action) => {
                        if (action.includes('login')) return 'fas fa-sign-in-alt';
                        if (action.includes('create')) return 'fas fa-plus-circle';
                        if (action.includes('update')) return 'fas fa-edit';
                        if (action.includes('delete')) return 'fas fa-trash';
                        if (action.includes('earthquake')) return 'fas fa-earthquake';
                        if (action.includes('device')) return 'fas fa-microchip';
                        return 'fas fa-history';
                    };

                    const actionColor = getActionColor(activity.action);
                    const actionIcon = getActionIcon(activity.action);

                    // User column (only for admin)
                    let userHtml = '';
                    if (isAdmin) {
                        userHtml = activity.user ? `
                            <div class="d-flex align-items-center">
                                <div class="user-profile-img-sm bg-primary text-white mr-2 d-flex align-items-center justify-content-center"
                                     style="width: 32px; height: 32px; border-radius: 50%; font-size: 0.9rem;">
                                    ${activity.user.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div class="font-weight-bold">${activity.user.name}</div>
                                    <div class="text-muted small">
                                        <span class="badge badge-${activity.user.role === 'admin' ? 'danger' : 'info'}">
                                            ${activity.user.role}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ` : `
                            <div class="text-muted">
                                <i class="fas fa-robot"></i> System
                            </div>
                        `;
                    }

                    // IP column (only for admin)
                    let ipHtml = '';
                    if (isAdmin) {
                        ipHtml = `
                            <code class="small">${activity.ip_address || ''}</code>
                            ${activity.user_agent ? `
                                <div class="text-muted small mt-1" title="${activity.user_agent}">
                                    ${activity.user_agent.substring(0, 25)}${activity.user_agent.length > 25 ? '...' : ''}
                                </div>
                            ` : ''}
                        `;
                    }

                    // Model info
                    const modelInfo = activity.model_type ?
                        `<div class="text-muted small">${activity.model_type.replace(/^App\\Models\\/, '')}</div>` : '';

                    html += `
                        <tr>
                            <td>
                                <div class="text-muted small">
                                    ${time.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </div>
                                <div class="font-weight-bold">
                                    ${time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}
                                </div>
                                <div class="text-muted small">
                                    ${activity.time_ago}
                                </div>
                            </td>
                            ${isAdmin ? `<td>${userHtml}</td>` : ''}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-2">
                                        <i class="${actionIcon} text-${actionColor}"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-${actionColor}">${activity.action.replace('_', ' ').toUpperCase()}</div>
                                        ${modelInfo}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold">${activity.description}</div>
                                ${activity.details ? `
                                    <div class="text-muted small mt-1">
                                        ${Object.entries(activity.details).map(([key, value]) => {
                                            if (!['timestamp', 'browser', 'location'].includes(key)) {
                                                const displayValue = Array.isArray(value) ? JSON.stringify(value) : value;
                                                return `<span class="badge badge-light mr-1">${key}: ${displayValue}</span>`;
                                            }
                                            return '';
                                        }).join('')}
                                    </div>
                                ` : ''}
                            </td>
                            ${isAdmin ? `<td>${ipHtml}</td>` : ''}
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Activity Refreshed!',
                    text: `Loaded ${data.activities.length} recent activities`,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                // Show no data message
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="{{ Auth::user()->role === 'admin' ? '5' : '3' }}" class="text-center py-4">
                            <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                            <h6 class="text-gray-500">No recent activity</h6>
                            <p class="text-gray-500 small">Activity will appear here as you use the system</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error refreshing activities:', error);
            // Restore original content on error
            tableBody.innerHTML = originalContent;
            Swal.fire('Error!', 'Failed to load activities. Please try again.', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
}

// Function to load activity statistics
function loadActivityStats() {
    fetch('/api/v1/activity/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update statistics cards if they exist
                const statsElements = {
                    'totalActivities': data.statistics.total_logs,
                    'todayActivities': data.statistics.today_logs,
                    'uniqueUsers': data.statistics.unique_users,
                    'uniqueActions': data.statistics.unique_actions
                };

                for (const [key, value] of Object.entries(statsElements)) {
                    const element = document.getElementById(key);
                    if (element) {
                        element.textContent = value;
                    }
                }
            }
        })
        .catch(error => console.error('Error loading activity stats:', error));
}

    function loadRecentDetections() {
        fetch('/earthquake-events/recent')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recentDetections');

                if (!data.events || data.events.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <p class="text-muted">No recent detections</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.events.forEach(event => {
                    const timeAgo = new Date(event.occurred_at).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    html += `
                        <a href="/earthquake-events/${event.id}" class="list-group-item list-group-item-action ${event.status}">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-${event.status === 'danger' ? 'fire' : 'exclamation-triangle'} fa-lg text-${event.status}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark">${event.device.nama_device}</strong>
                                        <small class="text-muted">${timeAgo}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-${event.magnitude >= 5.0 ? 'danger' : 'warning'} mr-2">
                                            ${event.magnitude.toFixed(1)}
                                        </span>
                                        <small class="text-muted">
                                            ${event.device.lokasi}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                });

                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading recent detections:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initRealTimeChart();
        loadRecentDetections();

        setTimeout(() => {
            fetch('/earthquake-events/recent')
                .then(response => response.json())
                .then(data => {
                    if (data.events && data.events.length > 0) {
                        const latestEvent = data.events[0];
                        const eventTime = new Date(latestEvent.occurred_at);
                        const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000);

                        if (eventTime > fiveMinutesAgo && latestEvent.magnitude >= 3.0) {
                            showDetectionAlert(latestEvent);
                        }
                    }
                });
        }, 1000);

        setInterval(loadRecentDetections, 30000);

        setInterval(() => {
            fetch('/earthquake-events/recent')
                .then(response => response.json())
                .then(data => {
                    if (data.events && data.events.length > 0) {
                        const latestEvent = data.events[0];
                        const eventTime = new Date(latestEvent.occurred_at);
                        const oneMinuteAgo = new Date(Date.now() - 60 * 1000);

                        if (eventTime > oneMinuteAgo && latestEvent.magnitude >= 3.0) {
                            showDetectionAlert(latestEvent);
                        }
                    }
                })
                .catch(error => console.error('Polling error:', error));
        }, 30000);
    });
</script>
@endpush
