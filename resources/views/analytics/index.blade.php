@extends('layouts.app')

@section('title', 'Analytics Dashboard - EQMonitor')
@section('page-title', 'Analytics Dashboard')

@section('action-button')
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customReportModal">
            <i class="fas fa-chart-line mr-2"></i>Custom Report
        </button>
        <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#exportModal">
            <i class="fas fa-download mr-2"></i>Export Data
        </button>
        <button type="button" class="btn btn-success ml-2" onclick="refreshAnalytics()">
            <i class="fas fa-sync-alt mr-2"></i>Refresh
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
                            Total Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_events']) }}</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-success mr-2">
                                <i class="fas fa-arrow-up"></i> {{ $stats['today_events'] }} today
                            </span>
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
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Devices</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_devices'] }}/{{ $stats['total_devices'] }}</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-success mr-2">
                                <i class="fas fa-percentage"></i> {{ $stats['device_uptime'] }}% uptime
                            </span>
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
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Warning Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['warning_count']) }}</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-warning mr-2">
                                <i class="fas fa-exclamation-triangle"></i> {{ $stats['today_warning'] }} today
                            </span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                            Danger Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['danger_count']) }}</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-danger mr-2">
                                <i class="fas fa-fire"></i> {{ $stats['today_danger'] }} today
                            </span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Events Trend Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Earthquake Events Trend (Last 30 Days)</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <a class="dropdown-item" href="#" onclick="changeChartType('line')">Line Chart</a>
                        <a class="dropdown-item" href="#" onclick="changeChartType('bar')">Bar Chart</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="downloadChart('eventsChart')">Download Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="eventsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Magnitude Distribution -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Magnitude Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="magnitudeChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> 0-2.9
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> 3.0-4.9
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> 5.0+
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Hourly Activity -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hourly Activity Pattern</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Activity -->
<div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Top Active Devices</h6>
            <a href="{{ route('devices.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Events</th>
                            <th>Avg Magnitude</th>
                            <th>Max Magnitude</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topDevices as $device)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $device->nama_device }}</strong><br>
                                    <small class="text-muted">{{ $device->lokasi }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $device->event_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $device->avg_magnitude >= 5.0 ? 'danger' : ($device->avg_magnitude >= 3.0 ? 'warning' : 'success') }}">
                                    {{ number_format($device->avg_magnitude, 1) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $device->max_magnitude >= 5.0 ? 'danger' : ($device->max_magnitude >= 3.0 ? 'warning' : 'success') }}">
                                    {{ number_format($device->max_magnitude, 1) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $device->status_color }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Events -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Earthquake Events</h6>
                <a href="{{ route('earthquake-events.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Device</th>
                                <th>Magnitude</th>
                                <th>Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEvents as $event)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $event->occurred_at->format('M d, Y') }}</strong><br>
                                        <small class="text-muted">{{ $event->occurred_at->format('H:i:s') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('devices.show', $event->device_id) }}">
                                        {{ $event->device->nama_device }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $event->magnitude_color }} p-2">
                                        {{ number_format($event->magnitude, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $event->status_color }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($event->hasLocation())
                                        <small class="text-success">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ number_format($event->latitude, 2) }}, {{ number_format($event->longitude, 2) }}
                                        </small>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Warning
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> Danger
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Normal
                    </span>
                </div>
                <div class="mt-4">
                    <h6>Average Magnitude by Status:</h6>
                    <ul class="list-unstyled">
                        @foreach($chartData['status_distribution']['avg_magnitudes'] as $index => $avg)
                        <li class="mb-2">
                            <i class="fas fa-circle text-{{ $chartData['status_distribution']['colors'][$index] }} mr-2"></i>
                            <strong>{{ $chartData['status_distribution']['labels'][$index] }}:</strong>
                            {{ $avg }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Report Modal -->
<div class="modal fade" id="customReportModal" tabindex="-1" role="dialog" aria-labelledby="customReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customReportModalLabel">Generate Custom Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="customReportForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ date('Y-m-d', strtotime('-30 days')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="device_id">Device (Optional)</label>
                                <select class="form-control" id="device_id" name="device_id">
                                    <option value="">All Devices</option>
                                    @foreach($chartData['device_activity'] as $device)
                                        <option value="{{ $device->id }}">{{ $device->nama_device }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report_type">Report Type</label>
                                <select class="form-control" id="report_type" name="report_type" required>
                                    <option value="daily">Daily Summary</option>
                                    <option value="weekly">Weekly Summary</option>
                                    <option value="monthly">Monthly Summary</option>
                                    <option value="device_summary">Device Summary</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="report_preview">Report Preview</label>
                                <div id="reportPreview" class="card bg-light">
                                    <div class="card-body">
                                        <p class="text-muted mb-0">Select parameters to preview report...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" onclick="previewReport()">
                        <i class="fas fa-eye mr-2"></i>Preview
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Analytics Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportForm" action="{{ route('analytics.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="export_type">Data Type</label>
                        <select class="form-control" id="export_type" name="type" required>
                            <option value="events_summary">Events Summary</option>
                            <option value="devices_summary">Devices Summary</option>
                            <option value="full_report">Full Analytics Report</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="export_format">Format</label>
                        <select class="form-control" id="export_format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        CSV format is recommended for spreadsheet applications.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-area {
        position: relative;
        height: 300px;
    }

    .chart-bar {
        position: relative;
        height: 300px;
    }

    .chart-pie {
        position: relative;
        height: 250px;
    }

    .card-body canvas {
        max-width: 100%;
    }

    .badge-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
    .badge-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .badge-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    .badge-danger { background: linear-gradient(45deg, #e74a3b, #be2617); }

    .table th {
        font-weight: 600;
        color: #4e73df;
        border-top: none;
    }

    .dropdown-menu {
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    // Chart instances
    let eventsChart, magnitudeChart, hourlyChart, statusChart;
    let currentChartType = 'line';

    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Events Trend Chart
        const eventsCtx = document.getElementById('eventsChart').getContext('2d');
        eventsChart = new Chart(eventsCtx, {
            type: currentChartType,
            data: {
                labels: @json($chartData['events_by_day']['dates']),
                datasets: [{
                    label: 'Total Events',
                    data: @json($chartData['events_by_day']['totals']),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Warning Events',
                    data: @json($chartData['events_by_day']['warnings']),
                    borderColor: '#f6c23e',
                    backgroundColor: 'rgba(246, 194, 62, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Danger Events',
                    data: @json($chartData['events_by_day']['dangers']),
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
                        },
                        title: {
                            display: true,
                            text: 'Number of Events'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
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

        // Magnitude Distribution Chart
        const magnitudeCtx = document.getElementById('magnitudeChart').getContext('2d');
        magnitudeChart = new Chart(magnitudeCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['magnitude_distribution']['labels']),
                datasets: [{
                    data: @json($chartData['magnitude_distribution']['counts']),
                    backgroundColor: @json($chartData['magnitude_distribution']['colors']),
                    hoverBackgroundColor: @json($chartData['magnitude_distribution']['colors']),
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 70,
            },
        });

        // Hourly Activity Chart
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        hourlyChart = new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['hourly_activity']['hours']),
                datasets: [{
                    label: 'Number of Events',
                    data: @json($chartData['hourly_activity']['counts']),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }, {
                    label: 'Avg Magnitude',
                    data: @json($chartData['hourly_activity']['avg_magnitudes']),
                    type: 'line',
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Events'
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        max: 10,
                        title: {
                            display: true,
                            text: 'Average Magnitude'
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
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: @json($chartData['status_distribution']['labels']),
                datasets: [{
                    data: @json($chartData['status_distribution']['counts']),
                    backgroundColor: @json($chartData['status_distribution']['colors']),
                    hoverBackgroundColor: @json($chartData['status_distribution']['colors']),
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                plugins: {
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: function(value, context) {
                            return value;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Auto-refresh real-time stats every 60 seconds
        setInterval(updateRealTimeStats, 60000);
    });

    // Change chart type
    function changeChartType(type) {
        currentChartType = type;
        eventsChart.destroy();

        const eventsCtx = document.getElementById('eventsChart').getContext('2d');
        eventsChart = new Chart(eventsCtx, {
            type: type,
            data: {
                labels: @json($chartData['events_by_day']['dates']),
                datasets: [{
                    label: 'Total Events',
                    data: @json($chartData['events_by_day']['totals']),
                    borderColor: '#4e73df',
                    backgroundColor: type === 'bar' ? 'rgba(78, 115, 223, 0.8)' : 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: type === 'line',
                    tension: 0.4
                }, {
                    label: 'Warning Events',
                    data: @json($chartData['events_by_day']['warnings']),
                    borderColor: '#f6c23e',
                    backgroundColor: type === 'bar' ? 'rgba(246, 194, 62, 0.8)' : 'rgba(246, 194, 62, 0.1)',
                    borderWidth: 2,
                    fill: type === 'line',
                    tension: 0.4
                }, {
                    label: 'Danger Events',
                    data: @json($chartData['events_by_day']['dangers']),
                    borderColor: '#e74a3b',
                    backgroundColor: type === 'bar' ? 'rgba(231, 74, 59, 0.8)' : 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    fill: type === 'line',
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
                        },
                        title: {
                            display: true,
                            text: 'Number of Events'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
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

        Swal.fire('Success!', `Changed to ${type} chart.`, 'success');
    }

    // Download chart as image
    function downloadChart(chartId) {
        const canvas = document.getElementById(chartId);
        const link = document.createElement('a');
        link.download = `chart-${chartId}-${new Date().toISOString().slice(0,10)}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();

        Swal.fire('Downloaded!', 'Chart has been downloaded as PNG.', 'success');
    }

    // Preview custom report
    function previewReport() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const deviceId = document.getElementById('device_id').value;
        const reportType = document.getElementById('report_type').value;

        if (!startDate || !endDate) {
            Swal.fire('Error!', 'Please select start and end dates.', 'error');
            return;
        }

        const preview = document.getElementById('reportPreview');
        preview.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Generating preview...</p>
            </div>
        `;

        fetch('/analytics/custom-report', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate,
                device_id: deviceId,
                report_type: reportType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let previewHtml = `
                    <h6>Report Preview</h6>
                    <p class="mb-2"><strong>Type:</strong> ${data.report_type}</p>
                    <p class="mb-2"><strong>Period:</strong> ${data.start_date} to ${data.end_date}</p>
                    <hr>
                `;

                if (data.data && data.data.length > 0) {
                    const sample = data.data[0];
                    previewHtml += `
                        <p><strong>Sample Data:</strong></p>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        ${Object.keys(sample).map(key => `<th>${key}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        ${Object.values(sample).map(value => `<td>${value}</td>`).join('')}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="mb-0"><strong>Total Records:</strong> ${data.data.length}</p>
                    `;
                } else {
                    previewHtml += '<p class="text-muted">No data found for the selected criteria.</p>';
                }

                preview.innerHTML = previewHtml;
            } else {
                preview.innerHTML = '<p class="text-danger">Error generating preview.</p>';
            }
        })
        .catch(error => {
            preview.innerHTML = '<p class="text-danger">Error loading preview.</p>';
            console.error('Error:', error);
        });
    }

    // Generate custom report
    document.getElementById('customReportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const params = new URLSearchParams(formData);

        Swal.fire({
            title: 'Generating Report...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/analytics/custom-report', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: params
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();

            if (data.success) {
                // Create download link
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `report-${data.report_type}-${data.start_date}-to-${data.end_date}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                Swal.fire(
                    'Report Generated!',
                    'The report has been downloaded successfully.',
                    'success'
                );

                $('#customReportModal').modal('hide');
            } else {
                Swal.fire('Error!', 'Failed to generate report.', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Failed to generate report.', 'error');
            console.error('Error:', error);
        });
    });

    // Refresh analytics
    function refreshAnalytics() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
        btn.disabled = true;

        fetch('/analytics/real-time-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stats in UI
                    updateStatsDisplay(data.stats);

                    Swal.fire({
                        icon: 'success',
                        title: 'Analytics Refreshed!',
                        text: 'Data has been updated with latest information.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to refresh analytics.', 'error');
            })
            .finally(() => {
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }, 1000);
            });
    }

    // Update real-time stats
    function updateRealTimeStats() {
        fetch('/analytics/real-time-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatsDisplay(data.stats);
                }
            })
            .catch(error => console.error('Error updating stats:', error));
    }

    // Update stats display
    function updateStatsDisplay(stats) {
        // Update stats cards if elements exist
        const elements = {
            'total_events_today': '.card:first-child .h5',
            'warning_events_today': '.card:nth-child(3) .h5',
            'danger_events_today': '.card:last-child .h5',
            'active_devices': '.card:nth-child(2) .h5'
        };

        for (const [key, selector] of Object.entries(elements)) {
            const element = document.querySelector(selector);
            if (element && stats[key] !== undefined) {
                const currentValue = parseInt(element.textContent.replace(/,/g, ''));
                const newValue = stats[key];

                if (currentValue !== newValue) {
                    // Animate value change
                    animateValue(element, currentValue, newValue, 500);
                }
            }
        }
    }

    // Animate value change
    function animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.abs(Math.floor(duration / range));
        let current = start;

        const timer = setInterval(function() {
            current += increment;
            element.textContent = current.toLocaleString();

            if (current === end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    // Export form submission
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });
</script>
@endpush
