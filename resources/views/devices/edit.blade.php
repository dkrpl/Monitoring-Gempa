@extends('layouts.app')

@section('title', 'Edit Device - EQMonitor')
@section('page-title', 'Edit Device: ' . $device->nama_device)

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('devices.show', $device) }}" class="btn btn-info">
            <i class="fas fa-eye mr-2"></i>View Details
        </a>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Devices
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Device Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('devices.update', $device) }}" method="POST" id="deviceForm">
                    @csrf
                    @method('PUT')

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
                                           value="{{ old('nama_device', $device->nama_device) }}"
                                           required>
                                    @error('nama_device')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lokasi" class="form-label required">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Location
                                    </label>
                                    <input type="text"
                                           class="form-control @error('lokasi') is-invalid @enderror"
                                           id="lokasi"
                                           name="lokasi"
                                           value="{{ old('lokasi', $device->lokasi) }}"
                                           required>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                        <option value="aktif" {{ old('status', $device->status) == 'aktif' ? 'selected' : '' }}>Active</option>
                                        <option value="nonaktif" {{ old('status', $device->status) == 'nonaktif' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-fingerprint mr-2"></i>Device UUID
                                    </label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           value="{{ $device->uuid }}"
                                           readonly>
                                    <small class="form-text text-muted">
                                        Unique identifier (cannot be changed)
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar mr-2"></i>Created At
                                    </label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           value="{{ $device->created_at->format('F d, Y H:i:s') }}"
                                           readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-clock mr-2"></i>Last Updated
                                    </label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           value="{{ $device->updated_at->format('F d, Y H:i:s') }}"
                                           readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="last_seen" class="form-label">
                                            <i class="fas fa-wifi mr-2"></i>Last Seen
                                        </label>
                                        <input type="text"
                                               class="form-control bg-light"
                                               value="{{ $device->last_seen ? $device->last_seen->format('F d, Y H:i:s') . ' (' . $device->last_seen->diffForHumans() . ')' : 'Never' }}"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Update Device
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="testConnection({{ $device->id }})">
                                        <i class="fas fa-plug mr-2"></i>Test Connection
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
                                        <i class="fas fa-info-circle mr-2"></i>Device Statistics
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-earthquake text-warning mr-2"></i>
                                            <strong>Total Events:</strong> {{ $device->earthquakeEvents()->count() }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-history text-info mr-2"></i>
                                            <strong>Total Logs:</strong> {{ $device->logs()->count() }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-calendar-day text-success mr-2"></i>
                                            <strong>Today's Activity:</strong> {{ $device->logs()->whereDate('logged_at', today())->count() }}
                                        </li>
                                        <li>
                                            <i class="fas fa-clock text-secondary mr-2"></i>
                                            <strong>Uptime:</strong>
                                            @if($device->last_seen)
                                                {{ $device->created_at->diffForHumans($device->last_seen, true) }}
                                            @else
                                                N/A
                                            @endif
                                        </li>
                                    </ul>
                                    <hr>
                                    <h6>Quick Actions:</h6>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-block mb-2" onclick="generateQRCode({{ $device->id }})">
                                            <i class="fas fa-qrcode mr-2"></i>Generate QR Code
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-block" onclick="viewDeviceData({{ $device->id }})">
                                            <i class="fas fa-database mr-2"></i>View Raw Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Connection Test Result -->
        <div class="card shadow mb-4" id="connectionTestResult" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Connection Test Result</h6>
            </div>
            <div class="card-body">
                <div class="text-center" id="testResultContent">
                    <!-- Result will be shown here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function testConnection(deviceId) {
        const resultCard = document.getElementById('connectionTestResult');
        const resultContent = document.getElementById('testResultContent');

        // Show loading
        resultContent.innerHTML = `
            <div class="py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Testing device connection...</p>
            </div>
        `;
        resultCard.style.display = 'block';

        // Scroll to result
        resultCard.scrollIntoView({ behavior: 'smooth' });

        // Simulate connection test
        setTimeout(() => {
            const isSuccess = Math.random() > 0.3; // 70% success rate for demo

            if (isSuccess) {
                resultContent.innerHTML = `
                    <div class="py-4">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-check fa-2x text-white"></i>
                        </div>
                        <h5 class="text-success">Connection Successful!</h5>
                        <p class="text-muted">Device is responding normally.</p>
                        <div class="mt-4 text-left">
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Network connectivity: OK</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Sensor status: Active</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Data transmission: OK</p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i> Response time: 120ms</p>
                        </div>
                    </div>
                `;

                // Update last seen
                fetch(`/devices/${deviceId}/heartbeat`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            } else {
                resultContent.innerHTML = `
                    <div class="py-4">
                        <div class="rounded-circle bg-danger d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-times fa-2x text-white"></i>
                        </div>
                        <h5 class="text-danger">Connection Failed!</h5>
                        <p class="text-muted">Unable to connect to device.</p>
                        <div class="mt-4 text-left">
                            <p><i class="fas fa-times-circle text-danger mr-2"></i> Network connectivity: Failed</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Check device power</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Verify network settings</p>
                            <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Check firewall rules</p>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-warning" onclick="testConnection(${deviceId})">
                                <i class="fas fa-redo mr-2"></i>Retry Test
                            </button>
                        </div>
                    </div>
                `;
            }
        }, 2000);
    }

    function generateQRCode(deviceId) {
        // In a real app, this would generate/download QR code
        Swal.fire({
            title: 'QR Code Generated',
            text: 'QR code has been generated for this device.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function viewDeviceData(deviceId) {
        Swal.fire({
            title: 'Device Raw Data',
            html: `
                <div class="text-left">
                    <p><strong>Device ID:</strong> ${deviceId}</p>
                    <p><strong>UUID:</strong> {{ $device->uuid }}</p>
                    <p><strong>Status:</strong> {{ $device->status }}</p>
                    <p><strong>Created:</strong> {{ $device->created_at }}</p>
                    <p><strong>Last Updated:</strong> {{ $device->updated_at }}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Close'
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });
</script>
@endpush
