@extends('layouts.app')

@section('title', 'Add Device - EQMonitor')
@section('page-title', 'Add New Device')

@section('action-button')
    <a href="{{ route('devices.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Devices
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('devices.store') }}" method="POST" id="deviceForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_device" class="form-label required">
                                        <i class="fas fa-microchip mr-2"></i>Device Name
                                    </label>
                                    <input type="text"
                                           class="form-control @error('nama_device') is-invalid @enderror"
                                           id="nama_device"
                                           name="nama_device"
                                           value="{{ old('nama_device') }}"
                                           placeholder="e.g., SW-420 Sensor #001"
                                           required>
                                    @error('nama_device')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Give your device a descriptive name
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lokasi" class="form-label required">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Location
                                    </label>
                                    <input type="text"
                                           class="form-control @error('lokasi') is-invalid @enderror"
                                           id="lokasi"
                                           name="lokasi"
                                           value="{{ old('lokasi') }}"
                                           placeholder="e.g., Building A, 3rd Floor"
                                           required>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Physical location of the device
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label required">
                                        <i class="fas fa-power-off mr-2"></i>Status
                                    </label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        <option value="">Select Status</option>
                                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Active</option>
                                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Set device activation status
                                    </small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-fingerprint mr-2"></i>Device UUID
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control bg-light"
                                               value="Will be generated automatically"
                                               readonly>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateUuid()">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Unique identifier for the device
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label">
                                            <i class="fas fa-sticky-note mr-2"></i>Additional Notes
                                        </label>
                                        <textarea class="form-control"
                                                  id="notes"
                                                  name="notes"
                                                  rows="3"
                                                  placeholder="Any additional information about this device...">{{ old('notes') }}</textarea>
                                        <small class="form-text text-muted">
                                            Optional: Installation date, maintenance notes, etc.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Create Device
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="previewDevice()">
                                        <i class="fas fa-eye mr-2"></i>Preview
                                    </button>
                                    <a href="{{ route('devices.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-info-circle mr-2"></i>Device Information
                                    </h5>
                                    <p class="card-text">
                                        <strong>SW-420 Vibration Sensor</strong><br>
                                        Add a new earthquake monitoring device to your network.
                                    </p>
                                    <hr>
                                    <h6>Requirements:</h6>
                                    <ul class="small">
                                        <li>Device must be properly installed</li>
                                        <li>Network connectivity available</li>
                                        <li>Power supply secured</li>
                                        <li>Calibration completed</li>
                                    </ul>
                                    <h6>Best Practices:</h6>
                                    <ul class="small">
                                        <li>Choose descriptive names</li>
                                        <li>Specify exact location</li>
                                        <li>Test connection after setup</li>
                                        <li>Document installation details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Device Preview Card -->
        <div class="card shadow" id="devicePreview" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Preview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 id="previewName">Device Name</h5>
                        <p class="text-muted" id="previewLocation">Location</p>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Status</h6>
                                        <span class="badge badge-success" id="previewStatus">Active</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>UUID</h6>
                                        <code class="text-muted small">auto-generated</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="device-icon-placeholder">
                            <i class="fas fa-microchip fa-4x text-primary"></i>
                        </div>
                        <p class="text-muted mt-2">SW-420 Sensor</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateUuid() {
        // Generate a random UUID-like string
        const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });

        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'UUID Generated',
            text: 'Device UUID will be generated upon creation',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function previewDevice() {
        const name = document.getElementById('nama_device').value;
        const location = document.getElementById('lokasi').value;
        const status = document.getElementById('status').value;

        if (!name || !location || !status) {
            Swal.fire('Error!', 'Please fill all required fields first.', 'error');
            return;
        }

        // Update preview
        document.getElementById('previewName').textContent = name;
        document.getElementById('previewLocation').textContent = location;
        document.getElementById('previewStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);
        document.getElementById('previewStatus').className = `badge badge-${status === 'aktif' ? 'success' : 'warning'}`;

        // Show preview
        document.getElementById('devicePreview').style.display = 'block';

        // Scroll to preview
        document.getElementById('devicePreview').scrollIntoView({
            behavior: 'smooth'
        });
    }

    // Form validation
    document.getElementById('deviceForm').addEventListener('submit', function(e) {
        const name = document.getElementById('nama_device').value;
        const location = document.getElementById('lokasi').value;
        const status = document.getElementById('status').value;

        if (!name || !location || !status) {
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
</script>
@endpush
