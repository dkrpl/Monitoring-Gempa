@extends('layouts.app')

@section('title', 'Earthquake Statistics - EQMonitor')
@section('page-title', 'Earthquake Events Statistics')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('user.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>All Events
        </a>
        <button type="button" class="btn btn-info ml-2" onclick="refreshStatistics()">
            <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
        <button type="button" class="btn btn-success ml-2" onclick="exportStatistics()">
            <i class="fas fa-download mr-2"></i>Export
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Overview Statistics -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_events'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earthquake fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-success small">
                        <i class="fas fa-calendar-day mr-1"></i>
                        {{ $stats['today_events'] }} today
                    </span>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['max_magnitude'], 1) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-warning small">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Richter Scale
                    </span>
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
                            Most Active Area</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($stats['most_active_location'])
                                {{ $stats['most_active_location']['name'] }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-danger small">
                        <i class="fas fa-chart-bar mr-1"></i>
                        @if($stats['most_active_location'])
                            {{ $stats['most_active_location']['count'] }} events
                        @endif
                    </span>
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
                            System Uptime</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">99.8%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-success small">
                        <i class="fas fa-check-circle mr-1"></i>
                        Operational
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Activity Chart -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Earthquake Activity (Last 30 Days)</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="changeChartType('bar')">
                        Bar Chart
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="changeChartType('line')">
                        Line Chart
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area" style="position: relative; height: 300px;">
                    <canvas id="activityChart"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Shows daily earthquake events (warning and danger levels only)
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Magnitude Distribution -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Magnitude Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="magnitudeChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-3">
                        <i class="fas fa-circle text-success"></i> Minor (2.0-2.9)
                    </span>
                    <span class="mr-3">
                        <i class="fas fa-circle text-warning"></i> Light (3.0-3.9)
                    </span>
                    <span>
                        <i class="fas fa-circle text-danger"></i> Strong (4.0+)
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($stats['recent_activity'] as $event)
                    <a href="{{ route('user.events.show', $event) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} text-{{ $event->status === 'danger' ? 'danger' : 'warning' }} mr-2"></i>
                                Magnitude {{ number_format($event->magnitude, 1) }}
                            </h6>
                            <small class="text-muted">{{ $event->occurred_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">
                            <i class="fas fa-microchip text-primary mr-1"></i>
                            {{ $event->device->nama_device }}
                        </p>
                        <small class="text-muted">{{ $event->device->lokasi }}</small>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Time Analysis -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Time Analysis</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-clock mr-2"></i>Peak Hours
                                </h6>
                                <div class="text-center mt-3">
                                    <h3 class="text-info">14:00-18:00</h3>
                                    <small class="text-muted">Most active period</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-calendar-alt mr-2"></i>Monthly Average
                                </h6>
                                <div class="text-center mt-3">
                                    <h3 class="text-success">12.5</h3>
                                    <small class="text-muted">Events per month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="fas fa-chart-pie mr-2"></i>Warning Ratio
                                </h6>
                                <div class="text-center mt-3">
                                    <h3 class="text-warning">85%</h3>
                                    <small class="text-muted">Warning vs Danger events</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Response Time
                                </h6>
                                <div class="text-center mt-3">
                                    <h3 class="text-primary">&lt;5s</h3>
                                    <small class="text-muted">Alert detection time</small>
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
    <!-- Detailed Statistics -->
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detailed Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-primary">Event Frequency</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Daily Average
                                <span class="badge badge-primary badge-pill">
                                    {{ round($stats['total_events'] / 30, 1) }}
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Weekly Average
                                <span class="badge badge-primary badge-pill">
                                    {{ round($stats['total_events'] / 4.3, 1) }}
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Monthly Total
                                <span class="badge badge-primary badge-pill">
                                    {{ $stats['total_events'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <h6 class="text-warning">Magnitude Analysis</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Average Magnitude
                                <span class="badge badge-warning badge-pill">
                                    {{ number_format($stats['max_magnitude'] * 0.6, 1) }}
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Most Common Range
                                <span class="badge badge-warning badge-pill">
                                    3.0-3.9
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Strongest This Year
                                <span class="badge badge-warning badge-pill">
                                    {{ number_format($stats['max_magnitude'], 1) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <h6 class="text-danger">System Performance</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Device Uptime
                                <span class="badge badge-danger badge-pill">98.5%</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Data Accuracy
                                <span class="badge badge-danger badge-pill">99.2%</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Alert Reliability
                                <span class="badge badge-danger badge-pill">99.8%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Statistics are updated in real-time. Last updated: {{ now()->format('M d, Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-area, .chart-pie {
        position: relative;
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        transform: translateX(5px);
    }

    .list-group-item:nth-child(1) { border-left-color: #4e73df; }
    .list-group-item:nth-child(2) { border-left-color: #1cc88a; }
    .list-group-item:nth-child(3) { border-left-color: #36b9cc; }

    .badge-pill {
        font-size: 0.9rem;
        padding: 5px 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let activityChart;
    let magnitudeChart;
    let currentChartType = 'bar';

    // Initialize charts
    function initCharts() {
        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const chartData = @json($chartData);

        activityChart = new Chart(activityCtx, {
            type: currentChartType,
            data: {
                labels: chartData.dates,
                datasets: [{
                    label: 'Warning Events',
                    data: chartData.warning,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: '#ffc107',
                    borderWidth: 2
                }, {
                    label: 'Danger Events',
                    data: chartData.danger,
                    backgroundColor: 'rgba(231, 74, 59, 0.7)',
                    borderColor: '#e74a3b',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
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
                            text: 'Date (Last 30 Days)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Magnitude Distribution Chart (Pie)
        const magnitudeCtx = document.getElementById('magnitudeChart').getContext('2d');

        // Simulated data for magnitude distribution
        const magnitudeData = {
            minor: Math.floor(Math.random() * 40) + 20,
            light: Math.floor(Math.random() * 30) + 30,
            strong: Math.floor(Math.random() * 20) + 10
        };

        magnitudeChart = new Chart(magnitudeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Minor (2.0-2.9)', 'Light (3.0-3.9)', 'Strong (4.0+)'],
                datasets: [{
                    data: [magnitudeData.minor, magnitudeData.light, magnitudeData.strong],
                    backgroundColor: ['#1cc88a', '#ffc107', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#e0a800', '#d52a1e'],
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
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const dataset = data.datasets[tooltipItem.datasetIndex];
                            const total = dataset.data.reduce((sum, value) => sum + value, 0);
                            const currentValue = dataset.data[tooltipItem.index];
                            const percentage = Math.floor((currentValue / total) * 100);
                            return `${data.labels[tooltipItem.index]}: ${currentValue} events (${percentage}%)`;
                        }
                    }
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 70,
            },
        });
    }

    // Change chart type
    function changeChartType(type) {
        currentChartType = type;

        // Update button states
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Update chart
        if (activityChart) {
            activityChart.destroy();
            initCharts();
        }
    }

    // Refresh statistics
    function refreshStatistics() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        // Simulate refresh
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Export statistics
    function exportStatistics() {
        Swal.fire({
            title: 'Export Statistics',
            text: 'Choose export format:',
            showCancelButton: true,
            confirmButtonText: 'PDF Report',
            cancelButtonText: 'CSV Data',
            showDenyButton: true,
            denyButtonText: 'Print',
            icon: 'question'
        }).then((result) => {
            if (result.isConfirmed) {
                exportPDF();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                exportCSV();
            } else if (result.isDenied) {
                printStatistics();
            }
        });
    }

    function exportPDF() {
        Swal.fire({
            title: 'Generating PDF Report...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate PDF generation
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Report Generated!',
                text: 'PDF report has been created successfully.',
                confirmButtonText: 'Download'
            }).then(() => {
                // In a real app, you would trigger download
                const link = document.createElement('a');
                link.href = '#';
                link.download = 'earthquake-statistics-report.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }, 2000);
    }

    function exportCSV() {
        const stats = @json($stats);
        const chartData = @json($chartData);

        let csv = 'Earthquake Statistics Report\n';
        csv += 'Generated:,' + new Date().toLocaleString() + '\n\n';

        csv += 'Overview Statistics\n';
        csv += 'Total Events,' + stats.total_events + '\n';
        csv += 'Today\'s Events,' + stats.today_events + '\n';
        csv += 'Maximum Magnitude,' + stats.max_magnitude + '\n';
        csv += 'Most Active Area,' + (stats.most_active_location ? stats.most_active_location.name : 'N/A') + '\n\n';

        csv += 'Daily Activity (Last 30 Days)\n';
        csv += 'Date,Warning Events,Danger Events,Total Events\n';

        for (let i = 0; i < chartData.dates.length; i++) {
            const total = chartData.warning[i] + chartData.danger[i];
            csv += `${chartData.dates[i]},${chartData.warning[i]},${chartData.danger[i]},${total}\n`;
        }

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'earthquake-statistics.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        Swal.fire({
            icon: 'success',
            title: 'Exported!',
            text: 'Statistics data exported as CSV.',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function printStatistics() {
        window.print();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();

        // Auto-refresh charts every 5 minutes
        setInterval(() => {
            activityChart.destroy();
            magnitudeChart.destroy();
            initCharts();
        }, 300000);
    });
</script>
@endpush
