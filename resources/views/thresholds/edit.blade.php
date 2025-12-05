@extends('layouts.app')

@section('title', 'Edit Threshold - EQMonitor')
@section('page-title', 'Edit Threshold: ' . ucfirst($threshold->description))

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('thresholds.show', $threshold) }}" class="btn btn-info">
            <i class="fas fa-eye mr-2"></i>View Details
        </a>
        <a href="{{ route('thresholds.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Thresholds
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Threshold Configuration</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('thresholds.update', $threshold) }}" method="POST" id="thresholdForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label required">
                                <i class="fas fa-tag mr-2"></i>Status Name
                            </label>
                            <select class="form-control @error('description') is-invalid @enderror"
                                    id="description"
                                    name="description"
                                    required>
                                <option value="">Select Status</option>
                                <option value="warning" {{ old('description', $threshold->description) == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="danger" {{ old('description', $threshold->description) == 'danger' ? 'selected' : '' }}>Danger</option>
                                <option value="critical" {{ old('description', $threshold->description) == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="min_value" class="form-label required">
                                <i class="fas fa-chart-line mr-2"></i>Minimum Magnitude
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('min_value') is-invalid @enderror"
                                       id="min_value"
                                       name="min_value"
                                       value="{{ old('min_value', $threshold->min_value) }}"
                                       step="0.1"
                                       min="0"
                                       max="10"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Richter Scale</span>
                                </div>
                            </div>
                            @error('min_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label required">
                                <i class="fas fa-palette mr-2"></i>Color
                            </label>
                            <div class="input-group">
                                <input type="color"
                                       class="form-control @error('color') is-invalid @enderror"
                                       id="color"
                                       name="color"
                                       value="{{ old('color', $threshold->color) }}"
                                       style="height: 38px; padding: 0;">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="colorPreview"
                                          style="width: 50px; background-color: {{ $threshold->color }};"></span>
                                </div>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">
                                <i class="fas fa-sort-numeric-down mr-2"></i>Priority
                            </label>
                            <input type="number"
                                   class="form-control @error('priority') is-invalid @enderror"
                                   id="priority"
                                   name="priority"
                                   value="{{ old('priority', $threshold->priority) }}"
                                   min="1"
                                   max="999">
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="card border-left-primary mb-4">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bell mr-2"></i>Notification Settings
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="notification_enabled"
                                               name="notification_enabled"
                                               value="1"
                                               {{ old('notification_enabled', $threshold->notification_enabled) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notification_enabled">
                                            <strong>Enable Notifications</strong>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="notification_message" class="form-label">
                                        <i class="fas fa-comment-dots mr-2"></i>Notification Message Template
                                    </label>
                                    <textarea class="form-control @error('notification_message') is-invalid @enderror"
                                              id="notification_message"
                                              name="notification_message"
                                              rows="3">{{ old('notification_message', $threshold->notification_message) }}</textarea>
                                    @error('notification_message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Use placeholders: {magnitude}, {location}, {device}, {time}
                                    </small>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="auto_alert"
                                               name="auto_alert"
                                               value="1"
                                               {{ old('auto_alert', $threshold->auto_alert) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="auto_alert">
                                            <strong>Enable Auto-Alert</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
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
                                       value="{{ $threshold->created_at->format('F d, Y H:i:s') }}"
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
                                       value="{{ $threshold->updated_at->format('F d, Y H:i:s') }}"
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Update Threshold
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="resetForm()">
                                <i class="fas fa-undo mr-2"></i>Reset Changes
                            </button>
                            <a href="{{ route('thresholds.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Configuration -->
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background-color: {{ $threshold->color }}; color: white;">
                <h6 class="m-0 font-weight-bold">Current Configuration</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="current-status mb-3">
                        <span class="badge" style="background-color: {{ $threshold->color }}; color: white; padding: 10px 20px; font-size: 1.1rem;">
                            {{ strtoupper($threshold->description) }}
                        </span>
                    </div>
                    <h4>{{ number_format($threshold->min_value, 1) }}</h4>
                    <small class="text-muted">Current Minimum Magnitude</small>
                </div>

                <div class="current-settings">
                    <div class="setting-item">
                        <i class="fas fa-bell mr-2 {{ $threshold->notification_enabled ? 'text-success' : 'text-secondary' }}"></i>
                        <span>Notifications: {{ $threshold->notification_enabled ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="setting-item">
                        <i class="fas fa-bolt mr-2 {{ $threshold->auto_alert ? 'text-danger' : 'text-warning' }}"></i>
                        <span>Auto-Alert: {{ $threshold->auto_alert ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="setting-item">
                        <i class="fas fa-sort-numeric-down mr-2 text-info"></i>
                        <span>Priority: {{ $threshold->priority }}</span>
                    </div>
                </div>

                @if($threshold->notification_message)
                <div class="mt-4">
                    <h6>Current Message:</h6>
                    <div class="alert alert-light small">
                        {{ $threshold->notification_message }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Impact Warning -->
        <div class="card border-left-warning shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Important Notice
                </h6>
            </div>
            <div class="card-body">
                <p class="small">
                    <strong>Changing threshold values may affect:</strong>
                </p>
                <ul class="small">
                    <li>Existing earthquake event classifications</li>
                    <li>Alert triggers and notifications</li>
                    <li>Historical data analysis</li>
                    <li>System reports and statistics</li>
                </ul>
                <p class="small text-muted mt-3">
                    Consider updating related configurations if you change magnitude values significantly.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .current-status .badge {
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .current-settings {
        border-top: 1px solid #e3e6f0;
        padding-top: 15px;
    }

    .setting-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .setting-item:last-child {
        margin-bottom: 0;
    }

    input[type="color"] {
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    #colorPreview {
        border: 1px solid #ced4da;
        border-left: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Update color preview
    document.getElementById('color').addEventListener('input', function() {
        document.getElementById('colorPreview').style.backgroundColor = this.value;
    });

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
                document.getElementById('thresholdForm').reset();
                document.getElementById('colorPreview').style.backgroundColor = '{{ $threshold->color }}';
                Swal.fire(
                    'Reset!',
                    'All changes have been reverted.',
                    'success'
                );
            }
        });
    }

    // Form validation
    document.getElementById('thresholdForm').addEventListener('submit', function(e) {
        const description = document.getElementById('description').value;
        const magnitude = document.getElementById('min_value').value;

        if (!description || !magnitude) {
            e.preventDefault();
            Swal.fire('Error!', 'Please fill all required fields.', 'error');
            return;
        }

        // Check if magnitude value has changed significantly
        const originalMagnitude = {{ $threshold->min_value }};
        const newMagnitude = parseFloat(magnitude);

        if (Math.abs(newMagnitude - originalMagnitude) > 1.0) {
            e.preventDefault();
            Swal.fire({
                title: 'Significant Change Detected',
                text: `You're changing the magnitude from ${originalMagnitude} to ${newMagnitude}. This may affect many earthquake event classifications. Are you sure?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Review Changes'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Re-submit the form
                    document.getElementById('thresholdForm').submit();
                }
            });
            return false;
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
