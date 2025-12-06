@extends('layouts.app')

@section('title', 'User Dashboard - EQMonitor')
@section('page-title', 'Earthquake Monitoring Dashboard')

@push('styles')
<style>
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

    #alertBanner {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    .chart-area {
        position: relative;
        height: 250px;
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

    .quick-action-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: #5a5c69;
        height: 100%;
    }

    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border-color: #4e73df;
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }

    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .safety-card {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        border: none;
    }

    .info-card {
        background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        border: none;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .badge-status {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
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
                            Earthquake Monitoring Dashboard
                        </div>
                        <p class="mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Real-time earthquake monitoring with automatic detection (≥3.0 magnitude)
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

<!-- Alert Banner -->
<div class="row mb-4" id="alertBannerContainer" style="display: none;">
    <div class="col-12">
        <div class="card shadow border-left-danger" id="alertBanner">
            <div class="card-header py-3 bg-danger text-white d-flex justify-content-between align-items-center">
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
                        <i class="fas fa-earthquake fa-3x text-danger" id="alertIcon"></i>
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
                        <button class="btn btn-danger" onclick="viewLatestAlert()">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Active Devices</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeDevicesCount">0</div>
                        <div class="mt-2">
                            <a href="#" class="text-xs text-primary" onclick="viewDevices()">
                                <i class="fas fa-arrow-right mr-1"></i>View All
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-microchip fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Today's Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayEventsCount">0</div>
                        <div class="mt-2">
                            <a href="{{ route('user.events.index') }}" class="text-xs text-warning">
                                <i class="fas fa-arrow-right mr-1"></i>View Events
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earthquake fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Active Alerts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeAlertsCount">0</div>
                        <div class="mt-2">
                            <a href="{{ route('user.events.alerts') }}" class="text-xs text-danger">
                                <i class="fas fa-arrow-right mr-1"></i>View Alerts
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            System Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge badge-success">Operational</span>
                        </div>
                        <div class="mt-2">
                            <a href="#" class="text-xs text-success" onclick="showSystemInfo()">
                                <i class="fas fa-info-circle mr-1"></i>System Info
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Live Monitoring Chart -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Live Seismic Activity (Last 24 Hours)
                </h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshChart()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleLiveMode()" id="liveModeBtn">
                        <i class="fas fa-play"></i> Live
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="activityChart"></canvas>
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

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <a href="{{ route('user.events.index') }}" class="card quick-action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-primary mb-2">
                                    <i class="fas fa-earthquake text-white"></i>
                                </div>
                                <small class="font-weight-bold">View Events</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('user.events.map') }}" class="card quick-action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-info mb-2">
                                    <i class="fas fa-map text-white"></i>
                                </div>
                                <small class="font-weight-bold">Event Map</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('user.events.alerts') }}" class="card quick-action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-danger mb-2">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                                <small class="font-weight-bold">Alerts</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('user.profile.edit') }}" class="card quick-action-card text-center p-3">
                            <div class="card-body p-2">
                                <div class="icon-circle bg-success mb-2">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <small class="font-weight-bold">My Profile</small>
                            </div>
                        </a>
                    </div>
                </div>

                <hr class="my-3">

                <div class="text-center">
                    <button class="btn btn-outline-warning btn-sm btn-block mb-2" onclick="showSafetyInstructions()">
                        <i class="fas fa-shield-alt mr-2"></i>Safety Instructions
                    </button>
                    <button class="btn btn-outline-info btn-sm btn-block" onclick="testNotification()">
                        <i class="fas fa-bell mr-2"></i>Test Notification
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Device Status -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-microchip mr-2"></i>Device Status
                </h6>
                <small class="text-muted">Recently Active</small>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="deviceStatusList">
                    <!-- Devices will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Instructions -->
    <div class="col-lg-6">
        <div class="card shadow safety-card h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-shield-alt mr-2"></i>Safety Instructions
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-danger mb-3">If you feel an earthquake:</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start mb-2">
                            <div class="mr-2">
                                <i class="fas fa-home text-primary"></i>
                            </div>
                            <div>
                                <strong>If indoors:</strong>
                                <ul class="small mb-0 pl-3">
                                    <li>Drop, Cover, Hold On</li>
                                    <li>Stay away from windows</li>
                                    <li>Do not use elevators</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start mb-2">
                            <div class="mr-2">
                                <i class="fas fa-tree text-success"></i>
                            </div>
                            <div>
                                <strong>If outdoors:</strong>
                                <ul class="small mb-0 pl-3">
                                    <li>Move to an open area</li>
                                    <li>Avoid buildings, trees</li>
                                    <li>Stay low to the ground</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Remember:</strong> This system provides early warning. Always follow official emergency procedures.
                </div>
                <button class="btn btn-danger btn-sm btn-block" onclick="showDetailedSafety()">
                    <i class="fas fa-book mr-2"></i>View Detailed Instructions
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recent Earthquake Events -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Recent Earthquake Events
                </h6>
                <a href="{{ route('user.events.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="recentEventsTable">
                        <thead>
                            <tr>
                                <th width="15%">Time</th>
                                <th width="15%">Magnitude</th>
                                <th width="15%">Status</th>
                                <th width="30%">Location</th>
                                <th width="25%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentEventsBody">
                            <!-- Events will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information Modal -->
<div class="modal fade" id="systemInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>System Information
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-cogs mr-2"></i>System Configuration
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-bell text-warning mr-2"></i>
                                        <strong>Detection Threshold:</strong> 3.0 magnitude
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-microchip text-success mr-2"></i>
                                        <strong>Active Sensors:</strong> <span id="modalActiveSensors">0</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-clock text-info mr-2"></i>
                                        <strong>Update Frequency:</strong> Real-time
                                    </li>
                                    <li>
                                        <i class="fas fa-shield-alt text-danger mr-2"></i>
                                        <strong>Security:</strong> Encrypted & Secure
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-user mr-2"></i>Your Account
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-user-circle text-primary mr-2"></i>
                                        <strong>Name:</strong> {{ Auth::user()->name }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-envelope text-secondary mr-2"></i>
                                        <strong>Email:</strong> {{ Auth::user()->email }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-calendar text-success mr-2"></i>
                                        <strong>Member Since:</strong> {{ Auth::user()->created_at->format('M d, Y') }}
                                    </li>
                                    <li>
                                        <i class="fas fa-user-shield text-warning mr-2"></i>
                                        <strong>Role:</strong> Regular User
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-lightbulb mr-2"></i>
                    <strong>Tip:</strong> For any issues or questions, please contact your system administrator.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Safety Instructions Modal -->
<div class="modal fade" id="safetyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Earthquake Safety Instructions
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="fas fa-home mr-2"></i>If indoors:
                                </h6>
                                <ol class="pl-3">
                                    <li><strong>DROP</strong> onto your hands and knees</li>
                                    <li><strong>COVER</strong> your head and neck with your arms</li>
                                    <li><strong>HOLD ON</strong> to any sturdy furniture</li>
                                    <li>Stay away from windows and glass</li>
                                    <li>Do not use elevators</li>
                                    <li>If in bed, stay there and protect your head</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-warning mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="fas fa-tree mr-2"></i>If outdoors:
                                </h6>
                                <ol class="pl-3">
                                    <li>Move to an open area away from buildings</li>
                                    <li>Avoid streetlights and utility wires</li>
                                    <li>Stay away from bridges and overpasses</li>
                                    <li>If driving, pull over and stay in vehicle</li>
                                    <li>If near the shore, move to higher ground</li>
                                    <li>If in mountains, watch for falling rocks</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>After the shaking stops:</strong> Check for injuries, be prepared for aftershocks, listen to official announcements.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="printSafetyInstructions()">
                    <i class="fas fa-print mr-2"></i>Print Instructions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let activityChart;
    let liveMode = false;
    let liveInterval;
    let alertSound = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3');

    // Load dashboard data
    function loadDashboardData() {
        fetch('/user/dashboard-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardStats(data);
                    updateDeviceStatus(data.devices);
                    updateRecentEvents(data.recentEvents);
                    checkForAlerts(data.alerts);
                }
            })
            .catch(error => console.error('Error loading dashboard:', error));
    }

    // Update statistics
    function updateDashboardStats(data) {
        document.getElementById('activeDevicesCount').textContent = data.activeDevices;
        document.getElementById('todayEventsCount').textContent = data.todayEvents;
        document.getElementById('activeAlertsCount').textContent = data.activeAlerts;
        document.getElementById('modalActiveSensors').textContent = data.activeDevices;

        // Update chart
        updateActivityChart(data.chartData);
    }

    // Update device status
    function updateDeviceStatus(devices) {
        const container = document.getElementById('deviceStatusList');

        if (!devices || devices.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-wifi-slash fa-2x text-gray-300 mb-3"></i>
                    <p class="text-muted mb-0">No active devices</p>
                </div>
            `;
            return;
        }

        let html = '';
        devices.forEach(device => {
            const statusClass = device.status === 'aktif' ? 'success' : 'danger';
            const lastSeen = device.last_seen ?
                new Date(device.last_seen).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) :
                'Never';

            html += `
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">
                            <i class="fas fa-microchip text-primary mr-2"></i>
                            ${device.nama_device}
                        </h6>
                        <span class="badge badge-${statusClass} badge-status">
                            ${device.status === 'aktif' ? 'Online' : 'Offline'}
                        </span>
                    </div>
                    <p class="mb-1">
                        <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                        ${device.lokasi}
                    </p>
                    <small class="text-muted">
                        Last seen: ${lastSeen}
                    </small>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Update recent events
    function updateRecentEvents(events) {
        const container = document.getElementById('recentEventsBody');

        if (!events || events.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <p class="text-muted mb-0">No recent earthquake events</p>
                        <small class="text-muted">System is monitoring for activity</small>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        events.forEach(event => {
            const time = new Date(event.occurred_at).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const date = new Date(event.occurred_at).toLocaleDateString();
            const magnitudeClass = event.magnitude >= 5.0 ? 'danger' :
                                  event.magnitude >= 3.0 ? 'warning' : 'success';
            const statusClass = event.status === 'danger' ? 'danger' : 'warning';
            const statusIcon = event.status === 'danger' ? 'fire' : 'exclamation-triangle';

            html += `
                <tr>
                    <td>
                        <div class="font-weight-bold">${date}</div>
                        <small class="text-muted">${time}</small>
                    </td>
                    <td>
                        <span class="badge badge-${magnitudeClass} p-2">
                            ${event.magnitude.toFixed(1)}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-${statusClass}">
                            <i class="fas fa-${statusIcon} mr-1"></i>
                            ${event.status.toUpperCase()}
                        </span>
                    </td>
                    <td>
                        <strong>${event.device_name}</strong><br>
                        <small class="text-muted">${event.device_location}</small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info mr-1" onclick="viewEventDetails(${event.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="showSafetyInstructions()">
                            <i class="fas fa-shield-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        container.innerHTML = html;
    }

    // Update activity chart
    function updateActivityChart(chartData) {
        const ctx = document.getElementById('activityChart').getContext('2d');

        if (activityChart) {
            activityChart.destroy();
        }

        // Create threshold line data
        const thresholdData = chartData.magnitudes.map(() => 3.0);
        const dangerThresholdData = chartData.magnitudes.map(() => 5.0);

        activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Magnitude',
                    data: chartData.magnitudes,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Warning Threshold (3.0)',
                    data: thresholdData,
                    borderColor: '#ffc107',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0
                }, {
                    label: 'Danger Threshold (5.0)',
                    data: dangerThresholdData,
                    borderColor: '#e74a3b',
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
                                if (value === 3.0) return 'Warning';
                                if (value === 5.0) return 'Danger';
                                return value;
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time (Last 24 Hours)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(1);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Check for alerts
    function checkForAlerts(alerts) {
        if (alerts && alerts.length > 0) {
            const alert = alerts[0]; // Show most recent alert
            showAlert(alert);
        }
    }

    // Show alert banner
    function showAlert(alert) {
        const banner = document.getElementById('alertBannerContainer');
        const title = document.getElementById('alertTitle');
        const message = document.getElementById('alertMessage');
        const time = document.getElementById('alertTime');
        const location = document.getElementById('alertLocation');
        const icon = document.getElementById('alertIcon');

        title.textContent = `ALERT: Earthquake Detected!`;
        message.textContent = `Magnitude: ${alert.magnitude.toFixed(1)} | Device: ${alert.device_name}`;
        time.textContent = new Date(alert.time).toLocaleTimeString();
        location.textContent = alert.location;

        if (alert.magnitude >= 5.0) {
            icon.className = 'fas fa-fire fa-3x text-danger';
            banner.style.borderColor = '#e74a3b';
        } else {
            icon.className = 'fas fa-exclamation-triangle fa-3x text-warning';
            banner.style.borderColor = '#ffc107';
        }

        banner.style.display = 'block';

        // Play alert sound
        try {
            alertSound.play();
        } catch (e) {
            console.log('Audio alert failed:', e);
        }

        // Flash effect
        let flashCount = 0;
        const flashInterval = setInterval(() => {
            banner.style.borderColor = flashCount % 2 === 0 ?
                (alert.magnitude >= 5.0 ? '#e74a3b' : '#ffc107') :
                (alert.magnitude >= 5.0 ? '#dc3545' : '#e0a800');
            flashCount++;
            if (flashCount > 10) clearInterval(flashInterval);
        }, 500);
    }

    // Dismiss alert
    function dismissAlert() {
        document.getElementById('alertBannerContainer').style.display = 'none';
        alertSound.pause();
        alertSound.currentTime = 0;
    }

    // View latest alert
    function viewLatestAlert() {
        window.location.href = "{{ route('user.events.alerts') }}";
    }

    // View event details
    function viewEventDetails(eventId) {
        window.location.href = `/user/events/${eventId}`;
    }

    // Toggle live mode
    function toggleLiveMode() {
        const btn = document.getElementById('liveModeBtn');

        if (!liveMode) {
            liveMode = true;
            btn.innerHTML = '<i class="fas fa-stop"></i> Stop';
            btn.classList.remove('btn-outline-success');
            btn.classList.add('btn-outline-danger');

            // Start live updates
            liveInterval = setInterval(loadDashboardData, 10000);

            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Live Mode Activated',
                text: 'Dashboard will update every 10 seconds',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            liveMode = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Live';
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-outline-success');

            // Stop live updates
            clearInterval(liveInterval);
        }
    }

    // Refresh chart
    function refreshChart() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        loadDashboardData();

        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            Swal.fire({
                icon: 'success',
                title: 'Refreshed!',
                text: 'Chart data updated successfully',
                timer: 1500,
                showConfirmButton: false
            });
        }, 1000);
    }

    // Show system information
    function showSystemInfo() {
        $('#systemInfoModal').modal('show');
    }

    // Show safety instructions
    function showSafetyInstructions() {
        $('#safetyModal').modal('show');
    }

    // Show detailed safety instructions
    function showDetailedSafety() {
        window.open('/user/safety-guide', '_blank');
    }

    // Print safety instructions
    function printSafetyInstructions() {
        window.print();
    }

    // Test notification
    function testNotification() {
        Swal.fire({
            title: 'Test Notification',
            text: 'This is a test notification. In a real earthquake, you would receive this alert.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Send Test Alert',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Simulate alert
                const testAlert = {
                    type: 'Earthquake Alert',
                    magnitude: 4.5,
                    location: 'Test Location',
                    device_name: 'Test Device',
                    time: new Date().toISOString()
                };

                showAlert(testAlert);

                Swal.fire({
                    icon: 'success',
                    title: 'Test Alert Sent!',
                    text: 'Check the alert banner at the top of the dashboard',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    // View devices
    function viewDevices() {
        Swal.fire({
            icon: 'info',
            title: 'Device Information',
            html: `
                <p>As a regular user, you can monitor device status but cannot manage devices.</p>
                <p>Contact your administrator for device management.</p>
            `,
            confirmButtonText: 'OK'
        });
    }

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardData();

        // Auto-refresh every 30 seconds if not in live mode
        setInterval(() => {
            if (!liveMode) {
                loadDashboardData();
            }
        }, 30000);

        // Check for alerts every 10 seconds
        setInterval(() => {
            fetch('/user/check-alerts')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.hasAlerts) {
                        checkForAlerts(data.alerts);
                    }
                })
                .catch(error => console.error('Error checking alerts:', error));
        }, 10000);
    });
</script>
@endpush
