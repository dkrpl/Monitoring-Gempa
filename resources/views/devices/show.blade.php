@extends('layouts.app')

@section('title', 'Device Details - EQMonitor')
@section('page-title', 'Device Details: ' . $device->nama_device)

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('devices.edit', $device) }}" class="btn btn-warning">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <button type="button" class="btn btn-success ml-2" onclick="simulateHeartbeat({{ $device->id }})">
            <i class="fas fa-heartbeat mr-2"></i>Simulate Heartbeat
        </button>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Devices
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Device Overview -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Overview</h6>
            </div>
            <div class="card-body text-center">
                <div class="device-icon-large mb-4">
                    <i class="fas fa-microchip fa-4x text-primary"></i>
                </div>

                <h4 class="font-weight-bold">{{ $device->nama_device }}</h4>
                <span class="badge badge-{{ $device->status_color }} p-2 mb-3">
                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i>
                    {{ ucfirst($device->status) }}
                </span>

                <hr>

                <div class="text-left">
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                        <strong>Location:</strong>
                        <p class="text-muted mb-0">{{ $device->lokasi }}</p>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-fingerprint text-info mr-2"></i>
                        <strong>UUID:</strong>
                        <p class="text-muted mb-0">
                            <code style="font-size: 0.8rem;">{{ $device->uuid }}</code>
                        </p>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-calendar text-success mr-2"></i>
                        <strong>Created:</strong>
                        <p class="text-muted mb-0">{{ $device->created_at->format('F d, Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-clock text-warning mr-2"></i>
                        <strong>Last Seen:</strong>
                        <p class="text-muted mb-0">
                            @if($device->last_seen)
                                {{ $device->last_seen->diffForHumans() }}
                                <br><small>{{ $device->last_seen->format('M d, Y H:i:s') }}</small>
                            @else
                                Never
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-block" onclick="generateQRCode({{ $device->id }})">
                            <i class="fas fa-qrcode"></i><br>
                            <small>QR Code</small>
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <button type="button" class="btn btn-outline-info btn-block" onclick="viewLogs({{ $device->id }})">
                            <i class="fas fa-history"></i><br>
                            <small>View Logs</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="simulateEvent({{ $device->id }})">
                            <i class="fas fa-bell"></i><br>
                            <small>Test Alert</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-block status-btn"
                                data-device-id="{{ $device->id }}"
                                data-status="{{ $device->status }}">
                            <i class="fas fa-power-off"></i><br>
                            <small>{{ $device->status === 'aktif' ? 'Deactivate' : 'Activate' }}</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics & Charts -->
    <div class="col-lg-8">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Events</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_events'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-earthquake fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Warning Events</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['warning_events'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Danger Events</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['danger_events'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-fire fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Today's Logs</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['today_logs'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Activity (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Events & Logs -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Events</h6>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($device->earthquakeEvents->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <p class="text-muted">No earthquake events recorded</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($device->earthquakeEvents as $event)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} text-{{ $event->status_color }} mr-2"></i>
                                        {{ $event->status === 'danger' ? 'Danger' : 'Warning' }} Event
                                    </h6>
                                    <small class="text-muted">{{ $event->occurred_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    <strong>Magnitude:</strong> {{ $event->magnitude }}
                                </p>
                                <small class="text-muted">
                                    {{ $event->occurred_at->format('M d, Y H:i:s') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Logs</h6>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($device->logs->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                        <p class="text-muted">No logs available</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($device->logs as $log)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <i class="fas fa-{{ $log->status === 'online' ? 'wifi' : 'wifi-slash' }} text-{{ $log->status === 'online' ? 'success' : 'danger' }} mr-2"></i>
                                        {{ ucfirst($log->status) }}
                                    </h6>
                                    <small class="text-muted">{{ $log->logged_at->diffForHumans() }}</small>
                                </div>
                                @if($log->magnitude)
                                    <p class="mb-1">
                                        <strong>Magnitude:</strong> {{ $log->magnitude }}
                                    </p>
                                @endif
                                <small class="text-muted">
                                    {{ $log->logged_at->format('M d, Y H:i:s') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

        <!-- Danger Zone -->
        <div class="card shadow border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                </h6>
            </div>
            <div class="card-body">
                <p class="text-danger mb-4">
                    <strong>Warning:</strong> These actions are irreversible. Please proceed with caution.
                </p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-danger">
                                    <i class="fas fa-trash mr-2"></i>Delete Device
                                </h5>
                                <p class="card-text">
                                    Permanently delete this device and all associated data. This action cannot be undone.
                                </p>
                                <form action="{{ route('devices.destroy', $device) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger delete-btn">
                                        <i class="fas fa-trash mr-2"></i>Delete Device
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-warning">
                                    <i class="fas fa-redo mr-2"></i>Reset Device
                                </h5>
                                <p class="card-text">
                                    Reset this device to factory settings. This will clear all logs and events.
                                </p>
                                <button type="button" class="btn btn-warning" onclick="resetDevice({{ $device->id }})">
                                    <i class="fas fa-redo mr-2"></i>Reset Device
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="qr-code-placeholder mb-4">
                    <i class="fas fa-qrcode fa-5x text-primary"></i>
                </div>
                <h5>Device QR Code</h5>
                <p class="text-muted">Scan to view device information</p>
                <div class="mt-4">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-download mr-2"></i>Download QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .device-icon-large {
        width: 100px;
        height: 100px;
        background: linear-gradient(45deg, #4e73df, #224abe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
    }

    .qr-code-placeholder {
        width: 200px;
        height: 200px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        transform: translateX(5px);
    }

    .list-group-item:nth-child(odd) {
        border-left-color: #4e73df;
    }

    .list-group-item:nth-child(even) {
        border-left-color: #1cc88a;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Activity Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('activityChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['dates']),
                datasets: [{
                    label: 'Warning Events',
                    data: @json($chartData['warning']),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Danger Events',
                    data: @json($chartData['danger']),
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Status toggle
        const statusBtn = document.querySelector('.status-btn');
        if (statusBtn) {
            statusBtn.addEventListener('click', function() {
                const deviceId = this.dataset.deviceId;
                const currentStatus = this.dataset.status;
                const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';

                Swal.fire({
                    title: 'Change Device Status?',
                    text: `Change status to ${newStatus}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/devices/${deviceId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Updated!',
                                    'Device status has been updated.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        });
                    }
                });
            });
        }
    });

    function generateQRCode(deviceId) {
        $('#qrCodeModal').modal('show');

        // In a real app, you would fetch and display the QR code
        setTimeout(() => {
            // Simulate QR code generation
            Swal.fire({
                icon: 'success',
                title: 'QR Code Generated',
                text: 'QR code is ready for download',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1000);
    }

    function viewLogs(deviceId) {
        Swal.fire({
            title: 'Device Logs',
            text: 'Redirecting to logs page...',
            icon: 'info',
            timer: 1500,
            showConfirmButton: false
        });

        setTimeout(() => {
            // In a real app, redirect to logs page
            window.location.href = `#`;
        }, 1500);
    }

    function simulateEvent(deviceId) {
        Swal.fire({
            title: 'Simulate Earthquake Event?',
            text: 'This will create a test earthquake event for this device.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, simulate!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Simulate API call
                setTimeout(() => {
                    Swal.fire(
                        'Event Simulated!',
                        'Test earthquake event has been created.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                }, 1000);
            }
        });
    }

    function simulateHeartbeat(deviceId) {
        fetch(`/devices/${deviceId}/heartbeat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Heartbeat Recorded!',
                    text: 'Device last seen updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Failed to record heartbeat.', 'error');
        });
    }

    function resetDevice(deviceId) {
        Swal.fire({
            title: 'Reset Device?',
            text: 'This will clear all logs and events. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Reset!',
                    'Device has been reset to factory settings.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }

    // Auto-refresh last seen time every minute
    setInterval(() => {
        const lastSeenElement = document.querySelector('.card-body .text-left div:last-child p');
        if (lastSeenElement) {
            const timeText = lastSeenElement.textContent;
            if (timeText.includes('minute') || timeText.includes('second')) {
                // In a real app, you would fetch updated time
                // For now, we'll just reload after 5 minutes
                setTimeout(() => {
                    location.reload();
                }, 300000);
            }
        }
    }, 60000);
</script>
@endpush
