@extends('layouts.app')

@section('title', 'Edit Earthquake Event - EQMonitor')
@section('page-title', 'Edit Earthquake Event')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('earthquake-events.show', $earthquakeEvent) }}" class="btn btn-info">
            <i class="fas fa-eye mr-2"></i>View Details
        </a>
        <a href="{{ route('earthquake-events.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Earthquake Event</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('earthquake-events.update', $earthquakeEvent) }}" method="POST" id="eventForm">
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
                                    <option value="{{ $device->id }}" {{ old('device_id', $earthquakeEvent->device_id) == $device->id ? 'selected' : '' }}>
                                        {{ $device->nama_device }} - {{ $device->lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                       value="{{ old('magnitude', $earthquakeEvent->magnitude) }}"
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
                                   value="{{ old('occurred_at', $earthquakeEvent->occurred_at->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status_preview" class="form-label">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Current Status
                            </label>
                            <div class="form-control bg-light">
                                <span class="badge badge-{{ $earthquakeEvent->status_color }}">
                                    {{ strtoupper($earthquakeEvent->status) }}
                                </span>
                                <small class="text-muted ml-2">(Auto-calculated based on magnitude)</small>
                            </div>
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
                                   value="{{ old('latitude', $earthquakeEvent->latitude) }}"
                                   step="0.000001"
                                   min="-90"
                                   max="90">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-globe-americas mr-2"></i>Longitude
                            </label>
                            <input type="number"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude', $earthquakeEvent->longitude) }}"
                                   step="0.000001"
                                   min="-180"
                                   max="180">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="depth" class="form-label">
                                <i class="fas fa-mountain mr-2"></i>Depth (km)
                            </label>
                            <input type="number"
                                   class="form-control @error('depth') is-invalid @enderror"
                                   id="depth"
                                   name="depth"
                                   value="{{ old('depth', $earthquakeEvent->depth) }}"
                                   step="0.1"
                                   min="0">
                            @error('depth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                      rows="4">{{ old('description', $earthquakeEvent->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar mr-2"></i>Created At
                                </label>
                                <input type="text"
                                       class="form-control bg-light"
                                       value="{{ $earthquakeEvent->created_at->format('F d, Y H:i:s') }}"
                                       readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock mr-2"></i>Last Updated
                                </label>
                                <input type="text"
                                       class="form-control bg-light"
                                       value="{{ $earthquakeEvent->updated_at->format('F d, Y H:i:s') }}"
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Update Event
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                <i class="fas fa-undo mr-2"></i>Reset Changes
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
        <!-- Event Summary -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Event Summary</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="magnitude-display-large mb-3">
                        <span class="badge badge-{{ $earthquakeEvent->magnitude_color }} p-4" style="font-size: 2rem;">
                            {{ number_format($earthquakeEvent->magnitude, 1) }}
                        </span>
                    </div>
                    <h5>Current Status</h5>
                    <span class="badge badge-{{ $earthquakeEvent->status_color }} p-2">
                        {{ strtoupper($earthquakeEvent->status) }}
                    </span>
                </div>

                <hr>

                <div class="event-details">
                    <p><strong>Device:</strong> {{ $earthquakeEvent->device->nama_device }}</p>
                    <p><strong>Location:</strong> {{ $earthquakeEvent->device->lokasi }}</p>
                    <p><strong>Occurred:</strong> {{ $earthquakeEvent->formatted_occurred_at }}</p>

                    @if($earthquakeEvent->hasLocation())
                        <p><strong>Coordinates:</strong>
                            {{ number_format($earthquakeEvent->latitude, 4) }},
                            {{ number_format($earthquakeEvent->longitude, 4) }}
                        </p>
                        <p><strong>Depth:</strong> {{ $earthquakeEvent->depth }} km</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Threshold Information -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Threshold Settings</h6>
            </div>
            <div class="card-body">
                <p>Event status is automatically determined:</p>
                <ul class="list-unstyled">
                    @foreach($thresholds as $threshold)
                        <li class="mb-2">
                            <i class="fas fa-circle text-{{ $threshold->description == 'warning' ? 'warning' : 'danger' }} mr-2"></i>
                            <strong>≥ {{ $threshold->min_value }}:</strong> {{ ucfirst($threshold->description) }}
                        </li>
                    @endforeach
                    <li>
                        <i class="fas fa-circle text-success mr-2"></i>
                        <strong>&lt; {{ $thresholds->first()->min_value ?? 3.0 }}:</strong> Normal
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .magnitude-display-large .badge {
        border-radius: 12px;
        min-width: 100px;
    }

    .event-details p {
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .event-details p:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Reset form to original values
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
                document.getElementById('eventForm').reset();
                Swal.fire(
                    'Reset!',
                    'All changes have been reverted.',
                    'success'
                );
            }
        });
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });
</script>
@endpush
