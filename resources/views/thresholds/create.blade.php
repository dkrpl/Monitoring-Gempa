@extends('layouts.app')

@section('title', 'Create Threshold - EQMonitor')
@section('page-title', 'Create New Threshold')

@section('action-button')
    <a href="{{ route('thresholds.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Thresholds
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Threshold Configuration</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('thresholds.store') }}" method="POST" id="thresholdForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label required">
                                <i class="fas fa-tag mr-2"></i>Status Name
                            </label>
                            <select class="form-control @error('description') is-invalid @enderror"
                                    id="description"
                                    name="description"
                                    required
                                    onchange="updateColorPreview()">
                                <option value="">Select Status</option>
                                <option value="warning" {{ old('description') == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="danger" {{ old('description') == 'danger' ? 'selected' : '' }}>Danger</option>
                                <option value="critical" {{ old('description') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                The status name that will be assigned to earthquakes
                            </small>
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
                                       value="{{ old('min_value') }}"
                                       step="0.1"
                                       min="0"
                                       max="10"
                                       required
                                       oninput="updateMagnitudePreview()">
                                <div class="input-group-append">
                                    <span class="input-group-text">Richter Scale</span>
                                </div>
                            </div>
                            @error('min_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Earthquakes at or above this magnitude will trigger this status
                            </small>
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
                                       value="{{ old('color', '#6c757d') }}"
                                       style="height: 38px; padding: 0;"
                                       onchange="updateColorPreview()">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="colorPreview" style="width: 50px;"></span>
                                </div>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Color used to represent this threshold in visualizations
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">
                                <i class="fas fa-sort-numeric-down mr-2"></i>Priority
                            </label>
                            <input type="number"
                                   class="form-control @error('priority') is-invalid @enderror"
                                   id="priority"
                                   name="priority"
                                   value="{{ old('priority', 999) }}"
                                   min="1"
                                   max="999">
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Lower numbers have higher priority (1 = highest)
                            </small>
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
                                               {{ old('notification_enabled', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="notification_enabled">
                                            <strong>Enable Notifications</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Send notifications when this threshold is triggered
                                        </small>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="notification_message" class="form-label">
                                        <i class="fas fa-comment-dots mr-2"></i>Notification Message Template
                                    </label>
                                    <textarea class="form-control @error('notification_message') is-invalid @enderror"
                                              id="notification_message"
                                              name="notification_message"
                                              rows="3"
                                              placeholder="Enter notification message template...">{{ old('notification_message') }}</textarea>
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
                                               {{ old('auto_alert') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="auto_alert">
                                            <strong>Enable Auto-Alert</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Automatically send alerts without manual confirmation
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save mr-2"></i>Create Threshold
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="previewThreshold()">
                                <i class="fas fa-eye mr-2"></i>Preview
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
        <!-- Preview Card -->
        <div class="card shadow mb-4" id="thresholdPreview">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Threshold Preview</h6>
            </div>
            <div class="card-body text-center">
                <div class="preview-display mb-4">
                    <div class="preview-status" id="previewStatus">
                        <span class="badge" style="background-color: #6c757d; color: white; font-size: 1.2rem; padding: 10px 20px;">
                            STATUS
                        </span>
                    </div>
                    <div class="preview-magnitude mt-3">
                        <h4 id="previewMagnitude">≥ 0.0</h4>
                        <small class="text-muted">Minimum Magnitude</small>
                    </div>
                </div>

                <div class="preview-settings">
                    <div class="setting-item">
                        <i class="fas fa-bell text-primary mr-2"></i>
                        <span id="previewNotification">Notifications: Enabled</span>
                    </div>
                    <div class="setting-item">
                        <i class="fas fa-bolt text-warning mr-2"></i>
                        <span id="previewAlert">Auto-Alert: Disabled</span>
                    </div>
                    <div class="setting-item">
                        <i class="fas fa-sort-numeric-down text-info mr-2"></i>
                        <span id="previewPriority">Priority: 999</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Configuration Guidelines</h6>
            </div>
            <div class="card-body">
                <h6>Recommended Values:</h6>
                <ul class="small">
                    <li><strong>Warning:</strong> 3.0 - 4.9 (Light to Moderate)</li>
                    <li><strong>Danger:</strong> 5.0 - 6.9 (Strong to Major)</li>
                    <li><strong>Critical:</strong> 7.0+ (Great)</li>
                </ul>

                <h6>Best Practices:</h6>
                <ul class="small">
                    <li>Set distinct magnitude values</li>
                    <li>Enable notifications for important thresholds</li>
                    <li>Use auto-alert only for critical levels</li>
                    <li>Test notification messages</li>
                    <li>Consider regional seismic activity</li>
                </ul>

                <h6>Placeholders for Messages:</h6>
                <div class="small">
                    <code>{magnitude}</code> - Earthquake magnitude<br>
                    <code>{location}</code> - Device location<br>
                    <code>{device}</code> - Device name<br>
                    <code>{time}</code> - Occurrence time<br>
                </div>
            </div>
        </div>

        <!-- Example Messages -->
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Example Messages</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning small mb-2">
                    <strong>Warning:</strong> Earthquake magnitude {magnitude} detected at {location}. Be prepared.
                </div>
                <div class="alert alert-danger small mb-2">
                    <strong>Danger:</strong> Strong earthquake ({magnitude}) detected! Take safety precautions immediately.
                </div>
                <div class="alert alert-dark small">
                    <strong>Critical:</strong> MAJOR EARTHQUAKE ALERT! Magnitude {magnitude}. EVACUATE if necessary.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .preview-display {
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .preview-status .badge {
        border-radius: 20px;
        font-size: 1.1rem;
        padding: 12px 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .preview-magnitude h4 {
        font-weight: bold;
        color: #495057;
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

    .required::after {
        content: " *";
        color: #e74a3b;
    }
</style>
@endpush

@push('scripts')
<script>
    // Update color preview
    function updateColorPreview() {
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('colorPreview');
        const description = document.getElementById('description').value;

        // Set default colors based on description
        if (description) {
            const defaultColors = {
                'warning': '#ffc107',
                'danger': '#e74a3b',
                'critical': '#343a40'
            };

            if (defaultColors[description] && (!colorInput.value || colorInput.value === '#6c757d')) {
                colorInput.value = defaultColors[description];
            }
        }

        colorPreview.style.backgroundColor = colorInput.value;
        updatePreview();
    }

    // Update magnitude preview
    function updateMagnitudePreview() {
        updatePreview();
    }

    // Update notification preview
    document.getElementById('notification_enabled').addEventListener('change', updatePreview);
    document.getElementById('auto_alert').addEventListener('change', updatePreview);
    document.getElementById('priority').addEventListener('input', updatePreview);

    // Update the entire preview
    function updatePreview() {
        const description = document.getElementById('description').value;
        const magnitude = document.getElementById('min_value').value;
        const color = document.getElementById('color').value;
        const notificationEnabled = document.getElementById('notification_enabled').checked;
        const autoAlert = document.getElementById('auto_alert').checked;
        const priority = document.getElementById('priority').value;

        // Update status badge
        const previewStatus = document.getElementById('previewStatus');
        if (description) {
            const displayName = description.charAt(0).toUpperCase() + description.slice(1);
            previewStatus.innerHTML = `
                <span class="badge" style="background-color: ${color}; color: white; font-size: 1.2rem; padding: 10px 20px;">
                    ${displayName}
                </span>
            `;
        } else {
            previewStatus.innerHTML = `
                <span class="badge badge-secondary" style="font-size: 1.2rem; padding: 10px 20px;">
                    STATUS
                </span>
            `;
        }

        // Update magnitude
        const previewMagnitude = document.getElementById('previewMagnitude');
        previewMagnitude.textContent = magnitude ? `≥ ${parseFloat(magnitude).toFixed(1)}` : '≥ 0.0';

        // Update settings
        document.getElementById('previewNotification').textContent =
            `Notifications: ${notificationEnabled ? 'Enabled' : 'Disabled'}`;

        document.getElementById('previewAlert').textContent =
            `Auto-Alert: ${autoAlert ? 'Enabled' : 'Disabled'}`;

        document.getElementById('previewPriority').textContent =
            `Priority: ${priority}`;

        // Update color preview
        document.getElementById('colorPreview').style.backgroundColor = color;
    }

    function previewThreshold() {
        const description = document.getElementById('description').value;
        const magnitude = document.getElementById('min_value').value;

        if (!description || !magnitude) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in Status Name and Minimum Magnitude first.'
            });
            return;
        }

        updatePreview();

        // Show preview card
        document.getElementById('thresholdPreview').style.display = 'block';

        // Scroll to preview
        document.getElementById('thresholdPreview').scrollIntoView({
            behavior: 'smooth'
        });
    }

    // Form validation
    document.getElementById('thresholdForm').addEventListener('submit', function(e) {
        const description = document.getElementById('description').value;
        const magnitude = document.getElementById('min_value').value;

        if (!description || !magnitude) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please fill all required fields.'
            });
            return;
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        submitBtn.disabled = true;

        // Allow form submission
        return true;
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateColorPreview();

        // Initialize preview values based on form inputs
        updatePreview();
    });
</script>
@endpush
