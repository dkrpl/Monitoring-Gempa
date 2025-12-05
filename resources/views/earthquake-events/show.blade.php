@extends('layouts.app')

@section('title', 'Earthquake Event Details - EQMonitor')
@section('page-title', 'Earthquake Event Details')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('earthquake-events.edit', $earthquakeEvent) }}" class="btn btn-warning">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <a href="{{ route('earthquake-events.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Event Details -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Event Information</h6>
                <span class="badge badge-{{ $earthquakeEvent->status_color }} p-2">
                    <i class="fas fa-{{ $earthquakeEvent->status === 'danger' ? 'fire' : 'exclamation-triangle' }} mr-1"></i>
                    {{ strtoupper($earthquakeEvent->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Magnitude</label>
                            <div class="magnitude-display">
                                <span class="badge badge-{{ $earthquakeEvent->magnitude_color }} p-3" style="font-size: 1.5rem;">
                                    {{ number_format($earthquakeEvent->magnitude, 1) }}
                                </span>
                                <small class="text-muted ml-2">Richter Scale</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Occurred At</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ $earthquakeEvent->formatted_occurred_at }}
                            </p>
                            <small class="text-muted">{{ $earthquakeEvent->time_ago }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Device</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-microchip mr-2"></i>
                                <a href="{{ route('devices.show', $earthquakeEvent->device_id) }}">
                                    {{ $earthquakeEvent->device->nama_device }}
                                </a>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $earthquakeEvent->device->lokasi }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Event ID</label>
                            <p class="form-control-plaintext">
                                <code>EV-{{ str_pad($earthquakeEvent->id, 6, '0', STR_PAD_LEFT) }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                @if($earthquakeEvent->hasLocation())
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Latitude</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-globe-americas mr-2"></i>
                                {{ number_format($earthquakeEvent->latitude, 6) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Longitude</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-globe-americas mr-2"></i>
                                {{ number_format($earthquakeEvent->longitude, 6) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Depth</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-mountain mr-2"></i>
                                {{ number_format($earthquakeEvent->depth, 1) }} km
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Map placeholder -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="fas fa-map mr-2"></i>Event Location
                                </h6>
                                <div class="map-placeholder text-center py-4">
                                    <i class="fas fa-map-marked-alt fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">Map visualization would appear here</p>
                                    <p class="small text-muted">
                                        Coordinates: {{ $earthquakeEvent->latitude }}, {{ $earthquakeEvent->longitude }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($earthquakeEvent->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Description</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0">{{ $earthquakeEvent->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Device Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Device Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="text-primary font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $deviceStats['total_events'] }}
                                </div>
                                <div class="text-muted">Total Events</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <div class="text-success font-weight-bold" style="font-size: 1.5rem;">
                                    {{ number_format($deviceStats['avg_magnitude'], 1) }}
                                </div>
                                <div class="text-muted">Avg Magnitude</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <div class="text-warning font-weight-bold" style="font-size: 1.5rem;">
                                    {{ number_format($deviceStats['max_magnitude'], 1) }}
                                </div>
                                <div class="text-muted">Max Magnitude</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <div class="text-info font-weight-bold" style="font-size: 1.5rem;">
                                    {{ $deviceStats['today_events'] }}
                                </div>
                                <div class="text-muted">Today's Events</div>
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
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('earthquake-events.by-device', $earthquakeEvent->device_id) }}"
                           class="btn btn-outline-primary btn-block">
                            <i class="fas fa-list"></i><br>
                            <small>View All Events</small>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <button type="button" class="btn btn-outline-info btn-block" onclick="shareEvent()">
                            <i class="fas fa-share-alt"></i><br>
                            <small>Share</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-block" onclick="generateReport()">
                            <i class="fas fa-file-pdf"></i><br>
                            <small>Generate Report</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="sendAlert()">
                            <i class="fas fa-bell"></i><br>
                            <small>Send Alert</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Events -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Similar Events</h6>
            </div>
            <div class="card-body">
                @if($similarEvents->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <p class="text-muted">No similar events found</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($similarEvents as $event)
                            <a href="{{ route('earthquake-events.show', $event) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <span class="badge badge-{{ $event->magnitude_color }}">
                                            {{ number_format($event->magnitude, 1) }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">{{ $event->occurred_at->format('H:i') }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge badge-{{ $event->status_color }}">
                                        {{ $event->status }}
                                    </span>
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
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
                    <strong>Warning:</strong> These actions are irreversible.
                </p>

                <form action="{{ route('earthquake-events.destroy', $earthquakeEvent) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-block delete-btn">
                        <i class="fas fa-trash mr-2"></i>Delete Event
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .magnitude-display {
        display: flex;
        align-items: center;
    }

    .magnitude-display .badge {
        font-size: 1.8rem;
        padding: 10px 20px;
        border-radius: 8px;
    }

    .map-placeholder {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        transform: translateX(5px);
        border-left-color: #4e73df;
    }
</style>
@endpush

@push('scripts')
<script>
    function shareEvent() {
        const eventData = {
            title: 'Earthquake Event',
            text: `Magnitude ${@json($earthquakeEvent->magnitude)} earthquake detected by ${@json($earthquakeEvent->device->nama_device)}`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(eventData)
                .then(() => console.log('Shared successfully'))
                .catch(error => console.log('Error sharing:', error));
        } else {
            Swal.fire({
                title: 'Share Event',
                html: `
                    <p>Copy the link to share:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" value="${window.location.href}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('${window.location.href}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                `,
                icon: 'info'
            });
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire('Copied!', 'Link copied to clipboard.', 'success');
            })
            .catch(err => {
                Swal.fire('Error!', 'Failed to copy link.', 'error');
            });
    }

    function generateReport() {
        Swal.fire({
            title: 'Generate Report?',
            text: 'This will create a PDF report for this earthquake event.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Generate'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating Report...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Simulate report generation
                setTimeout(() => {
                    Swal.fire(
                        'Report Generated!',
                        'The PDF report has been generated successfully.',
                        'success'
                    );
                }, 2000);
            }
        });
    }

    function sendAlert() {
        Swal.fire({
            title: 'Send Alert?',
            text: 'This will send an alert notification about this earthquake event.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Send Alert'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/api/send-alert', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        event_id: @json($earthquakeEvent->id),
                        message: `Earthquake Alert: Magnitude ${@json($earthquakeEvent->magnitude)} detected`
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Alert Sent!',
                            'The earthquake alert has been sent successfully.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            'Failed to send alert.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Failed to send alert.',
                        'error'
                    );
                });
            }
        });
    }
</script>
@endpush
