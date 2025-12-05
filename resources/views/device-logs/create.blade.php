@extends('layouts.app')

@section('title', 'Create Device Log - EQMonitor')
@section('page-title', 'Create Device Log')

@section('action-button')
    <a href="{{ route('device-logs.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Logs
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create Device Log</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('device-logs.store') }}" method="POST" id="logForm">
                    @csrf

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
                                    <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                        {{ $device->nama_device }} - {{ $device->lokasi }}
                                        @if($device->status === 'nonaktif')
                                            (Inactive)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Select the device for this log entry
                            </small>
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
                                <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Device connection status
                            </small>
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
                                       value="{{ old('magnitude') }}"
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
                            <small class="form-text text-muted">
                                Optional: Earthquake magnitude (0.0 - 10.0)
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="logged_at" class="form-label required">
                                <i class="fas fa-calendar-alt mr-2"></i>Logged At
                            </label>
                            <input type="datetime-local"
                                   class="form-control @error('logged_at') is-invalid @enderror"
                                   id="logged_at"
                                   name="logged_at"
                                   value="{{ old('logged_at', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('logged_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Date and time when the log was recorded
                            </small>
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
                                      rows="3"
                                      placeholder="Additional notes about this log entry...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Optional: Additional information about this log
                            </small>
                        </div>
                    </div>

                    <!-- Log Preview -->
                    <div class="card border-left-info mb-4" id="logPreview" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title text-info">
                                <i class="fas fa-eye mr-2"></i>Log Preview
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Device:</strong> <span id="previewDevice">-</span></p>
                                    <p><strong>Status:</strong> <span id="previewStatus">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Magnitude:</strong> <span id="previewMagnitude">-</span></p>
                                    <p><strong>Logged At:</strong> <span id="previewTime">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Create Log
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="previewLog()">
                                <i class="fas fa-eye mr-2"></i>Preview
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="useCurrentTime()">
                                <i class="fas fa-clock mr-2"></i>Use Current Time
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
        <!-- Quick Tips -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Creating Device Logs</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        <strong>Device Selection:</strong> Choose the device that generated this log.
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-wifi text-success mr-2"></i>
                        <strong>Online Status:</strong> Device is connected and sending data.
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-wifi-slash text-danger mr-2"></i>
                        <strong>Offline Status:</strong> Device is not connected or not responding.
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-chart-line text-warning mr-2"></i>
                        <strong>Magnitude:</strong> Enter if vibration/earthquake data is available.
                    </li>
                    <li>
                        <i class="fas fa-clock text-info mr-2"></i>
                        <strong>Timestamp:</strong> Accurate time is important for monitoring.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Recent Device Activity -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Device Activity</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($devices->where('status', 'aktif')->take(5) as $device)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $device->nama_device }}</h6>
                                <small class="text-muted">
                                    @if($device->last_seen)
                                        {{ $device->last_seen->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </small>
                            </div>
                            <p class="mb-1 small">{{ $device->lokasi }}</p>
                            <small>
                                <span class="badge badge-{{ $device->status_color }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update preview when form changes
    document.getElementById('device_id').addEventListener('change', updatePreview);
    document.getElementById('status').addEventListener('change', updatePreview);
    document.getElementById('magnitude').addEventListener('input', updatePreview);
    document.getElementById('logged_at').addEventListener('change', updatePreview);

    function updatePreview() {
        const deviceSelect = document.getElementById('device_id');
        const deviceText = deviceSelect.options[deviceSelect.selectedIndex]?.text || '-';
        const status = document.getElementById('status').value;
        const magnitude = document.getElementById('magnitude').value;
        const loggedAt = document.getElementById('logged_at').value;

        // Update preview elements
        document.getElementById('previewDevice').textContent = deviceText;
        document.getElementById('previewStatus').textContent = status ? status.charAt(0).toUpperCase() + status.slice(1) : '-';
        document.getElementById('previewMagnitude').textContent = magnitude ? parseFloat(magnitude).toFixed(2) : 'N/A';
        document.getElementById('previewTime').textContent = loggedAt ? new Date(loggedAt).toLocaleString() : '-';

        // Show/hide preview
        if (deviceSelect.value && status && loggedAt) {
            document.getElementById('logPreview').style.display = 'block';
        } else {
            document.getElementById('logPreview').style.display = 'none';
        }
    }

    function previewLog() {
        const deviceId = document.getElementById('device_id').value;
        const status = document.getElementById('status').value;
        const loggedAt = document.getElementById('logged_at').value;

        if (!deviceId || !status || !loggedAt) {
            Swal.fire('Error!', 'Please fill all required fields first.', 'error');
            return;
        }

        updatePreview();

        // Scroll to preview
        document.getElementById('logPreview').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function useCurrentTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        document.getElementById('logged_at').value = `${year}-${month}-${day}T${hours}:${minutes}`;
        updatePreview();

        Swal.fire('Success!', 'Current time has been set.', 'success');
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });

    // Initialize preview on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
    });
</script>
@endpush
