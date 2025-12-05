@extends('layouts.app')

@section('title', 'Export Data - EQMonitor')
@section('page-title', 'Export Data')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Welcome Card -->
        <div class="card border-left-primary shadow mb-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Export Data Center
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Export your data in multiple formats
                        </div>
                        <p class="mt-2 mb-0 text-muted">
                            Download system data in CSV, JSON, or Excel format. Choose from individual modules or export all data at once.
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-download fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Users Export -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users mr-2"></i>Export Users
                </h6>
                <span class="badge badge-info">{{ \App\Models\User::count() }} Users</span>
            </div>
            <div class="card-body">
                <form id="exportUsersForm" action="{{ route('exports.users') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="start_date"
                                       value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="end_date"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control form-control-sm" name="role">
                            <option value="all">All Roles</option>
                            <option value="admin">Administrators Only</option>
                            <option value="user">Regular Users Only</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select class="form-control form-control-sm" name="format">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Export Users Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Devices Export -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-microchip mr-2"></i>Export Devices
                </h6>
                <span class="badge badge-info">{{ \App\Models\Device::count() }} Devices</span>
            </div>
            <div class="card-body">
                <form id="exportDevicesForm" action="{{ route('exports.devices') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Device Status</label>
                        <select class="form-control form-control-sm" name="status">
                            <option value="all">All Devices</option>
                            <option value="aktif">Active Only</option>
                            <option value="nonaktif">Inactive Only</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="start_date"
                                       value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="end_date"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select class="form-control form-control-sm" name="format">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Export Devices Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Earthquake Events Export -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-earthquake mr-2"></i>Export Events
                </h6>
                <span class="badge badge-info">{{ \App\Models\EarthquakeEvent::count() }} Events</span>
            </div>
            <div class="card-body">
                <form id="exportEventsForm" action="{{ route('exports.earthquake-events') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Event Status</label>
                        <select class="form-control form-control-sm" name="status">
                            <option value="all">All Events</option>
                            <option value="warning">Warning Only</option>
                            <option value="danger">Danger Only</option>
                            <option value="normal">Normal Only</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="start_date"
                                       value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="end_date"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select class="form-control form-control-sm" name="format">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Export Events Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Device Logs Export -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Export Device Logs
                </h6>
                <span class="badge badge-info">{{ \App\Models\DeviceLog::count() }} Logs</span>
            </div>
            <div class="card-body">
                <form id="exportLogsForm" action="{{ route('exports.device-logs') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Select Device</label>
                        <select class="form-control form-control-sm" name="device_id">
                            <option value="">All Devices</option>
                            @foreach(\App\Models\Device::all() as $device)
                                <option value="{{ $device->id }}">{{ $device->nama_device }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="start_date"
                                       value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="end_date"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select class="form-control form-control-sm" name="format">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Export Logs Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Export -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>Export Statistics
                </h6>
                <span class="badge badge-info">Analytics Report</span>
            </div>
            <div class="card-body">
                <form id="exportStatsForm" action="{{ route('exports.statistics') }}" method="GET" target="_blank">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select class="form-control form-control-sm" name="report_type" id="reportType">
                            <option value="daily">Daily Report</option>
                            <option value="weekly">Weekly Report</option>
                            <option value="monthly">Monthly Report</option>
                            <option value="yearly">Yearly Report</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="start_date"
                                       id="statsStartDate" value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control form-control-sm" name="end_date"
                                       id="statsEndDate" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select class="form-control form-control-sm" name="format">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Export Statistics Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Full System Export -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-database mr-2"></i>Full System Export
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">Export All Data</h5>
                        <p class="card-text">
                            Download complete system data including users, devices, earthquake events, device logs, and thresholds in a single ZIP file.
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            The export will include multiple files in your selected format.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form id="exportAllForm" action="{{ route('exports.all') }}" method="GET" target="_blank">
                            <div class="form-group">
                                <label>Include Data</label>
                                <div class="checkbox-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include[]" value="users" checked>
                                        <label class="form-check-label">Users</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include[]" value="devices" checked>
                                        <label class="form-check-label">Devices</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include[]" value="events" checked>
                                        <label class="form-check-label">Earthquake Events</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include[]" value="logs" checked>
                                        <label class="form-check-label">Device Logs</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="include[]" value="thresholds" checked>
                                        <label class="form-check-label">Thresholds</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Date Range</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="date" class="form-control form-control-sm" name="start_date"
                                               value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" class="form-control form-control-sm" name="end_date"
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Format</label>
                                <select class="form-control form-control-sm" name="format">
                                    <option value="csv">CSV (ZIP with multiple files)</option>
                                    <option value="json">JSON (Single file)</option>
                                    <option value="excel">Excel (ZIP with multiple files)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-download mr-2"></i>Export Complete System Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Export Buttons -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-2"></i>Quick Export
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="btn btn-outline-primary btn-block" onclick="quickExport('users', 'csv')">
                            <i class="fas fa-users mr-2"></i>Users CSV
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="btn btn-outline-info btn-block" onclick="quickExport('devices', 'csv')">
                            <i class="fas fa-microchip mr-2"></i>Devices CSV
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="btn btn-outline-warning btn-block" onclick="quickExport('earthquake-events', 'csv')">
                            <i class="fas fa-earthquake mr-2"></i>Events CSV
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <button class="btn btn-outline-success btn-block" onclick="quickExport('device-logs', 'csv')">
                            <i class="fas fa-history mr-2"></i>Logs CSV
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-danger btn-block" onclick="exportTodayEvents()">
                            <i class="fas fa-calendar-day mr-2"></i>Today's Events
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-secondary btn-block" onclick="exportActiveDevices()">
                            <i class="fas fa-wifi mr-2"></i>Active Devices
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-dark btn-block" onclick="exportWarningEvents()">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Warning Events
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export History (Placeholder) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Recent Exports
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-file-export fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No export history</h5>
                    <p class="text-gray-500">Your export activities will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .checkbox-group {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 4px;
        padding: 10px;
    }

    .form-check {
        margin-bottom: 5px;
    }

    .card {
        transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .btn-outline-primary, .btn-outline-info, .btn-outline-warning,
    .btn-outline-success, .btn-outline-danger, .btn-outline-secondary,
    .btn-outline-dark {
        transition: all 0.3s;
    }

    .btn-outline-primary:hover, .btn-outline-info:hover, .btn-outline-warning:hover,
    .btn-outline-success:hover, .btn-outline-danger:hover, .btn-outline-secondary:hover,
    .btn-outline-dark:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Set default dates based on report type
    document.getElementById('reportType').addEventListener('change', function() {
        const reportType = this.value;
        const today = new Date();
        const startDate = document.getElementById('statsStartDate');
        const endDate = document.getElementById('statsEndDate');

        endDate.value = today.toISOString().split('T')[0];

        switch(reportType) {
            case 'daily':
                startDate.value = new Date(today.setDate(today.getDate() - 30)).toISOString().split('T')[0];
                break;
            case 'weekly':
                startDate.value = new Date(today.setDate(today.getDate() - 90)).toISOString().split('T')[0];
                break;
            case 'monthly':
                startDate.value = new Date(today.setMonth(today.getMonth() - 12)).toISOString().split('T')[0];
                break;
            case 'yearly':
                startDate.value = new Date(today.setFullYear(today.getFullYear() - 5)).toISOString().split('T')[0];
                break;
        }
    });

    // Quick export functions
    function quickExport(type, format) {
        let url = '';
        const today = new Date().toISOString().split('T')[0];
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
        const oneMonthAgoStr = oneMonthAgo.toISOString().split('T')[0];

        switch(type) {
            case 'users':
                url = `/exports/users?start_date=${oneMonthAgoStr}&end_date=${today}&format=${format}`;
                break;
            case 'devices':
                url = `/exports/devices?start_date=${oneMonthAgoStr}&end_date=${today}&format=${format}`;
                break;
            case 'earthquake-events':
                url = `/exports/earthquake-events?start_date=${oneMonthAgoStr}&end_date=${today}&format=${format}`;
                break;
            case 'device-logs':
                url = `/exports/device-logs?start_date=${oneMonthAgoStr}&end_date=${today}&format=${format}`;
                break;
        }

        window.open(url, '_blank');
    }

    function exportTodayEvents() {
        const today = new Date().toISOString().split('T')[0];
        const url = `/exports/earthquake-events?start_date=${today}&end_date=${today}&format=csv`;
        window.open(url, '_blank');
    }

    function exportActiveDevices() {
        const url = `/exports/devices?status=aktif&format=csv`;
        window.open(url, '_blank');
    }

    function exportWarningEvents() {
        const today = new Date().toISOString().split('T')[0];
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
        const oneMonthAgoStr = oneMonthAgo.toISOString().split('T')[0];

        const url = `/exports/earthquake-events?start_date=${oneMonthAgoStr}&end_date=${today}&status=warning&format=csv`;
        window.open(url, '_blank');
    }

    // Form validation and loading indicators
    const forms = ['exportUsersForm', 'exportDevicesForm', 'exportEventsForm', 'exportLogsForm', 'exportStatsForm', 'exportAllForm'];

    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Preparing Export...';
                submitBtn.disabled = true;

                // Show loading message
                Swal.fire({
                    title: 'Preparing Export',
                    text: 'Please wait while we prepare your data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // The download will happen in new tab, so we can reset button after delay
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    Swal.close();
                }, 3000);
            });
        }
    });

    // Check all checkboxes for full export
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                // At least one must be checked
                const checkedCount = document.querySelectorAll('.form-check-input:checked').length;
                if (checkedCount === 0) {
                    this.checked = true;
                    Swal.fire('Info', 'At least one data type must be selected for export.', 'info');
                }
            }
        });
    });
</script>
@endpush
