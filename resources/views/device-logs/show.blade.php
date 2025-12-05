@extends('layouts.app')

@section('title', 'Device Log Details - EQMonitor')
@section('page-title', 'Device Log Details')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('device-logs.by-device', $deviceLog->device_id) }}" class="btn btn-primary">
            <i class="fas fa-list mr-2"></i>All Device Logs
        </a>
        <a href="{{ route('device-logs.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>All Logs
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Log Details -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Log Information</h6>
                <span class="badge badge-{{ $deviceLog->status === 'online' ? 'success' : 'danger' }} p-2">
                    <i class="fas fa-{{ $deviceLog->status === 'online' ? 'wifi' : 'wifi-slash' }} mr-1"></i>
                    {{ strtoupper($deviceLog->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Device</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-microchip mr-2"></i>
                                <a href="{{ route('devices.show', $deviceLog->device_id) }}">
                                    {{ $deviceLog->device->nama_device }}
                                </a>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $deviceLog->device->lokasi }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Log ID</label>
                            <p class="form-control-plaintext">
                                <code>LOG-{{ str_pad($deviceLog->id, 8, '0', STR_PAD_LEFT) }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Magnitude</label>
                            <div class="magnitude-display">
                                @if($deviceLog->magnitude !== null)
                                    <span class="badge badge-{{ $deviceLog->magnitude >= 5.0 ? 'danger' : ($deviceLog->magnitude >= 3.0 ? 'warning' : 'success') }} p-3" style="font-size: 1.5rem;">
                                        {{ number_format($deviceLog->magnitude, 2) }}
                                    </span>
                                    <small class="text-muted ml-2">Richter Scale</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Logged At</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ $deviceLog->logged_at->format('F d, Y H:i:s') }}
                            </p>
                            <small class="text-muted">{{ $deviceLog->logged_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Device Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-{{ $deviceLog->device->status_color }}">
                                    {{ ucfirst($deviceLog->device->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Last Seen</label>
                            <p class="form-control-plaintext">
                                @if($deviceLog->device->last_seen)
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $deviceLog->device->last_seen->format('M d, Y H:i:s') }}
                                    <br>
                                    <small class="text-muted">{{ $deviceLog->device->last_seen->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Raw Data Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-left-info">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-database mr-2"></i>Raw Data Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Log ID:</strong> {{ $deviceLog->id }}</p>
                                        <p><strong>Device ID:</strong> {{ $deviceLog->device_id }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Created:</strong> {{ $deviceLog->logged_at->format('Y-m-d H:i:s') }}</p>
                                        <p><strong>Timestamp:</strong> {{ $deviceLog->logged_at->getTimestamp() }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Status Code:</strong> {{ $deviceLog->status === 'online' ? '1' : '0' }}</p>
                                        <p><strong>Magnitude Raw:</strong> {{ $deviceLog->magnitude ?? 'null' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="text-primary font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $deviceStats['total_logs'] }}
                                </div>
                                <div class="text-muted">Total Logs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <div class="text-success font-weight-bold" style="font-size: 1.5rem;">
                                    {{ number_format($deviceStats['avg_magnitude'], 2) }}
                                </div>
                                <div class="text-muted">Avg Magnitude</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <div class="text-info font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $deviceStats['today_logs'] }}
                                </div>
                                <div class="text-muted">Today's Logs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <div class="text-warning font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $deviceLog->device->status === 'aktif' ? 'Active' : 'Inactive' }}
                                </div>
                                <div class="text-muted">Device Status</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('devices.show', $deviceLog->device_id) }}"
                           class="btn btn-outline-primary btn-block">
                            <i class="fas fa-microchip"></i><br>
                            <small>View Device</small>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <button type="button" class="btn btn-outline-info btn-block" onclick="viewRawJson()">
                            <i class="fas fa-code"></i><br>
                            <small>View JSON</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-block" onclick="simulateSimilar()">
                            <i class="fas fa-bolt"></i><br>
                            <small>Simulate Similar</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="testDeviceConnection()">
                            <i class="fas fa-plug"></i><br>
                            <small>Test Connection</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Logs -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Similar Logs (Same Day)</h6>
            </div>
            <div class="card-body">
                @if($similarLogs->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <p class="text-muted">No similar logs found</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($similarLogs as $log)
                            <a href="{{ route('device-logs.show', $log) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <span class="badge badge-{{ $log->status === 'online' ? 'success' : 'danger' }}">
                                            {{ $log->status }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">{{ $log->logged_at->format('H:i') }}</small>
                                </div>
                                <p class="mb-1">
                                    @if($log->magnitude !== null)
                                        <span class="badge badge-{{ $log->magnitude >= 5.0 ? 'danger' : ($log->magnitude >= 3.0 ? 'warning' : 'success') }}">
                                            {{ number_format($log->magnitude, 2) }}
                                        </span>
                                    @endif
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
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
                    <strong>Warning:</strong> This action cannot be undone.
                </p>

                <form action="{{ route('device-logs.destroy', $deviceLog) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-block delete-btn">
                        <i class="fas fa-trash mr-2"></i>Delete Log
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewRawJson() {
        const logData = {
            id: @json($deviceLog->id),
            device_id: @json($deviceLog->device_id),
            status: @json($deviceLog->status),
            magnitude: @json($deviceLog->magnitude),
            logged_at: @json($deviceLog->logged_at),
            device: {
                id: @json($deviceLog->device->id),
                name: @json($deviceLog->device->nama_device),
                location: @json($deviceLog->device->lokasi),
                status: @json($deviceLog->device->status)
            }
        };

        Swal.fire({
            title: 'Raw JSON Data',
            html: `<pre class="text-left">${JSON.stringify(logData, null, 2)}</pre>`,
            icon: 'info',
            width: '800px',
            confirmButtonText: 'Close'
        });
    }

    function simulateSimilar() {
        Swal.fire({
            title: 'Simulate Similar Log?',
            text: 'This will create a similar log entry for testing.',
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
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Simulated!',
                            'Similar log has been created.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to simulate log.', 'error');
                });
            }
        });
    }

    function testDeviceConnection() {
        Swal.fire({
            title: 'Testing Device Connection...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate connection test
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Connection Test Complete',
                html: `
                    <div class="text-left">
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Device: ${@json($deviceLog->device->nama_device)}</p>
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Status: ${@json($deviceLog->device->status)}</p>
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Last Seen: ${@json($deviceLog->device->last_seen ? $deviceLog->device->last_seen->diffForHumans() : 'Never')}</p>
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Connection: Stable</p>
                    </div>
                `,
                confirmButtonText: 'OK'
            });
        }, 2000);
    }
</script>
@endpush
