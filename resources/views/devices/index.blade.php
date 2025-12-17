@extends('layouts.app')

@section('title', 'Device Management - EQMonitor')
@section('page-title', 'Device Management')

@section('action-button')
<div class="btn-group" role="group">
    <a href="{{ route('devices.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Add New Device
    </a>
    <button type="button" class="btn btn-info ml-2" onclick="checkOfflineDevices()">
        <i class="fas fa-wifi mr-2"></i>Check Offline
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
                            Total Devices
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $devices->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-microchip fa-2x text-gray-300"></i>
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
                            Active Devices
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $devices->where('status', 'aktif')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Inactive Devices
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $devices->where('status', 'nonaktif')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            Total Events
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $devices->sum('earthquake_events_count') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earthquake fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary mb-0">All Devices</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshDevices()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($devices->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-microchip fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No devices found</h5>
                        <p class="text-gray-500">Get started by adding your first SW-420 sensor device</p>
                        <a href="{{ route('devices.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus mr-2"></i>Add Device
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%">ID</th>
                                    <th style="width: 20%">Device Name</th>
                                    <th style="width: 15%">UUID</th>
                                    <th style="width: 15%">Location</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 15%">Last Seen</th>
                                    <th style="width: 10%">Events</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devices as $device)
                                <tr>
                                    <td class="align-middle">{{ $device->id }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-microchip text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $device->nama_device }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <code class="text-muted" style="font-size: 0.8rem;">
                                            {{ substr($device->uuid, 0, 8) }}...
                                        </code>
                                    </td>
                                    <td class="align-middle">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                        {{ $device->lokasi }}
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-{{ $device->status_color }} p-2">
                                            <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i>
                                            {{ ucfirst($device->status) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($device->last_seen)
                                            <span class="text-muted" title="{{ $device->last_seen }}">
                                                {{ $device->last_seen->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center">
                                            <span class="badge badge-info mr-1" title="Total Events">
                                                {{ $device->earthquake_events_count }}
                                            </span>
                                            <span class="badge badge-warning" title="Logs">
                                                {{ $device->logs_count }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('devices.show', $device) }}"
                                               class="btn btn-info"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('devices.edit', $device) }}"
                                               class="btn btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-{{ $device->status === 'aktif' ? 'secondary' : 'success' }} status-btn"
                                                    data-device-id="{{ $device->id }}"
                                                    data-status="{{ $device->status }}"
                                                    title="{{ $device->status === 'aktif' ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <form action="{{ route('devices.destroy', $device) }}"
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

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $devices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row">
    <!-- Device Status Chart -->
    <div class="col-lg-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary mb-0">Device Status Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-2" style="height: 200px;">
                    <canvas id="deviceStatusChart"></canvas>
                </div>
                <div class="mt-3 text-center small">
                    <span class="mr-3">
                        <i class="fas fa-circle text-success"></i> Active
                    </span>
                    <span>
                        <i class="fas fa-circle text-warning"></i> Inactive
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Active Devices -->
    <div class="col-lg-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary mb-0">Recently Active Devices</h6>
                <small class="text-muted">Last 24 hours</small>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                @if($devices->where('status', 'aktif')->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-wifi-slash fa-2x text-gray-300 mb-3"></i>
                        <p class="text-gray-500 mb-0">No active devices found</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($devices->where('status', 'aktif')->sortByDesc('last_seen')->take(8) as $device)
                            <div class="list-group-item list-group-item-action border-bottom py-2 px-3">
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-microchip text-primary mr-2"></i>
                                        <h6 class="mb-0 font-weight-bold">{{ $device->nama_device }}</h6>
                                    </div>
                                    <small class="text-success">
                                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                        Online
                                    </small>
                                </div>
                                <div class="mb-1">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $device->lokasi }}
                                    </small>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="far fa-clock mr-1"></i>
                                    Last seen: {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-success {
        background: linear-gradient(45deg, #28a745, #20c997);
    }

    .badge-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
    }

    .badge-secondary {
        background: linear-gradient(45deg, #6c757d, #495057);
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        border-left-color: #4e73df;
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

    .chart-pie {
        position: relative;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Device Status Chart
    document.addEventListener('DOMContentLoaded', function() {
        // Pie Chart
        const ctx = document.getElementById('deviceStatusChart').getContext('2d');
        const activeCount = {{ $devices->where('status', 'aktif')->count() }};
        const inactiveCount = {{ $devices->where('status', 'nonaktif')->count() }};

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [activeCount, inactiveCount],
                    backgroundColor: ['#28a745', '#ffc107'],
                    hoverBackgroundColor: ['#218838', '#e0a800'],
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
                cutoutPercentage: 80,
            },
        });

        // Status toggle
        const statusButtons = document.querySelectorAll('.status-btn');
        statusButtons.forEach(button => {
            button.addEventListener('click', function() {
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
        });
    });

    function checkOfflineDevices() {
        Swal.fire({
            title: 'Checking offline devices...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/devices/offline')
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.count > 0) {
                    let deviceList = '';
                    data.devices.forEach(device => {
                        deviceList += `
                            <div class="mb-2">
                                <strong>${device.nama_device}</strong><br>
                                <small class="text-muted">${device.lokasi} • Last seen: ${device.last_seen ? new Date(device.last_seen).toLocaleString() : 'Never'}</small>
                            </div>
                        `;
                    });

                    Swal.fire({
                        title: `Offline Devices (${data.count})`,
                        html: deviceList,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'All devices online!',
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

    function refreshDevices() {
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Auto-refresh device status every 30 seconds
    setInterval(() => {
        const activeRows = document.querySelectorAll('tbody tr');
        activeRows.forEach(row => {
            const lastSeenCell = row.querySelector('td:nth-child(6)');
            if (lastSeenCell) {
                const text = lastSeenCell.textContent.trim();
                if (text.includes('seconds') || text.includes('minute')) {
                    // Update time ago text
                    // In a real app, you would fetch updated data
                }
            }
        });
    }, 30000);
</script>
@endpush
