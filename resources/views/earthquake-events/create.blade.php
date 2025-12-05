@extends('layouts.app')

@section('title', 'Record Earthquake Event - EQMonitor')
@section('page-title', 'Record New Earthquake Event')

@section('action-button')
    <a href="{{ route('earthquake-events.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Events
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Event Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('earthquake-events.store') }}" method="POST" id="eventForm">
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
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Select the device that detected the earthquake
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="magnitude" class="form-label required">
                                <i class="fas fa-chart-line mr-2"></i>Magnitude
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('magnitude') is-invalid @enderror"
                                       id="magnitude"
                                       name="magnitude"
                                       value="{{ old('magnitude') }}"
                                       step="0.1"
                                       min="0"
                                       max="10"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Richter Scale</span>
                                </div>
                            </div>
                            @error('magnitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter magnitude value (0.0 - 10.0)
                            </small>

                            <!-- Magnitude Scale Guide -->
                            <div class="mt-2">
                                <div class="magnitude-scale">
                                    <div class="scale-item" data-magnitude="2.0">
                                        <div class="scale-bar bg-success" style="width: 20%"></div>
                                        <small>2.0-2.9: Minor</small>
                                    </div>
                                    <div class="scale-item" data-magnitude="3.0">
                                        <div class="scale-bar bg-warning" style="width: 30%"></div>
                                        <small>3.0-3.9: Light</small>
                                    </div>
                                    <div class="scale-item" data-magnitude="4.0">
                                        <div class="scale-bar bg-warning" style="width: 40%"></div>
                                        <small>4.0-4.9: Moderate</small>
                                    </div>
                                    <div class="scale-item" data-magnitude="5.0">
                                        <div class="scale-bar bg-danger" style="width: 50%"></div>
                                        <small>5.0-5.9: Strong</small>
                                    </div>
                                    <div class="scale-item" data-magnitude="6.0">
                                        <div class="scale-bar bg-danger" style="width: 60%"></div>
                                        <small>6.0-6.9: Major</small>
                                    </div>
                                    <div class="scale-item" data-magnitude="7.0">
                                        <div class="scale-bar bg-danger" style="width: 70%"></div>
                                        <small>7.0+: Great</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="occurred_at" class="form-label required">
                                <i class="fas fa-calendar-alt mr-2"></i>Occurred At
                            </label>
                            <input type="datetime-local"
                                   class="form-control @error('occurred_at') is-invalid @enderror"
                                   id="occurred_at"
                                   name="occurred_at"
                                   value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Date and time when earthquake occurred
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status_preview" class="form-label">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Status Preview
                            </label>
                            <div id="statusPreview" class="form-control bg-light">
                                <span class="badge badge-secondary">Enter magnitude to see status</span>
                            </div>
                            <small class="form-text text-muted">
                                Status determined automatically based on thresholds
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-globe-americas mr-2"></i>Latitude
                            </label>
                            <input type="number"
                                   class="form-control @error('latitude') is-invalid @enderror"
                                   id="latitude"
                                   name="latitude"
                                   value="{{ old('latitude') }}"
                                   step="0.000001"
                                   min="-90"
                                   max="90">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Optional: -90 to 90
                            </small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-globe-americas mr-2"></i>Longitude
                            </label>
                            <input type="number"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude') }}"
                                   step="0.000001"
                                   min="-180"
                                   max="180">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Optional: -180 to 180
                            </small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="depth" class="form-label">
                                <i class="fas fa-mountain mr-2"></i>Depth (km)
                            </label>
                            <input type="number"
                                   class="form-control @error('depth') is-invalid @enderror"
                                   id="depth"
                                   name="depth"
                                   value="{{ old('depth') }}"
                                   step="0.1"
                                   min="0">
                            @error('depth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Optional: Depth in kilometers
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-sticky-note mr-2"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Additional details about the earthquake event...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Optional: Additional information, effects, or observations
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Record Event
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="getCurrentLocation()">
                                <i class="fas fa-map-marker-alt mr-2"></i>Use Current Location
                            </button>
                            <a href="{{ route('earthquake-events.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Thresholds Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Threshold Settings</h6>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Earthquake events are automatically classified based on these thresholds:
                </p>
                <div class="list-group">
                    @foreach($thresholds as $threshold)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <span class="badge badge-{{ $threshold->description == 'warning' ? 'warning' : 'danger' }}">
                                        {{ ucfirst($threshold->description) }}
                                    </span>
                                </h6>
                                <strong>≥ {{ $threshold->min_value }}</strong>
                            </div>
                            <p class="mb-1 small">
                                Magnitude {{ $threshold->min_value }} or higher
                            </p>
                        </div>
                    @endforeach
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <span class="badge badge-success">Normal</span>
                            </h6>
                            <strong>&lt; {{ $thresholds->first()->min_value ?? 3.0 }}</strong>
                        </div>
                        <p class="mb-1 small">
                            Below warning threshold
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Preview -->
        <div class="card shadow" id="eventPreview" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Event Preview</h6>
            </div>
            <div class="card-body">
                <h5 id="previewMagnitude">Magnitude: 0.0</h5>
                <div class="mb-3" id="previewStatus">
                    <span class="badge badge-secondary">Status</span>
                </div>
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1"><strong>Device:</strong></p>
                        <p class="mb-1"><strong>Location:</strong></p>
                        <p class="mb-1"><strong>Time:</strong></p>
                    </div>
                    <div class="col-6 text-right">
                        <p class="mb-1" id="previewDevice">-</p>
                        <p class="mb-1" id="previewLocation">-</p>
                        <p class="mb-1" id="previewTime">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recording Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Accuracy:</strong> Use precise measurements
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning mr-2"></i>
                        <strong>Timing:</strong> Record exact occurrence time
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map text-info mr-2"></i>
                        <strong>Location:</strong> Include coordinates if available
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-sticky-note text-primary mr-2"></i>
                        <strong>Details:</strong> Add descriptive information
                    </li>
                    <li>
                        <i class="fas fa-bell text-danger mr-2"></i>
                        <strong>Alerts:</strong> System will notify based on magnitude
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .magnitude-scale {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 10px;
    }

    .scale-item {
        margin-bottom: 5px;
    }

    .scale-bar {
        height: 4px;
        border-radius: 2px;
        margin-bottom: 2px;
    }

    .scale-item small {
        font-size: 0.8rem;
        color: #6c757d;
    }

    #eventPreview .card-body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0 0 0.35rem 0.35rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Update status preview based on magnitude
    document.getElementById('magnitude').addEventListener('input', function() {
        updateStatusPreview();
        updateEventPreview();
    });

    document.getElementById('device_id').addEventListener('change', function() {
        updateEventPreview();
    });

    document.getElementById('occurred_at').addEventListener('change', function() {
        updateEventPreview();
    });

    function updateStatusPreview() {
        const magnitude = parseFloat(document.getElementById('magnitude').value) || 0;
        const statusPreview = document.getElementById('statusPreview');

        // Determine status based on thresholds
        let status = 'normal';
        let statusClass = 'secondary';

        @foreach($thresholds as $threshold)
            if (magnitude >= {{ $threshold->min_value }}) {
                status = '{{ $threshold->description }}';
                statusClass = '{{ $threshold->description == 'warning' ? 'warning' : 'danger' }}';
            }
        @endforeach

        statusPreview.innerHTML = `<span class="badge badge-${statusClass} p-2">${status.toUpperCase()}</span>`;
    }

    function updateEventPreview() {
        const magnitude = parseFloat(document.getElementById('magnitude').value) || 0;
        const deviceSelect = document.getElementById('device_id');
        const deviceText = deviceSelect.options[deviceSelect.selectedIndex]?.text || '-';
        const occurredAt = document.getElementById('occurred_at').value;

        // Update preview
        document.getElementById('previewMagnitude').textContent = `Magnitude: ${magnitude.toFixed(1)}`;
        document.getElementById('previewDevice').textContent = deviceText.split(' - ')[0] || '-';
        document.getElementById('previewLocation').textContent = deviceText.split(' - ')[1] || '-';
        document.getElementById('previewTime').textContent = occurredAt ? new Date(occurredAt).toLocaleString() : '-';

        // Update status in preview
        updateStatusPreview();
        const statusBadge = document.getElementById('statusPreview').innerHTML;
        document.getElementById('previewStatus').innerHTML = statusBadge;

        // Show/hide preview
        if (magnitude > 0) {
            document.getElementById('eventPreview').style.display = 'block';
        }
    }

    function getCurrentLocation() {
        if (!navigator.geolocation) {
            Swal.fire('Error!', 'Geolocation is not supported by your browser.', 'error');
            return;
        }

        Swal.fire({
            title: 'Getting location...',
            text: 'Please allow location access',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);

                Swal.fire(
                    'Location Updated!',
                    'Current coordinates have been filled.',
                    'success'
                );
            },
            function(error) {
                Swal.fire(
                    'Error!',
                    'Unable to retrieve your location. Please enter manually.',
                    'error'
                );
            }
        );
    }

    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        const magnitude = document.getElementById('magnitude').value;
        const deviceId = document.getElementById('device_id').value;
        const occurredAt = document.getElementById('occurred_at').value;

        if (!magnitude || !deviceId || !occurredAt) {
            e.preventDefault();
            Swal.fire('Error!', 'Please fill all required fields.', 'error');
            return;
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Recording...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });

    // Initialize preview on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStatusPreview();
    });
</script>
@endpush
