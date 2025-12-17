@extends('layouts.app')

@section('title', 'Earthquake Events - EQMonitor')
@section('page-title', 'Earthquake Events Monitoring')

@section('action-button')
<div class="btn-group" role="group">
    <a href="{{ route('earthquake-events.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Record Event
    </a>
    <button type="button" class="btn btn-warning ml-2" onclick="simulateEvent()">
        <i class="fas fa-bolt mr-2"></i>Simulate Event
    </button>
    <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#exportModal">
        <i class="fas fa-download mr-2"></i>Export
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
                            Total Events
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earthquake fa-2x text-gray-300"></i>
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
                            Warning Events
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['warning'] }}</div>
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
                            Danger Events
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['danger'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire fa-2x text-gray-300"></i>
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
                            Today's Events
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
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
                <h6 class="m-0 font-weight-bold text-primary mb-0">Activity Chart (Last 30 Days)</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshChart()" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 300px;">
                    <canvas id="eventsChart"></canvas>
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
                <div class="list-group list-group-flush" id="recentEvents">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted mb-0">Loading recent events...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Events Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary mb-0">All Earthquake Events</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshEvents()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($events->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-earthquake fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No earthquake events recorded</h5>
                        <p class="text-gray-500">Start monitoring your devices for earthquake activity</p>
                        <a href="{{ route('earthquake-events.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus mr-2"></i>Record First Event
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%">ID</th>
                                    <th style="width: 20%">Device</th>
                                    <th style="width: 10%">Magnitude</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 15%">Occurred At</th>
                                    <th style="width: 20%">Location</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                <tr>
                                    <td class="align-middle">{{ $event->id }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-microchip text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $event->device->nama_device }}</div>
                                                <small class="text-muted">{{ $event->device->lokasi }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-{{ $event->magnitude_color }} p-2">
                                            {{ number_format($event->magnitude, 1) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-{{ $event->status_color }} p-2">
                                            <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} mr-1"></i>
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <div class="font-weight-bold">{{ $event->occurred_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $event->occurred_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @if($event->hasLocation())
                                            <div>
                                                <small class="text-success d-block">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    Lat: {{ number_format($event->latitude, 4) }}
                                                </small>
                                                <small class="text-success">
                                                    Lng: {{ number_format($event->longitude, 4) }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('earthquake-events.show', $event) }}"
                                               class="btn btn-info"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('earthquake-events.edit', $event) }}"
                                               class="btn btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('earthquake-events.destroy', $event) }}"
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
                <h5 class="modal-title" id="exportModalLabel">Export Earthquake Events</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportForm" action="{{ route('earthquake-events.export') }}" method="GET">
                <div class="modal-body">
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
                            <option value="all">All Events</option>
                            <option value="warning">Warning Only</option>
                            <option value="danger">Danger Only</option>
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
    .badge-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
    }

    .badge-danger {
        background: linear-gradient(45deg, #e74a3b, #d52a1e);
    }

    .badge-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
        padding: 0.75rem 1rem;
    }

    .list-group-item.warning {
        border-left-color: #ffc107;
    }

    .list-group-item.danger {
        border-left-color: #e74a3b;
    }

    .list-group-item:hover {
        transform: translateX(5px);
        background-color: #f8f9fa;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn-group .btn {
        margin-right: 2px;
        border-radius: 4px !important;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
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

    .chart-area {
        position: relative;
        min-height: 300px;
    }

    .badge {
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let eventsChart;

    // Load chart data
    function loadChartData() {
        // Gunakan route helper yang benar
        const chartUrl = "{{ route('earthquake-events.chart-data') }}";

        console.log('Loading chart data from:', chartUrl);

        fetch(chartUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Chart data received:', data);

                if (data.success) {
                    renderChart(data);
                } else {
                    throw new Error(data.message || 'Failed to load chart data');
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);

                // Fallback data untuk testing
                const fallbackData = {
                    dates: ['Jan 01', 'Jan 02', 'Jan 03', 'Jan 04', 'Jan 05', 'Jan 06', 'Jan 07'],
                    warning: [2, 3, 1, 4, 2, 3, 1],
                    danger: [1, 0, 2, 1, 0, 1, 2]
                };

                try {
                    renderChart(fallbackData);
                    console.log('Using fallback chart data');

                    // Show warning
                    Swal.fire({
                        icon: 'warning',
                        title: 'Using Sample Data',
                        text: 'Real chart data could not be loaded. Showing sample data instead.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } catch (renderError) {
                    console.error('Failed to render chart:', renderError);

                    // Show error message
                    const chartArea = document.querySelector('.chart-area');
                    if (chartArea) {
                        chartArea.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                                <p class="text-muted">Failed to load chart data</p>
                                <p class="small text-muted">${error.message}</p>
                                <button class="btn btn-sm btn-outline-primary" onclick="loadChartData()">
                                    <i class="fas fa-redo mr-1"></i>Retry
                                </button>
                            </div>
                        `;
                    }
                }
            });
    }

    // Load recent events
    function loadRecentEvents() {
        const recentUrl = "{{ route('earthquake-events.recent') }}";

        console.log('Loading recent events from:', recentUrl);

        fetch(recentUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Recent events data:', data);

                const container = document.getElementById('recentEvents');

                if (!container) {
                    console.error('Recent events container not found');
                    return;
                }

                if (!data.success || !data.events || data.events.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <p class="text-muted mb-0">No recent events in the last 24 hours</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.events.slice(0, 5).forEach(event => { // Limit to 5 events
                    const timeAgo = event.time_ago || 'Just now';
                    const statusClass = event.status === 'danger' ? 'danger' : 'warning';
                    const statusIcon = event.status === 'danger' ? 'fire' : 'exclamation-triangle';
                    const magnitudeClass = event.magnitude >= 5.0 ? 'danger' : event.magnitude >= 3.0 ? 'warning' : 'success';

                    html += `
                        <div class="list-group-item list-group-item-action ${statusClass} border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-${statusIcon} text-${statusClass} mr-2"></i>
                                    <h6 class="mb-0 font-weight-bold">${event.device.nama_device}</h6>
                                </div>
                                <small class="text-muted text-nowrap">${timeAgo}</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge badge-${magnitudeClass} mr-2">
                                    Mag: ${parseFloat(event.magnitude).toFixed(1)}
                                </span>
                                <span class="badge badge-${statusClass}">
                                    ${event.status}
                                </span>
                            </div>
                            <small class="text-muted d-block">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                ${event.device.lokasi}
                            </small>
                        </div>
                    `;
                });

                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading recent events:', error);
                const container = document.getElementById('recentEvents');
                if (container) {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                            <p class="text-muted mb-0">Cannot load recent events</p>
                            <p class="small text-muted mb-3">${error.message}</p>
                            <div class="mt-2">
                                ${getSampleRecentEvents()}
                            </div>
                        </div>
                    `;
                }
            });
    }

    // Function untuk sample data fallback
    function getSampleRecentEvents() {
        const sampleEvents = [
            {
                device: { nama_device: 'SW-420 #001', lokasi: 'Building A' },
                magnitude: 4.5,
                status: 'warning',
                time_ago: '2 hours ago'
            },
            {
                device: { nama_device: 'SW-420 #002', lokasi: 'Building B' },
                magnitude: 2.8,
                status: 'normal',
                time_ago: '5 hours ago'
            },
            {
                device: { nama_device: 'SW-420 #003', lokasi: 'Data Center' },
                magnitude: 5.2,
                status: 'danger',
                time_ago: '1 day ago'
            }
        ];

        let html = '';
        sampleEvents.forEach(event => {
            const statusClass = event.status === 'danger' ? 'danger' : event.status === 'warning' ? 'warning' : 'success';
            const statusIcon = event.status === 'danger' ? 'fire' : event.status === 'warning' ? 'exclamation-triangle' : 'check-circle';
            const magnitudeClass = event.magnitude >= 5.0 ? 'danger' : event.magnitude >= 3.0 ? 'warning' : 'success';

            html += `
                <div class="list-group-item list-group-item-action ${statusClass} border-bottom">
                    <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-${statusIcon} text-${statusClass} mr-2"></i>
                            <h6 class="mb-0 font-weight-bold">${event.device.nama_device}</h6>
                        </div>
                        <small class="text-muted text-nowrap">${event.time_ago}</small>
                    </div>
                    <div class="mb-2">
                        <span class="badge badge-${magnitudeClass} mr-2">
                            Mag: ${event.magnitude.toFixed(1)}
                        </span>
                        <span class="badge badge-${statusClass}">
                            ${event.status}
                        </span>
                    </div>
                    <small class="text-muted d-block">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        ${event.device.lokasi}
                    </small>
                </div>
            `;
        });

        return html;
    }

    // Render chart
    function renderChart(data) {
        const ctx = document.getElementById('eventsChart');

        if (!ctx) {
            console.error('Chart canvas not found');
            return;
        }

        const chartContext = ctx.getContext('2d');

        // Destroy existing chart
        if (window.eventsChartInstance) {
            window.eventsChartInstance.destroy();
        }

        window.eventsChartInstance = new Chart(chartContext, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [{
                    label: 'Warning Events',
                    data: data.warning,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffc107',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }, {
                    label: 'Danger Events',
                    data: data.danger,
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#e74a3b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10,
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 11
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 10
                            },
                            padding: 5
                        },
                        title: {
                            display: true,
                            text: 'Number of Events',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        },
                        title: {
                            display: true,
                            text: 'Date',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                },
                animation: {
                    duration: 1000
                }
            }
        });
    }

    // Simulate earthquake event
    function simulateEvent() {
        const simulateUrl = "{{ route('earthquake-events.simulate') }}";

        Swal.fire({
            title: 'Simulate Earthquake Event?',
            text: 'This will create a simulated earthquake event for testing purposes.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, simulate!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(simulateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let alertMessage = 'Earthquake event simulated successfully.';

                        if (data.alert) {
                            alertMessage += ' This event triggered an alert!';
                        }

                        Swal.fire(
                            'Simulated!',
                            alertMessage,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'Failed to simulate event.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Failed to simulate earthquake event.',
                        'error'
                    );
                });
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
            Swal.fire({
                icon: 'success',
                title: 'Refreshed!',
                text: 'Chart data updated.',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1000);
    }

    // Refresh events
    function refreshEvents() {
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
        // Load chart and recent events with delay
        setTimeout(() => {
            loadChartData();
            loadRecentEvents();
        }, 500);

        // Auto-refresh recent events every 30 seconds
        setInterval(loadRecentEvents, 30000);

        // Auto-refresh chart every 5 minutes
        setInterval(loadChartData, 300000);

        // Set default dates in export form
        const today = new Date().toISOString().split('T')[0];
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
        const oneMonthAgoStr = oneMonthAgo.toISOString().split('T')[0];

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (startDateInput) startDateInput.value = oneMonthAgoStr;
        if (endDateInput) endDateInput.value = today;

        // Test routes
        console.log('Chart URL:', "{{ route('earthquake-events.chart-data') }}");
        console.log('Recent URL:', "{{ route('earthquake-events.recent') }}");
        console.log('Simulate URL:', "{{ route('earthquake-events.simulate') }}");
    });
</script>
@endpush
