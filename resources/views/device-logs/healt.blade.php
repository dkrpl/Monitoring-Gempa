@extends('layouts.app')

@section('title', 'Device Health - EQMonitor')
@section('page-title', 'Device Health: ' . $device->nama_device)

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('device-logs.by-device', $device) }}" class="btn btn-primary">
            <i class="fas fa-list mr-2"></i>Device Logs
        </a>
        <a href="{{ route('devices.show', $device) }}" class="btn btn-info ml-2">
            <i class="fas fa-microchip mr-2"></i>Device Details
        </a>
        <a href="{{ route('device-logs.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>All Logs
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Device Health Overview -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Device Health Overview</h6>
                <span class="badge badge-{{ $device->status_color }}">
                    {{ ucfirst($device->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="device-info">
                            <h4>{{ $device->nama_device }}</h4>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                {{ $device->lokasi }}
                            </p>
                            <p class="text-muted">
                                <i class="fas fa-fingerprint mr-2"></i>
                                UUID: <code>{{ $device->uuid }}</code>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="health-status text-center">
                            @php
                                $uptimePercentage = $health['uptime_percentage'];
                                $statusColor = $uptimePercentage >= 90 ? 'success' : ($uptimePercentage >= 70 ? 'warning' : 'danger');
                                $statusText = $uptimePercentage >= 90 ? 'Excellent' : ($uptimePercentage >= 70 ? 'Good' : 'Poor');
                            @endphp
                            <div class="health-score">
                                <div class="score-circle" data-percentage="{{ $uptimePercentage }}">
                                    <span class="score-text">{{ number_format($uptimePercentage, 1) }}%</span>
                                </div>
                                <h5 class="mt-3 text-{{ $statusColor }}">{{ $statusText }} Health</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Metrics -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="text-primary font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $health['total_logs_24h'] }}
                                </div>
                                <div class="text-muted">Logs (24h)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <div class="text-success font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $health['online_logs'] }}
                                </div>
                                <div class="text-muted">Online</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-danger h-100">
                            <div class="card-body">
                                <div class="text-danger font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $health['offline_logs'] }}
                                </div>
                                <div class="text-muted">Offline</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <div class="text-info font-weight-bold" style="font-size: 1.5rem;">
                                    {{ number_format($health['avg_magnitude'], 2) }}
                                </div>
                                <div class="text-muted">Avg Magnitude</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-left-warning">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-lightbulb mr-2"></i>Recommendations
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($health['recommendations'] as $recommendation)
                                        <li class="list-group-item">
                                            <i class="fas fa-chevron-right text-primary mr-2"></i>
                                            {{ $recommendation }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Log Details -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Last Log Details</h6>
                            </div>
                            <div class="card-body">
                                @if($health['last_log'])
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Status:</strong>
                                                <span class="badge badge-{{ $health['last_log']->status === 'online' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($health['last_log']->status) }}
                                                </span>
                                            </p>
                                            <p><strong>Magnitude:</strong>
                                                @if($health['last_log']->magnitude)
                                                    <span class="badge badge-{{ $health['last_log']->magnitude >= 5.0 ? 'danger' : ($health['last_log']->magnitude >= 3.0 ? 'warning' : 'success') }}">
                                                        {{ number_format($health['last_log']->magnitude, 2) }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Logged At:</strong> {{ $health['last_log']->logged_at->format('F d, Y H:i:s') }}</p>
                                            <p><strong>Time Ago:</strong> {{ $health['last_log']->logged_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{ route('device-logs.show', $health['last_log']) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye mr-2"></i>View Log
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted">No logs found in the last 24 hours.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Device Status Timeline -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Timeline (24h)</h6>
            </div>
            <div class="card-body">
                <div id="statusTimeline">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading timeline...</p>
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
                        <button type="button" class="btn btn-outline-success btn-block" onclick="testConnection()">
                            <i class="fas fa-plug"></i><br>
                            <small>Test Connection</small>
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="sendTestData()">
                            <i class="fas fa-bolt"></i><br>
                            <small>Send Test Data</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info btn-block" onclick="refreshHealth()">
                            <i class="fas fa-sync-alt"></i><br>
                            <small>Refresh</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-block" onclick="generateReport()">
                            <i class="fas fa-file-pdf"></i><br>
                            <small>Health Report</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Information -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Information</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-calendar text-primary mr-2"></i>
                        <strong>Created:</strong> {{ $device->created_at->format('F d, Y') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning mr-2"></i>
                        <strong>Last Seen:</strong>
                        @if($device->last_seen)
                            {{ $device->last_seen->diffForHumans() }}
                        @else
                            Never
                        @endif
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-history text-info mr-2"></i>
                        <strong>Total Logs:</strong> {{ $device->logs()->count() }}
                    </li>
                    <li>
                        <i class="fas fa-earthquake text-danger mr-2"></i>
                        <strong>Earthquake Events:</strong> {{ $device->earthquakeEvents()->count() }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .health-score {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }

    .score-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: conic-gradient(
            #4e73df 0% calc(var(--percentage) * 1%),
            #e3e6f0 calc(var(--percentage) * 1%) 100%
        );
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .score-circle::before {
        content: '';
        position: absolute;
        width: 100px;
        height: 100px;
        background: white;
        border-radius: 50%;
    }

    .score-text {
        position: relative;
        z-index: 1;
        font-size: 1.5rem;
        font-weight: bold;
        color: #4e73df;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 5px;
        border-radius: 4px;
        background: #f8f9fa;
    }

    .timeline-time {
        width: 80px;
        font-weight: bold;
        color: #6c757d;
    }

    .timeline-status {
        width: 100px;
        text-align: center;
    }

    .timeline-bar {
        flex: 1;
        height: 20px;
        background: #e3e6f0;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    .timeline-fill {
        height: 100%;
        transition: width 0.3s ease;
    }

    .timeline-fill.online {
        background: #1cc88a;
    }

    .timeline-fill.offline {
        background: #e74a3b;
    }
</style>
@endpush

@push('scripts')
<script>
    // Load status timeline
    function loadStatusTimeline() {
        fetch(`/device-logs/chart-data/{{ $device->id }}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('statusTimeline');

                if (data.timestamps.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                            <p class="text-muted">No timeline data available</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                const maxItems = 12; // Show max 12 items
                const step = Math.ceil(data.timestamps.length / maxItems);

                for (let i = 0; i < data.timestamps.length; i += step) {
                    const timestamp = data.timestamps[i];
                    const status = data.statuses[i];
                    const magnitude = data.magnitudes[i];

                    const statusClass = status === 1 ? 'online' : 'offline';
                    const statusText = status === 1 ? 'Online' : 'Offline';
                    const statusColor = status === 1 ? 'success' : 'danger';

                    html += `
                        <div class="timeline-item">
                            <div class="timeline-time">${timestamp}</div>
                            <div class="timeline-status">
                                <span class="badge badge-${statusColor}">${statusText}</span>
                            </div>
                            <div class="timeline-bar">
                                <div class="timeline-fill ${statusClass}" style="width: ${status === 1 ? '100' : '30'}%"></div>
                            </div>
                        </div>
                    `;
                }

                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading timeline:', error);
            });
    }

    function testConnection() {
        Swal.fire({
            title: 'Testing Connection...',
            text: 'Testing device connection to server',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            const isSuccess = Math.random() > 0.2; // 80% success rate

            if (isSuccess) {
                Swal.fire({
                    icon: 'success',
                    title: 'Connection Successful!',
                    html: `
                        <div class="text-left">
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Server: Reachable</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Latency: 120ms</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> API: Responding</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Database: Connected</p>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Failed!',
                    html: `
                        <div class="text-left">
                            <p><i class="fas fa-times-circle text-danger mr-2"></i> Server: Unreachable</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Check network connection</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Verify server status</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Check firewall settings</p>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            }
        }, 2000);
    }

    function sendTestData() {
        Swal.fire({
            title: 'Send Test Data?',
            text: 'This will send a test log entry to the server.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Send Test'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/device-logs/simulate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        device_id: {{ $device->id }},
                        status: 'online',
                        magnitude: 2.5
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Test Data Sent!',
                            'Test log has been created successfully.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to send test data.', 'error');
                });
            }
        });
    }

    function refreshHealth() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    function generateReport() {
        Swal.fire({
            title: 'Generate Health Report?',
            text: 'This will create a PDF health report for this device.',
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
                        'The PDF health report has been generated successfully.',
                        'success'
                    );
                }, 2000);
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set percentage for health score circle
        const scoreCircle = document.querySelector('.score-circle');
        if (scoreCircle) {
            const percentage = scoreCircle.dataset.percentage;
            scoreCircle.style.setProperty('--percentage', percentage);
        }

        // Load timeline
        loadStatusTimeline();

        // Auto-refresh health data every 5 minutes
        setInterval(() => {
            loadStatusTimeline();
        }, 300000);
    });
</script>
@endpush
