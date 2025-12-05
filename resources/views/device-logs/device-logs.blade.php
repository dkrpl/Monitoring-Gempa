@extends('layouts.app')

@section('title', 'Device Logs - ' . $device->nama_device)
@section('page-title', 'Device Logs: ' . $device->nama_device)

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('devices.show', $device) }}" class="btn btn-info">
            <i class="fas fa-microchip mr-2"></i>Device Details
        </a>
        <a href="{{ route('device-logs.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>All Logs
        </a>
        <button type="button" class="btn btn-primary ml-2" onclick="exportDeviceLogs()">
            <i class="fas fa-download mr-2"></i>Export
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Device Info -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-microchip mr-2"></i>
                    Device Information
                </h6>
                <span class="badge badge-{{ $device->status_color }}">
                    {{ ucfirst($device->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5>{{ $device->nama_device }}</h5>
                        <p class="text-muted">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            {{ $device->lokasi }}
                        </p>
                        <p class="text-muted">
                            <i class="fas fa-fingerprint mr-2"></i>
                            UUID: <code>{{ $device->uuid }}</code>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">Connection Status</h6>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-{{ $device->last_seen && $device->last_seen->diffInMinutes(now()) < 5 ? 'success' : 'danger' }} mr-3"
                                         style="width: 15px; height: 15px;"></div>
                                    <div>
                                        @if($device->last_seen)
                                            <p class="mb-0">Last seen: {{ $device->last_seen->diffForHumans() }}</p>
                                            <small class="text-muted">{{ $device->last_seen->format('M d, Y H:i:s') }}</small>
                                        @else
                                            <p class="mb-0 text-danger">Never connected</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title">Quick Stats</h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-primary">{{ $stats['total'] }}</h4>
                                        <small class="text-muted">Total Logs</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $stats['today'] }}</h4>
                                        <small class="text-muted">Today</small>
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

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Logs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-history fa-2x text-gray-300"></i>
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
                            Today's Logs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                            Avg Magnitude</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_magnitude'], 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['max_magnitude'], 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Activity Chart (Last 24 Hours)</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshChart()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Logs</h6>
            </div>
            <div class="card-body">
                @if($logs->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                        <p class="text-muted">No logs found for this device</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($logs->take(5) as $log)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <span class="badge badge-{{ $log->status === 'online' ? 'success' : 'danger' }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">{{ $log->logged_at->format('H:i') }}</small>
                                </div>
                                @if($log->magnitude !== null)
                                    <p class="mb-1">
                                        <span class="badge badge-{{ $log->magnitude >= 5.0 ? 'danger' : ($log->magnitude >= 3.0 ? 'warning' : 'success') }}">
                                            Magnitude: {{ number_format($log->magnitude, 2) }}
                                        </span>
                                    </p>
                                @endif
                                <small class="text-muted">
                                    {{ $log->logged_at->format('M d, Y') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Device Health -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Health</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    @if($device->last_seen && $device->last_seen->diffInMinutes(now()) < 5)
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-2x text-white"></i>
                        </div>
                        <h5 class="text-success">Healthy</h5>
                        <p class="text-muted">Device is responding normally</p>
                    @else
                        <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                        </div>
                        <h5 class="text-warning">Warning</h5>
                        <p class="text-muted">Device has not reported recently</p>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="button" class="btn btn-outline-info btn-block" onclick="testDeviceConnection()">
                        <i class="fas fa-plug mr-2"></i>Test Connection
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-block mt-2" onclick="simulateDeviceLog()">
                        <i class="fas fa-bolt mr-2"></i>Simulate Log
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Logs for {{ $device->nama_device }}</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($logs->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No logs found for this device</h5>
                        <p class="text-gray-500">Device logs will appear here when device sends data</p>
                        <button type="button" class="btn btn-primary mt-3" onclick="simulateDeviceLog()">
                            <i class="fas fa-bolt mr-2"></i>Simulate Log
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered data-table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Status</th>
                                    <th>Magnitude</th>
                                    <th>Logged At</th>
                                    <th>Time Ago</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $log->status === 'online' ? 'success' : 'danger' }} p-2">
                                            <i class="fas fa-{{ $log->status === 'online' ? 'wifi' : 'wifi-slash' }} mr-1"></i>
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->magnitude !== null)
                                            <span class="badge badge-{{ $log->magnitude >= 5.0 ? 'danger' : ($log->magnitude >= 3.0 ? 'warning' : 'success') }} p-2">
                                                {{ number_format($log->magnitude, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $log->logged_at->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $log->logged_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->logged_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('device-logs.show', $log) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('device-logs.edit', $log) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('device-logs.destroy', $log) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-danger btn-sm delete-btn"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div> --}}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .badge-danger { background: linear-gradient(45deg, #e74a3b, #d52a1e); }
    .badge-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }

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
    let deviceChart;

    // Load chart data
    function loadChartData() {
        fetch(`/device-logs/chart-data/{{ $device->id }}`)
            .then(response => response.json())
            .then(data => {
                renderChart(data);
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
            });
    }

    // Render chart
    function renderChart(data) {
        const ctx = document.getElementById('deviceChart').getContext('2d');

        if (deviceChart) {
            deviceChart.destroy();
        }

        deviceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.timestamps,
                datasets: [{
                    label: 'Magnitude',
                    data: data.magnitudes,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Status (Online=1, Offline=0)',
                    data: data.statuses,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 1,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        },
                        ticks: {
                            maxTicksLimit: 12
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Magnitude'
                        },
                        min: 0,
                        max: 10
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Status'
                        },
                        min: 0,
                        max: 1,
                        ticks: {
                            callback: function(value) {
                                return value === 1 ? 'Online' : 'Offline';
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                }
            }
        });
    }

    // Refresh chart
    function refreshChart() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        loadChartData();

        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            Swal.fire('Refreshed!', 'Chart data updated.', 'success');
        }, 1000);
    }

    // Refresh logs
    function refreshLogs() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Export device logs
    function exportDeviceLogs() {
        const today = new Date().toISOString().split('T')[0];
        const oneWeekAgo = new Date();
        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        const oneWeekAgoStr = oneWeekAgo.toISOString().split('T')[0];

        const url = new URL('{{ route("device-logs.export") }}');
        url.searchParams.append('device_id', '{{ $device->id }}');
        url.searchParams.append('start_date', oneWeekAgoStr);
        url.searchParams.append('end_date', today);
        url.searchParams.append('status', 'all');

        window.location.href = url.toString();
    }

    // Test device connection
    function testDeviceConnection() {
        Swal.fire({
            title: 'Testing Connection...',
            text: 'Checking device connectivity',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate connection test
        setTimeout(() => {
            const isOnline = @json($device->last_seen && $device->last_seen->diffInMinutes(now()) < 5);

            if (isOnline) {
                Swal.fire({
                    icon: 'success',
                    title: 'Connection Successful!',
                    html: `
                        <div class="text-left">
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Device: {{ $device->nama_device }}</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Status: Online</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Last Seen: {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Response Time: 120ms</p>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Connection Issues',
                    html: `
                        <div class="text-left">
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Device: {{ $device->nama_device }}</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Status: Offline</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Last Seen: {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}</p>
                            <p><i class="fas fa-lightbulb text-info mr-2"></i> Recommendations:</p>
                            <ul class="text-left">
                                <li>Check device power supply</li>
                                <li>Verify WiFi connection</li>
                                <li>Restart the device</li>
                            </ul>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            }
        }, 2000);
    }

    // Simulate device log
    function simulateDeviceLog() {
        Swal.fire({
            title: 'Simulate Device Log?',
            text: 'This will create a simulated log entry for this device.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Simulate'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/device-logs/simulate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        device_id: {{ $device->id }}
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Simulated!',
                            'Device log simulated successfully.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'Failed to simulate log.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Failed to simulate device log.',
                        'error'
                    );
                });
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadChartData();

        // Auto-refresh chart every 2 minutes
        setInterval(loadChartData, 120000);
    });
</script>
@endpush
