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
                            Total Devices</div>
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
                            Active Devices</div>
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
                            Inactive Devices</div>
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
                            Total Events</div>
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

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Devices</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshDevices()">
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
                        <table class="table table-bordered data-table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Device Name</th>
                                    <th>UUID</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Last Seen</th>
                                    <th>Events</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devices as $device)
                                <tr>
                                    <td>{{ $device->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-microchip text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $device->nama_device }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-muted" style="font-size: 0.8rem;">
                                            {{ substr($device->uuid, 0, 8) }}...
                                        </code>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                        {{ $device->lokasi }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $device->status_color }} p-2">
                                            <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i>
                                            {{ ucfirst($device->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($device->last_seen)
                                            <span class="text-muted" title="{{ $device->last_seen }}">
                                                {{ $device->last_seen->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <span class="badge badge-info mr-2" title="Total Events">
                                                {{ $device->earthquake_events_count }}
                                            </span>
                                            <span class="badge badge-warning mr-2" title="Logs">
                                                {{ $device->logs_count }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('devices.show', $device) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('devices.edit', $device) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-{{ $device->status === 'aktif' ? 'secondary' : 'success' }} btn-sm status-btn"
                                                    data-device-id="{{ $device->id }}"
                                                    data-status="{{ $device->status }}"
                                                    title="{{ $device->status === 'aktif' ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm qr-btn"
                                                    data-device-id="{{ $device->id }}"
                                                    title="QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                            <form action="{{ route('devices.destroy', $device) }}"
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

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $devices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Status Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="deviceStatusChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
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

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recently Active Devices</h6>
                <small class="text-muted">Last 24 hours</small>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($devices->where('status', 'aktif')->sortByDesc('last_seen')->take(5) as $device)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-microchip text-primary mr-2"></i>
                                    {{ $device->nama_device }}
                                </h6>
                                <small class="text-success">
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    Online
                                </small>
                            </div>
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                {{ $device->lokasi }}
                            </p>
                            <small class="text-muted">
                                Last seen: {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'Never' }}
                            </small>
                        </div>
                    @endforeach

                    @if($devices->where('status', 'aktif')->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-wifi-slash fa-2x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No active devices found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">Device QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Generating QR Code...</p>
                </div>
                <div id="qrCodeInfo" class="mt-3" style="display: none;">
                    <h6>Device Information</h6>
                    <p class="mb-1"><strong>Name:</strong> <span id="qrDeviceName"></span></p>
                    <p class="mb-1"><strong>UUID:</strong> <span id="qrDeviceUuid" class="text-muted small"></span></p>
                    <p class="mb-0"><strong>Location:</strong> <span id="qrDeviceLocation"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadQrBtn" style="display: none;">
                    <i class="fas fa-download mr-2"></i>Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-success { background: linear-gradient(45deg, #28a745, #20c997); }
    .badge-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }
    .badge-secondary { background: linear-gradient(45deg, #6c757d, #495057); }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        border-left-color: #4e73df;
        transform: translateX(5px);
    }

    .btn-group .btn {
        margin-right: 2px;
        border-radius: 4px !important;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
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

        // QR Code generation
        const qrButtons = document.querySelectorAll('.qr-btn');
        qrButtons.forEach(button => {
            button.addEventListener('click', function() {
                const deviceId = this.dataset.deviceId;
                generateQRCode(deviceId);
            });
        });
    });

    function generateQRCode(deviceId) {
        $('#qrCodeModal').modal('show');

        fetch(`/devices/${deviceId}/qr-code`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simulate QR code generation
                    setTimeout(() => {
                        const qrContainer = document.getElementById('qrCodeContainer');
                        qrContainer.innerHTML = `
                            <div class="qr-code-placeholder mb-3">
                                <i class="fas fa-qrcode fa-4x text-primary"></i>
                            </div>
                            <p class="text-muted">Scan to view device details</p>
                        `;

                        // Parse QR data for display
                        const qrData = JSON.parse(data.qr_data);
                        document.getElementById('qrDeviceName').textContent = qrData.name;
                        document.getElementById('qrDeviceUuid').textContent = qrData.uuid;
                        document.getElementById('qrDeviceLocation').textContent = qrData.location;

                        document.getElementById('qrCodeInfo').style.display = 'block';
                        document.getElementById('downloadQrBtn').style.display = 'block';
                    }, 1000);
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to generate QR code.', 'error');
            });
    }

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
