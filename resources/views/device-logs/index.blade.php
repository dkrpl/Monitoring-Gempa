@extends('layouts.app')

@section('title', 'Device Logs - EQMonitor')
@section('page-title', 'Device Logs Monitoring')

@section('action-button')
<div class="btn-group" role="group">
    <button type="button" class="btn btn-warning" onclick="simulateLog()">
        <i class="fas fa-bolt mr-2"></i>Simulate Log
    </button>
    <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#exportModal">
        <i class="fas fa-download mr-2"></i>Export
    </button>
    <button type="button" class="btn btn-danger ml-2" onclick="clearOldLogs()">
        <i class="fas fa-trash mr-2"></i>Clear Old Logs
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Logs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
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
                            Online Logs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['online']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wifi fa-2x text-gray-300"></i>
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
                            Avg Magnitude
                        </div>
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
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Today's Logs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Chart -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary mb-0">Activity Chart (Last 24 Hours)</h6>
                <div>
                    <select class="form-control form-control-sm" id="chartDeviceSelect" onchange="updateChart()" style="width: 200px;">
                        <option value="">All Devices</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->nama_device }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 300px;">
                    <canvas id="logsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary mb-0">Recent Activity</h6>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <div class="list-group list-group-flush" id="recentLogs">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted mb-0">Loading recent logs...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Logs Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary mb-0">All Device Logs</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshLogs()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($logs->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No device logs found</h5>
                        <p class="text-gray-500">Device logs will appear here when devices send data</p>
                        <button type="button" class="btn btn-primary mt-3" onclick="simulateLog()">
                            <i class="fas fa-bolt mr-2"></i>Simulate Log
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%">ID</th>
                                    <th style="width: 25%">Device</th>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 15%">Magnitude</th>
                                    <th style="width: 20%">Logged At</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td class="align-middle">{{ $log->id }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-microchip fa-lg text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $log->device->nama_device }}</div>
                                                <small class="text-muted">{{ $log->device->lokasi }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-{{ $log->status === 'online' ? 'success' : 'danger' }} p-2">
                                            <i class="fas fa-{{ $log->status === 'online' ? 'wifi' : 'wifi-slash' }} mr-1"></i>
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($log->magnitude !== null)
                                            <span class="badge badge-{{ $log->magnitude >= 5.0 ? 'danger' : ($log->magnitude >= 3.0 ? 'warning' : 'success') }} p-2">
                                                {{ number_format($log->magnitude, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <div class="font-weight-bold">{{ $log->logged_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $log->logged_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('device-logs.show', $log) }}"
                                               class="btn btn-info"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('device-logs.by-device', $log->device_id) }}"
                                               class="btn btn-primary"
                                               title="Device Logs">
                                                <i class="fas fa-list"></i>
                                            </a>
                                            <form action="{{ route('device-logs.destroy', $log) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-danger delete-btn"
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
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Device Logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportForm" action="{{ route('device-logs.export') }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="device_id">Device</label>
                        <select class="form-control" id="device_id" name="device_id">
                            <option value="">All Devices</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->nama_device }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="all">All Status</option>
                            <option value="online">Online Only</option>
                            <option value="offline">Offline Only</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }

    .badge-danger {
        background: linear-gradient(45deg, #e74a3b, #d52a1e);
    }

    .badge-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
        padding: 0.75rem 1rem;
    }

    .list-group-item.online {
        border-left-color: #1cc88a;
    }

    .list-group-item.offline {
        border-left-color: #e74a3b;
    }

    .list-group-item:hover {
        transform: translateX(5px);
        background-color: #f8f9fa;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-top: none;
    }

    .table td {
        vertical-align: middle !important;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .chart-area {
        position: relative;
        min-height: 300px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let logsChart;
    let currentDeviceId = '';

    // Load chart data
    function loadChartData(deviceId = '') {
        const url = deviceId ? `/device-logs/chart-data/${deviceId}` : '/device-logs/chart-data';

        fetch(url)
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
        const ctx = document.getElementById('logsChart').getContext('2d');

        if (logsChart) {
            logsChart.destroy();
        }

        logsChart = new Chart(ctx, {
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
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        },
                        grid: {
                            display: false
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
                        max: 10,
                        grid: {
                            drawBorder: false
                        }
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
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
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

    // Update chart based on device selection
    function updateChart() {
        const deviceId = document.getElementById('chartDeviceSelect').value;
        currentDeviceId = deviceId;
        loadChartData(deviceId);
    }

    // Load recent logs
    function loadRecentLogs() {
        fetch('/device-logs/recent')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recentLogs');

                if (data.logs.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No recent logs in the last hour</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.logs.forEach(log => {
                    const timeAgo = new Date(log.logged_at).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    html += `
                        <div class="list-group-item list-group-item-action ${log.status}">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-microchip text-primary mr-2"></i>
                                    <h6 class="mb-0 font-weight-bold">${log.device.nama_device}</h6>
                                </div>
                                <small class="text-muted text-nowrap">${timeAgo}</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge badge-${log.status === 'online' ? 'success' : 'danger'} mr-2">
                                    ${log.status}
                                </span>
                                ${log.magnitude !== null ? `
                                <span class="badge badge-${log.magnitude >= 5.0 ? 'danger' : log.magnitude >= 3.0 ? 'warning' : 'success'}">
                                    Mag: ${log.magnitude.toFixed(2)}
                                </span>` : ''}
                            </div>
                            <small class="text-muted d-block">
                                <i class="fas fa-map-marker-alt mr-1"></i>${log.device.lokasi}
                            </small>
                        </div>
                    `;
                });

                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading recent logs:', error);
            });
    }

    // Simulate device log
    function simulateLog() {
        Swal.fire({
            title: 'Simulate Device Log?',
            text: 'This will create a simulated device log for testing purposes.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, simulate!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/device-logs/simulate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
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

    // Clear old logs
    function clearOldLogs() {
        Swal.fire({
            title: 'Clear Old Logs?',
            text: 'This will delete all logs older than 30 days. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, clear them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/device-logs/clear-old', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire(
                        'Cleared!',
                        'Old logs have been cleared successfully.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Failed to clear old logs.',
                        'error'
                    );
                });
            }
        });
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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadChartData();
        loadRecentLogs();

        // Set default dates in export form
        const today = new Date().toISOString().split('T')[0];
        const oneWeekAgo = new Date();
        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        const oneWeekAgoStr = oneWeekAgo.toISOString().split('T')[0];

        document.getElementById('start_date').value = oneWeekAgoStr;
        document.getElementById('end_date').value = today;

        // Auto-refresh recent logs every 30 seconds
        setInterval(loadRecentLogs, 30000);

        // Auto-refresh chart every 2 minutes
        setInterval(() => {
            loadChartData(currentDeviceId);
        }, 120000);
    });
</script>
@endpush
