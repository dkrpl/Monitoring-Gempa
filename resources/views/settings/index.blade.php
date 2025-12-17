@extends('layouts.app')

@section('title', 'System Settings - EQMonitor')
@section('page-title', 'System Settings')

@section('action-button')
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-info" onclick="showSystemInfo()">
            <i class="fas fa-info-circle mr-2"></i>System Info
        </button>
        <button type="button" class="btn btn-warning ml-2" onclick="clearSystemCache()">
            <i class="fas fa-broom mr-2"></i>Clear Cache
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Settings Navigation -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <ul class="nav nav-pills mb-3" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="thresholds-tab" data-toggle="pill" data-target="#thresholds" type="button">
                            <i class="fas fa-sliders-h mr-2"></i>Thresholds
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-toggle="pill" data-target="#system" type="button">
                            <i class="fas fa-cog mr-2"></i>System
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-toggle="pill" data-target="#security" type="button">
                            <i class="fas fa-shield-alt mr-2"></i>Security
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="import-export-tab" data-toggle="pill" data-target="#import-export" type="button">
                            <i class="fas fa-file-export mr-2"></i>Import/Export
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="tab-content" id="settingsTabContent">
            <!-- Thresholds Tab -->
            <div class="tab-pane fade show active" id="thresholds" role="tabpanel">
                <!-- Thresholds content remains the same -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-sliders-h mr-2"></i>Earthquake Threshold Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="thresholdsForm" action="{{ route('settings.updateThresholds') }}" method="POST">
                            @csrf

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                These thresholds determine how earthquake events are classified.
                                When an earthquake magnitude reaches or exceeds a threshold,
                                the corresponding status is automatically assigned.
                            </div>

                            <div class="row">
                                @foreach($thresholds as $index => $threshold)
                                <div class="col-md-6 mb-4">
                                    <div class="card border-left-{{ $threshold->description == 'warning' ? 'warning' : 'danger' }} h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title text-{{ $threshold->description == 'warning' ? 'warning' : 'danger' }}">
                                                    <i class="fas fa-{{ $threshold->description == 'warning' ? 'exclamation-triangle' : 'fire' }} mr-2"></i>
                                                    {{ ucfirst($threshold->description) }} Threshold
                                                </h5>
                                                <span class="badge badge-{{ $threshold->description == 'warning' ? 'warning' : 'danger' }} p-2">
                                                    Current: {{ $threshold->min_value }}
                                                </span>
                                            </div>

                                            <input type="hidden" name="thresholds[{{ $index }}][id]" value="{{ $threshold->id }}">

                                            <div class="form-group">
                                                <label for="threshold_{{ $threshold->id }}_min_value" class="font-weight-bold">
                                                    Minimum Magnitude
                                                </label>
                                                <div class="input-group">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="threshold_{{ $threshold->id }}_min_value"
                                                           name="thresholds[{{ $index }}][min_value]"
                                                           value="{{ old('thresholds.' . $index . '.min_value', $threshold->min_value) }}"
                                                           step="0.1"
                                                           min="0"
                                                           max="10"
                                                           required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Richter Scale</span>
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Earthquakes with magnitude ≥ this value will be classified as {{ $threshold->description }}
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label for="threshold_{{ $threshold->id }}_description" class="font-weight-bold">
                                                    Description
                                                </label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="threshold_{{ $threshold->id }}_description"
                                                       name="thresholds[{{ $index }}][description]"
                                                       value="{{ old('thresholds.' . $index . '.description', $threshold->description) }}"
                                                       required>
                                                <small class="form-text text-muted">
                                                    Display name for this threshold level
                                                </small>
                                            </div>

                                            <div class="alert alert-{{ $threshold->description == 'warning' ? 'warning' : 'danger' }} mt-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        <i class="fas fa-{{ $threshold->description == 'warning' ? 'exclamation-triangle' : 'fire' }} fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <strong>Effect:</strong>
                                                        @if($threshold->description == 'warning')
                                                            Triggers warning alerts and notifications. Monitor closely.
                                                        @else
                                                            Triggers danger alerts and emergency notifications. Immediate action required.
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Normal Threshold Info -->
                                <div class="col-12">
                                    <div class="card border-left-success">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="card-title text-success">
                                                        <i class="fas fa-check-circle mr-2"></i>
                                                        Normal Events
                                                    </h5>
                                                    <p class="mb-0">
                                                        Earthquakes below <strong>{{ $thresholds->first()->min_value ?? 3.0 }}</strong>
                                                        magnitude are considered normal and don't trigger alerts.
                                                    </p>
                                                </div>
                                                <span class="badge badge-success p-2">
                                                    &lt; {{ $thresholds->first()->min_value ?? 3.0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Save Threshold Settings
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetThresholds()">
                                        <i class="fas fa-undo mr-2"></i>Reset to Defaults
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- System Settings Tab -->
            <div class="tab-pane fade" id="system" role="tabpanel">
                <!-- System settings content remains the same -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog mr-2"></i>System Configuration
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="systemForm" action="{{ route('settings.updateSystem') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i>General Settings</h5>

                                    <div class="form-group">
                                        <label for="system_name" class="font-weight-bold">System Name</label>
                                        <input type="text"
                                               class="form-control"
                                               id="system_name"
                                               name="system_name"
                                               value="{{ old('system_name', $settings['system_name'] ?? '') }}"
                                               required>
                                        <small class="form-text text-muted">Display name for the application</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="system_email" class="font-weight-bold">System Email</label>
                                        <input type="email"
                                               class="form-control"
                                               id="system_email"
                                               name="system_email"
                                               value="{{ old('system_email', $settings['system_email'] ?? '') }}"
                                               required>
                                        <small class="form-text text-muted">Email address used for system communications</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="timezone" class="font-weight-bold">Timezone</label>
                                        <select class="form-control" id="timezone" name="timezone" required>
                                            @php
                                                $timezones = timezone_identifiers_list();
                                            @endphp
                                            @foreach($timezones as $tz)
                                                <option value="{{ $tz }}" {{ ($settings['timezone'] ?? '') == $tz ? 'selected' : '' }}>
                                                    {{ $tz }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">System timezone for all date/time displays</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="language" class="font-weight-bold">Language</label>
                                        <select class="form-control" id="language" name="language" required>
                                            <option value="en" {{ ($settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                            <option value="id" {{ ($settings['language'] ?? '') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-database mr-2"></i>Data Management</h5>

                                    <div class="form-group">
                                        <label for="data_retention_days" class="font-weight-bold">Data Retention (Days)</label>
                                        <input type="number"
                                               class="form-control"
                                               id="data_retention_days"
                                               name="data_retention_days"
                                               value="{{ old('data_retention_days', $settings['data_retention_days'] ?? 90) }}"
                                               min="1"
                                               max="3650"
                                               required>
                                        <small class="form-text text-muted">How long to keep historical data (1-3650 days)</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="max_log_size" class="font-weight-bold">Max Log Size (MB)</label>
                                        <input type="number"
                                               class="form-control"
                                               id="max_log_size"
                                               name="max_log_size"
                                               value="{{ old('max_log_size', $settings['max_log_size'] ?? 100) }}"
                                               min="10"
                                               max="1000"
                                               required>
                                        <small class="form-text text-muted">Maximum size for log files before rotation</small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="auto_cleanup"
                                               name="auto_cleanup"
                                               value="1"
                                               {{ ($settings['auto_cleanup'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="auto_cleanup">
                                            Enable Automatic Cleanup
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Automatically remove old data based on retention policy
                                        </small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="backup_enabled"
                                               name="backup_enabled"
                                               value="1"
                                               {{ ($settings['backup_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="backup_enabled">
                                            Enable Automatic Backups
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="backup_frequency" class="font-weight-bold">Backup Frequency</label>
                                        <select class="form-control" id="backup_frequency" name="backup_frequency" required>
                                            <option value="daily" {{ ($settings['backup_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="weekly" {{ ($settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ ($settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-sync-alt mr-2"></i>Performance</h5>

                                    <div class="form-group">
                                        <label for="refresh_interval" class="font-weight-bold">Refresh Interval (Seconds)</label>
                                        <input type="number"
                                               class="form-control"
                                               id="refresh_interval"
                                               name="refresh_interval"
                                               value="{{ old('refresh_interval', $settings['refresh_interval'] ?? 30) }}"
                                               min="5"
                                               max="300"
                                               required>
                                        <small class="form-text text-muted">How often to refresh monitoring data (5-300 seconds)</small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="maintenance_mode"
                                               name="maintenance_mode"
                                               value="1"
                                               {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="maintenance_mode">
                                            Maintenance Mode
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            When enabled, only administrators can access the system
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-map mr-2"></i>Map Settings</h5>

                                    <div class="form-group">
                                        <label for="map_provider" class="font-weight-bold">Map Provider</label>
                                        <select class="form-control" id="map_provider" name="map_provider" required>
                                            <option value="openstreetmap" {{ ($settings['map_provider'] ?? '') == 'openstreetmap' ? 'selected' : '' }}>OpenStreetMap (Free)</option>
                                            <option value="google" {{ ($settings['map_provider'] ?? '') == 'google' ? 'selected' : '' }}>Google Maps</option>
                                            <option value="mapbox" {{ ($settings['map_provider'] ?? '') == 'mapbox' ? 'selected' : '' }}>Mapbox</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="map_api_key" class="font-weight-bold">Map API Key</label>
                                        <input type="text"
                                               class="form-control"
                                               id="map_api_key"
                                               name="map_api_key"
                                               value="{{ old('map_api_key', $settings['map_api_key'] ?? '') }}">
                                        <small class="form-text text-muted">API key for map provider (if required)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Save System Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab (REMOVED from navigation but content kept for reference) -->
            <!-- This tab content is commented out since it's removed from navigation -->
            {{--
            <div class="tab-pane fade" id="notifications" role="tabpanel">
                <!-- Notifications content removed -->
            </div>
            --}}

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <!-- Security settings content remains the same -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-shield-alt mr-2"></i>Security Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="securityForm" action="{{ route('settings.updateSecurity') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-user-lock mr-2"></i>Authentication</h5>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="require_2fa"
                                               name="require_2fa"
                                               value="1"
                                               {{ ($settings['require_2fa'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="require_2fa">
                                            Require Two-Factor Authentication
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            All users will be required to set up 2FA
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="session_timeout" class="font-weight-bold">Session Timeout (Minutes)</label>
                                        <input type="number"
                                               class="form-control"
                                               id="session_timeout"
                                               name="session_timeout"
                                               value="{{ old('session_timeout', $settings['session_timeout'] ?? 60) }}"
                                               min="5"
                                               max="480"
                                               required>
                                        <small class="form-text text-muted">Automatically log out users after inactivity</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="max_login_attempts" class="font-weight-bold">Max Login Attempts</label>
                                        <input type="number"
                                               class="form-control"
                                               id="max_login_attempts"
                                               name="max_login_attempts"
                                               value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                                               min="1"
                                               max="10"
                                               required>
                                        <small class="form-text text-muted">Number of failed login attempts before account lockout</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="password_expiry_days" class="font-weight-bold">Password Expiry (Days)</label>
                                        <input type="number"
                                               class="form-control"
                                               id="password_expiry_days"
                                               name="password_expiry_days"
                                               value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? 90) }}"
                                               min="0"
                                               max="365"
                                               required>
                                        <small class="form-text text-muted">0 = Never expire</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-network-wired mr-2"></i>Network & API Security</h5>

                                    <div class="form-group">
                                        <label for="ip_whitelist" class="font-weight-bold">IP Whitelist</label>
                                        <textarea class="form-control"
                                                  id="ip_whitelist"
                                                  name="ip_whitelist"
                                                  rows="3"
                                                  placeholder="192.168.1.1&#10;10.0.0.0/24">{{ old('ip_whitelist', $settings['ip_whitelist'] ?? '') }}</textarea>
                                        <small class="form-text text-muted">
                                            One IP address or CIDR range per line. Leave empty to allow all IPs.
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="api_rate_limit" class="font-weight-bold">API Rate Limit</label>
                                        <input type="number"
                                               class="form-control"
                                               id="api_rate_limit"
                                               name="api_rate_limit"
                                               value="{{ old('api_rate_limit', $settings['api_rate_limit'] ?? 100) }}"
                                               min="10"
                                               max="1000"
                                               required>
                                        <small class="form-text text-muted">Maximum API requests per minute per IP</small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="enable_audit_log"
                                               name="enable_audit_log"
                                               value="1"
                                               {{ ($settings['enable_audit_log'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="enable_audit_log">
                                            Enable Audit Logging
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Log all user activities and system changes
                                        </small>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="data_encryption"
                                               name="data_encryption"
                                               value="1"
                                               {{ ($settings['data_encryption'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold" for="data_encryption">
                                            Enable Data Encryption
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            Encrypt sensitive data in the database
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Warning:</strong> Changing security settings may affect system access and functionality.
                                        Make sure you understand the implications before saving.
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Save Security Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Import/Export Tab -->
            <div class="tab-pane fade" id="import-export" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-file-export mr-2"></i>Export Settings
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="fas fa-download fa-3x text-primary mb-3"></i>
                                    <h5>Export Current Settings</h5>
                                    <p class="text-muted">
                                        Download all system settings and thresholds as a JSON file.
                                        This can be used for backup or to transfer settings to another system.
                                    </p>
                                    <a href="{{ route('settings.export') }}" class="btn btn-primary">
                                        <i class="fas fa-download mr-2"></i>Export Settings
                                    </a>
                                </div>

                                <hr class="my-4">

                                <h6 class="font-weight-bold">What's Included:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        All system settings
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Earthquake thresholds
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Notification templates
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Security configurations
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        Metadata (export date, version)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-file-import mr-2"></i>Import Settings
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="importForm" action="{{ route('settings.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="text-center mb-4">
                                        <i class="fas fa-upload fa-3x text-warning mb-3"></i>
                                        <h5>Import Settings from File</h5>
                                        <p class="text-muted">
                                            Upload a previously exported JSON file to restore settings.
                                            <strong class="text-danger">This will overwrite current settings!</strong>
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="settings_file" class="font-weight-bold">Settings File (JSON)</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                   class="custom-file-input"
                                                   id="settings_file"
                                                   name="settings_file"
                                                   accept=".json"
                                                   required>
                                            <label class="custom-file-label" for="settings_file">Choose JSON file</label>
                                        </div>
                                        <small class="form-text text-muted">Maximum file size: 2MB</small>
                                    </div>

                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Warning:</strong> Importing settings will overwrite all current system settings.
                                        This action cannot be undone!
                                    </div>

                                    <button type="button" class="btn btn-warning btn-block" onclick="confirmImport()">
                                        <i class="fas fa-file-import mr-2"></i>Import Settings
                                    </button>
                                </form>

                                <hr class="my-4">

                                <h6 class="font-weight-bold">Reset to Defaults</h6>
                                <p class="text-muted">
                                    Restore all settings to their original default values.
                                </p>
                                <button type="button" class="btn btn-danger btn-block" onclick="confirmReset()">
                                    <i class="fas fa-undo mr-2"></i>Reset All Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info Modal -->
<div class="modal fade" id="systemInfoModal" tabindex="-1" role="dialog" aria-labelledby="systemInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemInfoModalLabel">System Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3" id="systemInfoLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading system information...</p>
                </div>
                <div id="systemInfoContent" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(45deg, #4e73df, #224abe);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }

    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .tab-content {
        padding-top: 1rem;
    }

    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Form submission handlers
    document.getElementById('thresholdsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm(this, 'thresholds');
    });

    document.getElementById('systemForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm(this, 'system');
    });

    // Removed notifications form handler since the tab is removed
    // document.getElementById('notificationsForm').addEventListener('submit', function(e) {
    //     e.preventDefault();
    //     submitForm(this, 'notifications');
    // });

    document.getElementById('securityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm(this, 'security');
    });

    function submitForm(form, type) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;

        // Get form data
        const formData = new FormData(form);

        // Send AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                // If response is a redirect (non-AJAX)
                window.location.href = data.redirect;
            } else {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved!',
                    text: `${type.charAt(0).toUpperCase() + type.slice(1)} settings have been updated successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload to show updated settings
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to save settings. Please try again.'
            });
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    function resetThresholds() {
        Swal.fire({
            title: 'Reset Thresholds?',
            text: 'This will reset all thresholds to their default values.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reset form values
                document.querySelectorAll('#thresholdsForm input[type="number"]').forEach(input => {
                    if (input.name.includes('warning')) {
                        input.value = 3.0;
                    } else if (input.name.includes('danger')) {
                        input.value = 5.0;
                    }
                });

                Swal.fire(
                    'Reset!',
                    'Thresholds have been reset to default values.',
                    'success'
                );
            }
        });
    }

    function confirmImport() {
        const fileInput = document.getElementById('settings_file');

        if (!fileInput.files.length) {
            Swal.fire('Error!', 'Please select a file to import.', 'error');
            return;
        }

        Swal.fire({
            title: 'Import Settings?',
            html: `
                <div class="alert alert-danger text-left">
                    <strong>Warning:</strong> This will overwrite all current system settings!<br>
                    This action cannot be undone.
                </div>
                <p>Are you sure you want to continue?</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, import!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('importForm').submit();
            }
        });
    }

    function confirmReset() {
        Swal.fire({
            title: 'Reset All Settings?',
            html: `
                <div class="alert alert-danger text-left">
                    <strong>Warning:</strong> This will reset ALL system settings to default values!<br>
                    This includes thresholds, notifications, security, and all other configurations.
                </div>
                <p>This action cannot be undone. Are you sure?</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset everything!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("settings.reset") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire(
                        'Reset Complete!',
                        'All settings have been reset to default values.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to reset settings.', 'error');
                });
            }
        });
    }

    function showSystemInfo() {
        $('#systemInfoModal').modal('show');

        fetch('{{ route("settings.systemInfo") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const info = data.system_info;

                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">System Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Laravel Version:</strong> ${info.system.laravel_version}</p>
                                        <p><strong>PHP Version:</strong> ${info.system.php_version}</p>
                                        <p><strong>Server:</strong> ${info.system.server_software}</p>
                                        <p><strong>Database:</strong> ${info.system.database}</p>
                                        <p><strong>Timezone:</strong> ${info.system.timezone}</p>
                                        <p><strong>Environment:</strong> ${info.system.environment}</p>
                                        <p><strong>Debug Mode:</strong> ${info.system.debug_mode ? 'Enabled' : 'Disabled'}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Application Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Users:</strong> ${info.application.users_count}</p>
                                        <p><strong>Devices:</strong> ${info.application.devices_count}</p>
                                        <p><strong>Earthquake Events:</strong> ${info.application.events_count}</p>
                                        <p><strong>Device Logs:</strong> ${info.application.logs_count}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Resource Limits</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Memory Limit:</strong> ${info.resources.memory_limit}</p>
                                        <p><strong>Max Execution Time:</strong> ${info.resources.max_execution_time}s</p>
                                        <p><strong>Upload Max Filesize:</strong> ${info.resources.upload_max_filesize}</p>
                                        <p><strong>Post Max Size:</strong> ${info.resources.post_max_size}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0">Storage Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Total:</strong> ${info.storage.total_formatted}</p>
                                        <p><strong>Used:</strong> ${info.storage.used_formatted}</p>
                                        <p><strong>Free:</strong> ${info.storage.free_formatted}</p>
                                        <div class="progress mt-2" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: ${(info.storage.used / info.storage.total * 100).toFixed(1)}%">
                                                ${(info.storage.used / info.storage.total * 100).toFixed(1)}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('systemInfoLoading').style.display = 'none';
                    document.getElementById('systemInfoContent').innerHTML = html;
                    document.getElementById('systemInfoContent').style.display = 'block';
                }
            })
            .catch(error => {
                document.getElementById('systemInfoLoading').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Failed to load system information. Please try again.
                    </div>
                `;
            });
    }

    function clearSystemCache() {
        Swal.fire({
            title: 'Clear System Cache?',
            text: 'This will clear all cached settings and data.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, clear cache!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("settings.clearCache") }}', {
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
                            'Cache Cleared!',
                            'System cache has been cleared successfully.',
                            'success'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to clear cache.', 'error');
                });
            }
        });
    }

    // File input label
    document.getElementById('settings_file').addEventListener('change', function() {
        const fileName = this.files[0]?.name || 'Choose file';
        this.nextElementSibling.textContent = fileName;
    });

    // Tab persistence
    document.addEventListener('DOMContentLoaded', function() {
        // Store active tab in localStorage
        $('button[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            localStorage.setItem('activeSettingsTab', $(e.target).attr('id'));
        });

        // Retrieve active tab
        const activeTab = localStorage.getItem('activeSettingsTab');
        if (activeTab) {
            $(`#${activeTab}`).tab('show');
        }
    });
</script>
@endpush
