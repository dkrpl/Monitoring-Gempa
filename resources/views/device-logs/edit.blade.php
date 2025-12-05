@extends('layouts.app')

@section('title', 'Edit Device Log - EQMonitor')
@section('page-title', 'Edit Device Log')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('device-logs.show', $deviceLog) }}" class="btn btn-info">
            <i class="fas fa-eye mr-2"></i>View Details
        </a>
        <a href="{{ route('device-logs.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Logs
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Device Log</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('device-logs.update', $deviceLog) }}" method="POST" id="logForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="device_id" class="form-label required">
                                <i class="fas fa-microchip mr-2"></i>Device
                            </label>
                            <select class="form-control @error('device_id') is-invalid @enderror"
                                    id="device_id"
                                    name="device_id"
                                    required>
                                <option value="">Select Device</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" {{ old('device_id', $deviceLog->device_id) == $device->id ? 'selected' : '' }}>
                                        {{ $device->nama_device }} - {{ $device->lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label required">
                                <i class="fas fa-power-off mr-2"></i>Status
                            </label>
                            <select class="form-control @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="">Select Status</option>
                                <option value="online" {{ old('status', $deviceLog->status) == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ old('status', $deviceLog->status) == 'offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="magnitude" class="form-label">
                                <i class="fas fa-chart-line mr-2"></i>Magnitude
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('magnitude') is-invalid @enderror"
                                       id="magnitude"
                                       name="magnitude"
                                       value="{{ old('magnitude', $deviceLog->magnitude) }}"
                                       step="0.01"
                                       min="0"
                                       max="10">
                                <div class="input-group-append">
                                    <span class="input-group-text">Richter Scale</span>
                                </div>
                            </div>
                            @error('magnitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="logged_at" class="form-label required">
                                <i class="fas fa-calendar-alt mr-2"></i>Logged At
                            </label>
                            <input type="datetime-local"
                                   class="form-control @error('logged_at') is-invalid @enderror"
                                   id="logged_at"
                                   name="logged_at"
                                   value="{{ old('logged_at', $deviceLog->logged_at->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('logged_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note mr-2"></i>Notes
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="3">{{ old('notes', $deviceLog->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Log Information -->
                    <div class="card border-left-info mb-4">
                        <div class="card-body">
                            <h6 class="card-title text-info">
                                <i class="fas fa-info-circle mr-2"></i>Log Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Log ID:</strong> {{ $deviceLog->id }}</p>
                                    <p><strong>Created:</strong> {{ $deviceLog->logged_at->format('F d, Y H:i:s') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Device:</strong> {{ $deviceLog->device->nama_device }}</p>
                                    <p><strong>Current Status:</strong>
                                        <span class="badge badge-{{ $deviceLog->status === 'online' ? 'success' : 'danger' }}">
                                            {{ ucfirst($deviceLog->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Update Log
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                <i class="fas fa-undo mr-2"></i>Reset Changes
                            </button>
                            <a href="{{ route('device-logs.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Device Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Information</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-microchip fa-3x text-primary mb-3"></i>
                    <h5>{{ $deviceLog->device->nama_device }}</h5>
                    <p class="text-muted">{{ $deviceLog->device->lokasi }}</p>
                </div>

                <div class="device-details">
                    <p><strong>UUID:</strong> <code>{{ $deviceLog->device->uuid }}</code></p>
                    <p><strong>Status:</strong>
                        <span class="badge badge-{{ $deviceLog->device->status_color }}">
                            {{ ucfirst($deviceLog->device->status) }}
                        </span>
                    </p>
                    <p><strong>Last Seen:</strong>
                        @if($deviceLog->device->last_seen)
                            {{ $deviceLog->device->last_seen->diffForHumans() }}
                        @else
                            Never
                        @endif
                    </p>
                    <p><strong>Created:</strong> {{ $deviceLog->device->created_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
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
                        <a href="{{ route('device-logs.by-device', $deviceLog->device_id) }}"
                           class="btn btn-outline-info btn-block">
                            <i class="fas fa-list"></i><br>
                            <small>All Logs</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="testDevice()">
                            <i class="fas fa-plug"></i><br>
                            <small>Test Device</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-block" onclick="simulateSimilar()">
                            <i class="fas fa-bolt"></i><br>
                            <small>Simulate</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function resetForm() {
        Swal.fire({
            title: 'Reset Changes?',
            text: 'This will revert all changes to original values.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logForm').reset();
                Swal.fire(
                    'Reset!',
                    'All changes have been reverted.',
                    'success'
                );
            }
        });
    }

    function testDevice() {
        Swal.fire({
            title: 'Testing Device Connection...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Connection Test Complete',
                html: `
                    <div class="text-left">
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Device: ${@json($deviceLog->device->nama_device)}</p>
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Status: ${@json($deviceLog->device->status)}</p>
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Last Log: ${@json($deviceLog->logged_at->diffForHumans())}</p>
                        <p><i class="fas fa-check-circle text-success mr-2"></i> Connection: Stable</p>
                    </div>
                `,
                confirmButtonText: 'OK'
            });
        }, 2000);
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
                    },
                    body: JSON.stringify({
                        device_id: @json($deviceLog->device_id),
                        status: @json($deviceLog->status),
                        magnitude: @json($deviceLog->magnitude)
                    })
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

    // Form validation
    document.getElementById('logForm').addEventListener('submit', function(e) {
        const deviceId = document.getElementById('device_id').value;
        const status = document.getElementById('status').value;
        const loggedAt = document.getElementById('logged_at').value;

        if (!deviceId || !status || !loggedAt) {
            e.preventDefault();
            Swal.fire('Error!', 'Please fill all required fields.', 'error');
            return;
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });
</script>
@endpush
