@extends('layouts.app')

@section('title', 'Active Alerts - EQMonitor')
@section('page-title', 'Active Earthquake Alerts')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('user.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>All Events
        </a>
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary ml-2">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
        <button type="button" class="btn btn-danger ml-2" onclick="testAlert()">
            <i class="fas fa-bell mr-2"></i>Test Alert
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Alert Summary -->
    <div class="col-12 mb-4">
        <div class="card border-left-danger shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="h5 mb-0 font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            ACTIVE ALERTS: {{ $alerts->total() }} Danger Events in Last 24 Hours
                        </div>
                        <div class="text-muted">
                            These are earthquake events with magnitude 5.0 or higher requiring immediate attention
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="alert-badge">
                            <span class="badge badge-danger p-3" style="font-size: 1.5rem;">
                                {{ $alerts->total() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($alerts->isEmpty())
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                <h3 class="text-success">No Active Alerts</h3>
                <p class="text-muted">No danger-level earthquake events detected in the last 24 hours.</p>
                <div class="mt-4">
                    <div class="alert alert-info d-inline-block">
                        <i class="fas fa-info-circle mr-2"></i>
                        The system is currently monitoring normally. All warning and minor events can be viewed in the events list.
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('user.events.index') }}" class="btn btn-primary">
                        <i class="fas fa-list mr-2"></i>View All Events
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary ml-2">
                        <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <!-- Alert Statistics -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Last 24 Hours</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alerts->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            Max Magnitude</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="maxMagnitude">0.0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Most Active Area</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="mostActiveArea">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
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
                            Latest Alert</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="latestAlert">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Alert Timeline -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Alert Timeline (Last 24 Hours)</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($alerts as $alert)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-fire text-danger mr-2"></i>
                                    Magnitude {{ number_format($alert->magnitude, 1) }}
                                </h6>
                                <small class="text-muted">{{ $alert->occurred_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                <i class="fas fa-microchip text-primary mr-1"></i>
                                {{ $alert->device->nama_device }}
                                <span class="badge badge-secondary ml-2">{{ $alert->device->lokasi }}</span>
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('user.events.show', $alert) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                                <button class="btn btn-sm btn-warning" onclick="showSafetyInstructions()">
                                    <i class="fas fa-shield-alt mr-1"></i>Safety Info
                                </button>
                                @if($alert->latitude && $alert->longitude)
                                <button class="btn btn-sm btn-success" onclick="viewOnMap({{ $alert->latitude }}, {{ $alert->longitude }})">
                                    <i class="fas fa-map mr-1"></i>View Map
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Emergency Actions -->
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Emergency Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-phone-alt text-danger mr-2"></i>
                                Call Emergency
                            </h6>
                            <strong class="text-danger">112</strong>
                        </div>
                        <p class="mb-1 small">All emergency services</p>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-first-aid text-warning mr-2"></i>
                                First Aid
                            </h6>
                            <strong class="text-danger">119</strong>
                        </div>
                        <p class="mb-1 small">Medical emergency</p>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-fire text-danger mr-2"></i>
                                Fire Department
                            </h6>
                            <strong class="text-danger">113</strong>
                        </div>
                        <p class="mb-1 small">Fire emergency</p>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-shield-alt text-primary mr-2"></i>
                                Safety Checklist
                            </h6>
                            <button class="btn btn-sm btn-primary" onclick="showSafetyInstructions()">
                                View
                            </button>
                        </div>
                        <p class="mb-1 small">Earthquake safety procedures</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bell mr-2"></i>Alert Notifications
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Alert System Status:</strong> Active
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="alertNotifications" checked>
                        <label class="custom-control-label" for="alertNotifications">Enable Push Notifications</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="soundAlerts" checked>
                        <label class="custom-control-label" for="soundAlerts">Enable Alert Sounds</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alert Threshold</label>
                    <select class="form-control" id="alertThreshold">
                        <option value="danger" selected>Danger Only (≥5.0)</option>
                        <option value="warning">Warning & Danger (≥3.0)</option>
                        <option value="all">All Events</option>
                    </select>
                </div>
                <button class="btn btn-primary btn-block" onclick="saveAlertSettings()">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Alert Details Table -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Active Alerts</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="alertsTable">
                        <thead>
                            <tr class="bg-danger text-white">
                                <th>Time</th>
                                <th>Magnitude</th>
                                <th>Device</th>
                                <th>Location</th>
                                <th>Coordinates</th>
                                <th>Depth</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $alert)
                            <tr class="alert-row">
                                <td>
                                    <strong>{{ $alert->occurred_at->format('M d, Y') }}</strong><br>
                                    <small class="text-muted">{{ $alert->occurred_at->format('H:i:s') }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-danger p-2" style="font-size: 1.1rem;">
                                        {{ number_format($alert->magnitude, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-microchip text-primary mr-2"></i>
                                    {{ $alert->device->nama_device }}
                                </td>
                                <td>{{ $alert->device->lokasi }}</td>
                                <td>
                                    @if($alert->latitude && $alert->longitude)
                                        <small>{{ number_format($alert->latitude, 4) }}, {{ number_format($alert->longitude, 4) }}</small>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                                <td>{{ $alert->depth ? number_format($alert->depth, 1) . ' km' : '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('user.events.show', $alert) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" onclick="showSafetyInstructions()">
                                            <i class="fas fa-shield-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="shareAlert({{ $alert->id }})">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $alerts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Safety Instructions Modal -->
<div class="modal fade" id="safetyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Emergency Earthquake Safety
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-skull-crossbones mr-2"></i>
                    <strong>DANGER LEVEL EARTHQUAKE DETECTED!</strong> Immediate action required.
                </div>

                <h6>Immediate Actions:</h6>
                <ol>
                    <li><strong>DROP</strong> to your hands and knees immediately</li>
                    <li><strong>COVER</strong> your head and neck with your arms</li>
                    <li><strong>HOLD ON</strong> to sturdy furniture until shaking stops</li>
                    <li>Stay away from windows, glass, and exterior walls</li>
                    <li>If outdoors, move to an open area away from buildings</li>
                    <li>If driving, pull over and stay in vehicle</li>
                </ol>

                <h6 class="mt-4">After Shaking Stops:</h6>
                <ul>
                    <li>Check for injuries and provide first aid</li>
                    <li>Check for fire, gas leaks, and electrical damage</li>
                    <li>Listen to battery-powered radio for information</li>
                    <li>Be prepared for aftershocks</li>
                    <li>Use telephone only for emergency calls</li>
                </ul>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-phone-alt mr-2"></i>
                    <strong>Emergency Contacts:</strong> 112 (All emergency) | 119 (Ambulance) | 113 (Fire)
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printSafetyInstructions()">
                    <i class="fas fa-print mr-2"></i>Print Instructions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .alert-badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e74a3b;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -33px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 3px #e74a3b;
    }

    .timeline-content {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #e74a3b;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .alert-row {
        transition: all 0.3s;
    }

    .alert-row:hover {
        background-color: #fff5f5;
        transform: translateX(5px);
    }

    #alertsTable thead th {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Calculate statistics
    document.addEventListener('DOMContentLoaded', function() {
        @if(!$alerts->isEmpty())
            // Calculate max magnitude
            const magnitudes = @json($alerts->pluck('magnitude'));
            const maxMagnitude = Math.max(...magnitudes);
            document.getElementById('maxMagnitude').textContent = maxMagnitude.toFixed(1);

            // Calculate most active area
            const locations = @json($alerts->pluck('device.lokasi'));
            const locationCounts = {};
            locations.forEach(location => {
                locationCounts[location] = (locationCounts[location] || 0) + 1;
            });

            let mostActive = '-';
            let maxCount = 0;
            for (const [location, count] of Object.entries(locationCounts)) {
                if (count > maxCount) {
                    maxCount = count;
                    mostActive = location;
                }
            }
            document.getElementById('mostActiveArea').textContent = mostActive;

            // Set latest alert time
            const latestAlert = @json($alerts->first());
            const latestTime = new Date(latestAlert.occurred_at);
            document.getElementById('latestAlert').textContent = latestTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            // Auto-refresh alerts every 30 seconds
            setInterval(() => {
                location.reload();
            }, 30000);
        @endif

        // Make alert rows clickable
        document.querySelectorAll('.alert-row').forEach(row => {
            const viewBtn = row.querySelector('a[href*="/events/"]');
            if (viewBtn) {
                row.style.cursor = 'pointer';
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('button') && !e.target.closest('a')) {
                        window.location.href = viewBtn.href;
                    }
                });
            }
        });
    });

    function testAlert() {
        const alertSound = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3');

        Swal.fire({
            title: 'Test Alert System',
            text: 'This will simulate an earthquake alert for testing purposes.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Test Alert',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show test alert
                Swal.fire({
                    title: '🚨 TEST ALERT: Earthquake Detected!',
                    html: `
                        <div class="text-left">
                            <div class="alert alert-danger">
                                <strong>TEST ONLY - NOT A REAL EARTHQUAKE</strong>
                            </div>
                            <p><strong>Magnitude:</strong> 6.5 (Simulated)</p>
                            <p><strong>Location:</strong> Test Area</p>
                            <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                This is a test of the earthquake alert system.
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonText: 'Acknowledge Test'
                });

                // Play alert sound
                alertSound.play().catch(e => console.log('Audio play failed:', e));
            }
        });
    }

    function showSafetyInstructions() {
        $('#safetyModal').modal('show');
    }

    function viewOnMap(latitude, longitude) {
        const url = `https://www.google.com/maps?q=${latitude},${longitude}`;
        window.open(url, '_blank');
    }

    function shareAlert(alertId) {
        const alert = @json($alerts->firstWhere('id', '==', 'placeholder'));

        // For demo, we'll use a generic message
        const shareData = {
            title: '🚨 Earthquake Alert',
            text: 'Danger-level earthquake detected. Stay safe and follow safety procedures.',
            url: window.location.origin + '/user/events/' + alertId
        };

        if (navigator.share) {
            navigator.share(shareData)
                .then(() => console.log('Shared successfully'))
                .catch(error => console.log('Error sharing:', error));
        } else {
            navigator.clipboard.writeText(shareData.text + '\n' + shareData.url)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Alert information copied to clipboard.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to copy alert information.',
                    });
                });
        }
    }

    function saveAlertSettings() {
        const notifications = document.getElementById('alertNotifications').checked;
        const sound = document.getElementById('soundAlerts').checked;
        const threshold = document.getElementById('alertThreshold').value;

        // In a real app, you would save these to user settings
        Swal.fire({
            icon: 'success',
            title: 'Settings Saved!',
            text: 'Your alert notification preferences have been updated.',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function printSafetyInstructions() {
        const printContent = document.getElementById('safetyModal').querySelector('.modal-body').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Emergency Earthquake Safety Instructions</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
                    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
                    .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
                </style>
            </head>
            <body>
                <h1>Emergency Earthquake Safety Instructions</h1>
                <p><em>Generated on ${new Date().toLocaleString()}</em></p>
                ${printContent}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endpush
