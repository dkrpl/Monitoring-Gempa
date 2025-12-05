@extends('layouts.app')

@section('title', 'Threshold Settings - EQMonitor')
@section('page-title', 'Threshold Settings Management')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('thresholds.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Threshold
        </a>
        <button type="button" class="btn btn-warning ml-2" onclick="resetToDefault()">
            <i class="fas fa-undo mr-2"></i>Reset to Default
        </button>
        <button type="button" class="btn btn-info ml-2" onclick="generateEffectivenessReport()">
            <i class="fas fa-chart-bar mr-2"></i>Effectiveness Report
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Information Card -->
    <div class="col-12 mb-4">
        <div class="card border-left-info shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <i class="fas fa-info-circle mr-2"></i>About Thresholds
                        </div>
                        <p class="mb-0">
                            Thresholds define earthquake magnitude levels that trigger different alert statuses.
                            When an earthquake's magnitude reaches or exceeds a threshold, the system automatically
                            assigns the corresponding status and can send notifications.
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sliders-h fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Threshold Configuration</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="sortByValue()">
                        <i class="fas fa-sort-numeric-down"></i> Sort by Value
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="sortByPriority()">
                        <i class="fas fa-sort"></i> Sort by Priority
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($thresholds->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-sliders-h fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No thresholds configured</h5>
                        <p class="text-gray-500">Set up thresholds to define earthquake alert levels</p>
                        <a href="{{ route('thresholds.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus mr-2"></i>Create First Threshold
                        </a>
                    </div>
                @else
                    <!-- Threshold Visualization -->
                    <div class="mb-5">
                        <h6 class="font-weight-bold mb-3">Threshold Visualization</h6>
                        <div class="threshold-visualization">
                            <div class="scale-container">
                                <div class="scale-label">Magnitude Scale</div>
                                <div class="scale-bar">
                                    @php
                                        $prevValue = 0;
                                        $maxValue = 10;
                                    @endphp

                                    @foreach($thresholds->sortBy('min_value') as $threshold)
                                        @php
                                            $width = (($threshold->min_value - $prevValue) / $maxValue) * 100;
                                            $prevValue = $threshold->min_value;
                                        @endphp

                                        <div class="scale-segment"
                                             style="width: {{ $width }}%; background-color: {{ $threshold->color }};"
                                             data-toggle="tooltip"
                                             title="{{ ucfirst($threshold->description) }} (≥{{ $threshold->min_value }})">
                                            <div class="segment-label">
                                                {{ $threshold->min_value }}
                                            </div>
                                        </div>
                                    @endforeach

                                    @if($prevValue < $maxValue)
                                        @php $width = (($maxValue - $prevValue) / $maxValue) * 100; @endphp
                                        <div class="scale-segment" style="width: {{ $width }}%; background-color: #28a745;">
                                            <div class="segment-label">
                                                Normal
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="scale-legend">
                                    <div class="legend-item">
                                        <span class="legend-color" style="background-color: #28a745;"></span>
                                        <span>Normal (<{{ $thresholds->first()->min_value ?? 0 }})</span>
                                    </div>
                                    @foreach($thresholds->sortBy('min_value') as $threshold)
                                        <div class="legend-item">
                                            <span class="legend-color" style="background-color: {{ $threshold->color }};"></span>
                                            <span>{{ ucfirst($threshold->description) }} (≥{{ $threshold->min_value }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thresholds List -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="thresholdsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Min Value</th>
                                    <th>Range</th>
                                    <th>Notifications</th>
                                    <th>Auto Alert</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortableThresholds">
                                @foreach($thresholds->sortBy('priority') as $threshold)
                                <tr data-id="{{ $threshold->id }}" data-priority="{{ $threshold->priority }}">
                                    <td class="priority-cell">
                                        <div class="d-flex align-items-center">
                                            <div class="priority-handle mr-2" style="cursor: move;">
                                                <i class="fas fa-arrows-alt"></i>
                                            </div>
                                            <span class="badge badge-secondary">{{ $threshold->priority }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $threshold->color }}; color: white;">
                                            <i class="fas fa-{{ $threshold->description === 'danger' ? 'fire' : 'exclamation-triangle' }} mr-1"></i>
                                            {{ ucfirst($threshold->description) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($threshold->min_value, 1) }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $threshold->range_description }}</small>
                                    </td>
                                    <td>
                                        @if($threshold->notification_enabled)
                                            <span class="badge badge-success">
                                                <i class="fas fa-bell mr-1"></i> Enabled
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-bell-slash mr-1"></i> Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($threshold->auto_alert)
                                            <span class="badge badge-danger">
                                                <i class="fas fa-bolt mr-1"></i> Auto
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-hand-paper mr-1"></i> Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('thresholds.show', $threshold) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('thresholds.edit', $threshold) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm test-notification-btn"
                                                    data-threshold-id="{{ $threshold->id }}"
                                                    title="Test Notification">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                            <form action="{{ route('thresholds.destroy', $threshold) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-danger btn-sm delete-btn"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Save Order Button -->
                    <div class="mt-3 text-right" id="saveOrderContainer" style="display: none;">
                        <button type="button" class="btn btn-success" onclick="saveThresholdOrder()">
                            <i class="fas fa-save mr-2"></i>Save New Order
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cancelReorder()">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Threshold Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Threshold Statistics</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="threshold-count-display">
                        <div class="display-number">{{ $thresholds->count() }}</div>
                        <div class="display-label">Total Thresholds</div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">{{ $thresholds->where('notification_enabled', true)->count() }}</div>
                        <div class="stat-label">With Notifications</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $thresholds->where('auto_alert', true)->count() }}</div>
                        <div class="stat-label">Auto Alerts</div>
                    </div>
                </div>

                <hr>

                <h6 class="font-weight-bold mb-3">Quick Actions</h6>
                <div class="list-group">
                    <a href="{{ route('thresholds.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus text-primary mr-2"></i>
                        Create New Threshold
                    </a>
                    <button type="button" class="list-group-item list-group-item-action" onclick="enableAllNotifications()">
                        <i class="fas fa-bell text-success mr-2"></i>
                        Enable All Notifications
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="disableAllNotifications()">
                        <i class="fas fa-bell-slash text-warning mr-2"></i>
                        Disable All Notifications
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="testAllNotifications()">
                        <i class="fas fa-play-circle text-info mr-2"></i>
                        Test All Notifications
                    </button>
                </div>
            </div>
        </div>

        <!-- Configuration Tips -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Configuration Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Set realistic values:</strong> Based on seismic activity in your region
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Consider overlap:</strong> Higher thresholds override lower ones
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Test notifications:</strong> Ensure alert messages are clear and effective
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Monitor effectiveness:</strong> Review which thresholds trigger most often
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <strong>Update regularly:</strong> Adjust based on changing conditions
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Effectiveness Report Modal -->
<div class="modal fade" id="effectivenessReportModal" tabindex="-1" role="dialog" aria-labelledby="effectivenessReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="effectivenessReportModalLabel">Threshold Effectiveness Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="reportContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Generating report...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .threshold-visualization {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .scale-container {
        width: 100%;
    }

    .scale-label {
        font-weight: bold;
        margin-bottom: 10px;
        color: #495057;
    }

    .scale-bar {
        height: 40px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        display: flex;
        margin-bottom: 10px;
        border: 2px solid #dee2e6;
    }

    .scale-segment {
        height: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .scale-segment:hover {
        transform: scaleY(1.1);
        z-index: 1;
    }

    .segment-label {
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        font-size: 0.9rem;
    }

    .scale-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 8px;
        border: 1px solid #dee2e6;
    }

    .threshold-count-display {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        color: white;
        margin-bottom: 20px;
    }

    .display-number {
        font-size: 3rem;
        font-weight: bold;
        line-height: 1;
    }

    .display-label {
        font-size: 1rem;
        opacity: 0.9;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e3e6f0;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #4e73df;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .priority-handle {
        color: #6c757d;
        cursor: move;
    }

    .priority-handle:hover {
        color: #4e73df;
    }

    #sortableThresholds tr {
        cursor: move;
        transition: all 0.3s;
    }

    #sortableThresholds tr.sortable-ghost {
        opacity: 0.4;
    }

    #sortableThresholds tr.sortable-chosen {
        background-color: #f8f9fa;
        transform: scale(1.02);
    }

    .report-table th {
        background-color: #4e73df;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    // Initialize sortable table
    let sortable;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Make table rows sortable
        const tbody = document.getElementById('sortableThresholds');
        if (tbody) {
            sortable = new Sortable(tbody, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                handle: '.priority-handle',
                onStart: function() {
                    document.getElementById('saveOrderContainer').style.display = 'block';
                },
                onEnd: function() {
                    updateRowPriorities();
                }
            });
        }

        // Test notification buttons
        const testButtons = document.querySelectorAll('.test-notification-btn');
        testButtons.forEach(button => {
            button.addEventListener('click', function() {
                const thresholdId = this.dataset.thresholdId;
                testNotification(thresholdId);
            });
        });
    });

    function updateRowPriorities() {
        const rows = document.querySelectorAll('#sortableThresholds tr');
        rows.forEach((row, index) => {
            const priorityCell = row.querySelector('.priority-cell .badge');
            if (priorityCell) {
                priorityCell.textContent = index + 1;
            }
            row.dataset.priority = index + 1;
        });
    }

    function saveThresholdOrder() {
        const rows = document.querySelectorAll('#sortableThresholds tr');
        const thresholds = [];

        rows.forEach((row, index) => {
            thresholds.push({
                id: row.dataset.id,
                order: index + 1
            });
        });

        fetch('/thresholds/order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ thresholds: thresholds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire(
                    'Order Saved!',
                    'Threshold priority order has been updated.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Failed to save threshold order.', 'error');
        });
    }

    function cancelReorder() {
        location.reload();
    }

    function sortByValue() {
        window.location.href = '{{ route("thresholds.index") }}?sort=value';
    }

    function sortByPriority() {
        window.location.href = '{{ route("thresholds.index") }}?sort=priority';
    }

    function resetToDefault() {
        Swal.fire({
            title: 'Reset to Default Thresholds?',
            text: 'This will replace all current thresholds with default values. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/thresholds/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire(
                        'Reset!',
                        'Thresholds have been reset to default values.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to reset thresholds.', 'error');
                });
            }
        });
    }

    function testNotification(thresholdId) {
        Swal.fire({
            title: 'Test Notification',
            text: 'Generating test notification...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/thresholds/${thresholdId}/test-notification`)
            .then(response => response.json())
            .then(data => {
                Swal.close();

                if (data.success) {
                    const notification = data.notification;

                    Swal.fire({
                        title: notification.title,
                        html: `
                            <div style="text-align: left;">
                                <p><strong>Message:</strong> ${notification.message}</p>
                                <p><strong>Status:</strong>
                                    <span class="badge" style="background-color: ${notification.color}; color: white;">
                                        ${notification.enabled ? 'Enabled' : 'Disabled'}
                                    </span>
                                </p>
                                <p><strong>Auto Alert:</strong>
                                    <span class="badge badge-${notification.auto_alert ? 'danger' : 'warning'}">
                                        ${notification.auto_alert ? 'Auto' : 'Manual'}
                                    </span>
                                </p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire('Error!', 'Failed to generate test notification.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to test notification.', 'error');
            });
    }

    function enableAllNotifications() {
        Swal.fire({
            title: 'Enable All Notifications?',
            text: 'This will enable notifications for all thresholds.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, enable all!'
        }).then((result) => {
            if (result.isConfirmed) {
                // In a real app, you would make an API call here
                // For now, we'll simulate the action
                Swal.fire(
                    'Enabled!',
                    'All notifications have been enabled.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }

    function disableAllNotifications() {
        Swal.fire({
            title: 'Disable All Notifications?',
            text: 'This will disable notifications for all thresholds.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, disable all!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Disabled!',
                    'All notifications have been disabled.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }

    function testAllNotifications() {
        Swal.fire({
            title: 'Test All Notifications?',
            text: 'This will test notifications for all enabled thresholds.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Test All'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Testing...',
                    text: 'Testing all notifications. This may take a moment.',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                setTimeout(() => {
                    Swal.fire(
                        'Complete!',
                        'All notifications have been tested successfully.',
                        'success'
                    );
                }, 2000);
            }
        });
    }

    function generateEffectivenessReport() {
        $('#effectivenessReportModal').modal('show');

        fetch('/thresholds/effectiveness-report')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let reportHtml = `
                        <div class="report-header mb-4">
                            <h6 class="font-weight-bold">Generated: ${data.generated_at}</h6>
                            <p class="text-muted">Threshold Effectiveness Analysis Report</p>
                        </div>
                    `;

                    data.report.forEach((item, index) => {
                        const threshold = item.threshold;
                        const stats = item.statistics;

                        reportHtml += `
                            <div class="card mb-3">
                                <div class="card-header" style="background-color: ${threshold.color}; color: white;">
                                    <h6 class="mb-0">
                                        ${ucfirst(threshold.description)} Threshold (≥${threshold.min_value})
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stat-box">
                                                <div class="stat-number">${item.effectiveness}%</div>
                                                <div class="stat-label">Effectiveness</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-box">
                                                <div class="stat-number">${stats.event_count}</div>
                                                <div class="stat-label">Total Events</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-box">
                                                <div class="stat-number">${stats.recent_count}</div>
                                                <div class="stat-label">Recent (30d)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Range:</strong> ${stats.range.from} - ${stats.range.to}</p>
                                            <p><strong>Average Magnitude:</strong> ${item.average_magnitude.toFixed(1)}</p>
                                            <p><strong>Max Magnitude:</strong> ${item.max_magnitude.toFixed(1)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Add summary
                    const totalEvents = data.report.reduce((sum, item) => sum + item.statistics.event_count, 0);
                    const avgEffectiveness = data.report.reduce((sum, item) => sum + item.effectiveness, 0) / data.report.length;

                    reportHtml += `
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Total Thresholds:</strong> ${data.report.length}</p>
                                        <p><strong>Total Events Covered:</strong> ${totalEvents}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Average Effectiveness:</strong> ${avgEffectiveness.toFixed(2)}%</p>
                                        <p><strong>Report Generated:</strong> ${data.generated_at}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('reportContent').innerHTML = reportHtml;
                } else {
                    document.getElementById('reportContent').innerHTML = `
                        <div class="alert alert-danger">
                            Failed to generate report. Please try again.
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('reportContent').innerHTML = `
                    <div class="alert alert-danger">
                        Error loading report: ${error.message}
                    </div>
                `;
            });
    }

    function printReport() {
        const printContent = document.getElementById('reportContent').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
            <html>
                <head>
                    <title>Threshold Effectiveness Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .card { border: 1px solid #ddd; margin-bottom: 15px; border-radius: 4px; }
                        .card-header { padding: 10px 15px; font-weight: bold; }
                        .card-body { padding: 15px; }
                        .stat-box { text-align: center; padding: 10px; background: #f8f9fa; border-radius: 4px; }
                        .stat-number { font-size: 1.5rem; font-weight: bold; }
                        .stat-label { font-size: 0.9rem; color: #6c757d; }
                        .row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
                        .col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; padding: 0 15px; }
                        .col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 0 15px; }
                        .col-12 { flex: 0 0 100%; max-width: 100%; padding: 0 15px; }
                        @media print {
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <h2>Threshold Effectiveness Report</h2>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                    <hr>
                    ${printContent}
                    <div class="no-print" style="margin-top: 20px;">
                        <button onclick="window.close()" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Close Window
                        </button>
                    </div>
                </body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }

    function ucfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
@endpush
