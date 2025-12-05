@extends('layouts.app')

@section('title', 'Threshold Details - EQMonitor')
@section('page-title', 'Threshold Details: ' . ucfirst($threshold->description))

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('thresholds.edit', $threshold) }}" class="btn btn-warning">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <button type="button" class="btn btn-primary ml-2" onclick="testNotification({{ $threshold->id }})">
            <i class="fas fa-bell mr-2"></i>Test Notification
        </button>
        <a href="{{ route('thresholds.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Thresholds
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Threshold Details -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background-color: {{ $threshold->color }}; color: white;">
                <h6 class="m-0 font-weight-bold">Threshold Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 text-center">
                        <div class="threshold-display-large mb-3">
                            <span class="badge" style="background-color: {{ $threshold->color }}; color: white; font-size: 1.5rem; padding: 15px 30px; border-radius: 25px;">
                                {{ strtoupper($threshold->description) }}
                            </span>
                        </div>
                        <h3 class="font-weight-bold">{{ $threshold->formatted_min_value }}</h3>
                        <p class="text-muted">Minimum Magnitude</p>
                    </div>

                    <div class="col-md-6">
                        <div class="threshold-stats">
                            <div class="stat-card">
                                <div class="stat-number">{{ $stats['event_count'] }}</div>
                                <div class="stat-label">Total Events</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">{{ $stats['recent_count'] }}</div>
                                <div class="stat-label">Recent (30d)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Priority</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-secondary">{{ $threshold->priority }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Magnitude Range</label>
                            <p class="form-control-plaintext">
                                {{ number_format($stats['range']['from'], 1) }} - {{ number_format($stats['range']['to'], 1) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Notification Status</label>
                            <p class="form-control-plaintext">
                                @if($threshold->notification_enabled)
                                    <span class="badge badge-success">
                                        <i class="fas fa-bell mr-1"></i> Enabled
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-bell-slash mr-1"></i> Disabled
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Alert Mode</label>
                            <p class="form-control-plaintext">
                                @if($threshold->auto_alert)
                                    <span class="badge badge-danger">
                                        <i class="fas fa-bolt mr-1"></i> Auto-Alert
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-hand-paper mr-1"></i> Manual
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($threshold->notification_message)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Notification Message Template</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0">{{ $threshold->notification_message }}</p>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Placeholders: {magnitude}, {location}, {device}, {time}
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Created At</label>
                            <p class="form-control-plaintext">{{ $threshold->created_at->format('F d, Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Last Updated</label>
                            <p class="form-control-plaintext">{{ $threshold->updated_at->format('F d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Events in this Range</h6>
            </div>
            <div class="card-body">
                @if($stats['events']->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <p class="text-muted">No earthquake events in this range</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Magnitude</th>
                                    <th>Device</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['events'] as $event)
                                <tr>
                                    <td>{{ $event->occurred_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $event->magnitude_color }}">
                                            {{ number_format($event->magnitude, 1) }}
                                        </span>
                                    </td>
                                    <td>{{ $event->device->nama_device }}</td>
                                    <td>{{ $event->device->lokasi }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('earthquake-events.index') }}?min_magnitude={{ $stats['range']['from'] }}&max_magnitude={{ $stats['range']['to'] }}"
                           class="btn btn-sm btn-outline-primary">
                            View All Events in Range
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Neighbor Thresholds -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Neighboring Thresholds</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="fas fa-arrow-down mr-2"></i>Lower Threshold
                                </h6>
                                @if($stats['next_lower'])
                                    <p class="mb-1">
                                        <strong>{{ ucfirst($stats['next_lower']->description) }}</strong>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Magnitude:</strong> {{ $stats['next_lower']->formatted_min_value }}
                                    </p>
                                    <a href="{{ route('thresholds.show', $stats['next_lower']) }}" class="btn btn-sm btn-outline-warning mt-2">
                                        View Details
                                    </a>
                                @else
                                    <p class="text-muted mb-0">No lower threshold</p>
                                    <small>(This is the lowest threshold)</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-danger h-100">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="fas fa-arrow-up mr-2"></i>Higher Threshold
                                </h6>
                                @if($stats['next_higher'])
                                    <p class="mb-1">
                                        <strong>{{ ucfirst($stats['next_higher']->description) }}</strong>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Magnitude:</strong> {{ $stats['next_higher']->formatted_min_value }}
                                    </p>
                                    <a href="{{ route('thresholds.show', $stats['next_higher']) }}" class="btn btn-sm btn-outline-danger mt-2">
                                        View Details
                                    </a>
                                @else
                                    <p class="text-muted mb-0">No higher threshold</p>
                                    <small>(This is the highest threshold)</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action" onclick="toggleNotification({{ $threshold->id }})">
                        @if($threshold->notification_enabled)
                            <i class="fas fa-bell-slash text-warning mr-2"></i>
                            Disable Notifications
                        @else
                            <i class="fas fa-bell text-success mr-2"></i>
                            Enable Notifications
                        @endif
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="toggleAutoAlert({{ $threshold->id }})">
                        @if($threshold->auto_alert)
                            <i class="fas fa-hand-paper text-warning mr-2"></i>
                            Disable Auto-Alert
                        @else
                            <i class="fas fa-bolt text-danger mr-2"></i>
                            Enable Auto-Alert
                        @endif
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="increasePriority({{ $threshold->id }})">
                        <i class="fas fa-arrow-up text-primary mr-2"></i>
                        Increase Priority
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" onclick="decreasePriority({{ $threshold->id }})">
                        <i class="fas fa-arrow-down text-primary mr-2"></i>
                        Decrease Priority
                    </button>
                </div>
            </div>
        </div>

        <!-- Threshold Visualization -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Position in Scale</h6>
            </div>
            <div class="card-body">
                <div class="scale-visualization">
                    <div class="scale-axis">
                        <div class="axis-label">0.0</div>
                        <div class="axis-line"></div>
                        <div class="axis-label">10.0</div>
                    </div>
                    <div class="threshold-marker" style="left: {{ ($threshold->min_value / 10) * 100 }}%; background-color: {{ $threshold->color }};">
                        <div class="marker-label">{{ $threshold->formatted_min_value }}</div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">This threshold activates at magnitude {{ $threshold->formatted_min_value }}</small>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card shadow border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                </h6>
            </div>
            <div class="card-body">
                <p class="text-danger mb-4">
                    <strong>Warning:</strong> These actions are irreversible. Proceed with caution.
                </p>

                <form action="{{ route('thresholds.destroy', $threshold) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-block delete-btn">
                        <i class="fas fa-trash mr-2"></i>Delete Threshold
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .threshold-display-large .badge {
        box-shadow: 0 6px 10px rgba(0,0,0,0.15);
    }

    .threshold-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 20px;
    }

    .stat-card {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #4e73df;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .scale-visualization {
        position: relative;
        height: 60px;
        margin: 20px 0;
    }

    .scale-axis {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
        position: relative;
    }

    .axis-line {
        position: absolute;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #28a745, #ffc107, #e74a3b, #dc3545);
        border-radius: 2px;
        z-index: 1;
    }

    .axis-label {
        background: white;
        padding: 0 10px;
        z-index: 2;
        font-weight: bold;
        color: #495057;
    }

    .threshold-marker {
        position: absolute;
        top: -10px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transform: translateX(-50%);
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: 3px solid white;
    }

    .marker-label {
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .list-group-item {
        transition: all 0.3s;
    }

    .list-group-item:hover {
        transform: translateX(5px);
    }
</style>
@endpush

@push('scripts')
<script>
    function testNotification(thresholdId) {
        fetch(`/thresholds/${thresholdId}/test-notification`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notification = data.notification;

                    Swal.fire({
                        title: notification.title,
                        html: `
                            <div style="text-align: left;">
                                <div class="alert" style="background-color: ${notification.color}; color: white; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                                    <strong>Test Notification:</strong><br>
                                    ${notification.message}
                                </div>
                                <p><strong>Settings:</strong></p>
                                <ul>
                                    <li>Notifications: ${notification.enabled ? 'Enabled' : 'Disabled'}</li>
                                    <li>Auto-Alert: ${notification.auto_alert ? 'Enabled' : 'Disabled'}</li>
                                </ul>
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

    function toggleNotification(thresholdId) {
        Swal.fire({
            title: 'Toggle Notifications?',
            text: 'This will enable or disable notifications for this threshold.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, toggle!'
        }).then((result) => {
            if (result.isConfirmed) {
                // In a real app, make API call to toggle notification
                fetch(`/thresholds/${thresholdId}/toggle-notification`, {
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
                            'Updated!',
                            'Notification settings have been updated.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to update notification settings.', 'error');
                });
            }
        });
    }

    function toggleAutoAlert(thresholdId) {
        Swal.fire({
            title: 'Toggle Auto-Alert?',
            text: 'This will enable or disable auto-alert for this threshold.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, toggle!'
        }).then((result) => {
            if (result.isConfirmed) {
                // In a real app, make API call to toggle auto-alert
                Swal.fire(
                    'Updated!',
                    'Auto-alert settings have been updated.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }

    function increasePriority(thresholdId) {
        // In a real app, make API call to increase priority
        Swal.fire({
            title: 'Increase Priority?',
            text: 'This will make this threshold more important (lower priority number).',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, increase!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Updated!',
                    'Threshold priority has been increased.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }

    function decreasePriority(thresholdId) {
        // In a real app, make API call to decrease priority
        Swal.fire({
            title: 'Decrease Priority?',
            text: 'This will make this threshold less important (higher priority number).',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, decrease!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Updated!',
                    'Threshold priority has been decreased.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            }
        });
    }
</script>
@endpush
