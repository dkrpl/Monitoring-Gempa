@extends('layouts.app')

@section('title', 'Earthquake Event Details - EQMonitor')
@section('page-title', 'Earthquake Event Details')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('user.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
        <button type="button" class="btn btn-warning ml-2" onclick="showSafetyInstructions()">
            <i class="fas fa-shield-alt mr-2"></i>Safety Info
        </button>
        @if($event->latitude && $event->longitude)
        <button type="button" class="btn btn-success ml-2" onclick="viewOnMap()">
            <i class="fas fa-map mr-2"></i>View on Map
        </button>
        @endif
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Event Details -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Event Information</h6>
                <span class="badge badge-{{ $event->status === 'danger' ? 'danger' : 'warning' }} p-2">
                    <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} mr-1"></i>
                    {{ strtoupper($event->status) }} EVENT
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="magnitude-display">
                            <div class="rounded-circle bg-{{ $event->magnitude >= 5.0 ? 'danger' : ($event->magnitude >= 3.0 ? 'warning' : 'success') }} d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 120px; height: 120px;">
                                <span class="text-white" style="font-size: 2.5rem; font-weight: bold;">
                                    {{ number_format($event->magnitude, 1) }}
                                </span>
                            </div>
                            <h5>Magnitude</h5>
                            <p class="text-muted">Richter Scale</p>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Occurred At</label>
                                <p class="form-control-plaintext">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    {{ $event->occurred_at->format('F d, Y') }}
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $event->occurred_at->format('H:i:s') }}
                                    ({{ $event->occurred_at->diffForHumans() }})
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Device</label>
                                <p class="form-control-plaintext">
                                    <i class="fas fa-microchip mr-2"></i>
                                    {{ $event->device->nama_device }}
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $event->device->lokasi }}
                                </p>
                            </div>
                        </div>

                        @if($event->hasLocation())
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Latitude</label>
                                <p class="form-control-plaintext">
                                    <i class="fas fa-globe-americas mr-2"></i>
                                    {{ number_format($event->latitude, 6) }}
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Longitude</label>
                                <p class="form-control-plaintext">
                                    <i class="fas fa-globe-americas mr-2"></i>
                                    {{ number_format($event->longitude, 6) }}
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Depth</label>
                                <p class="form-control-plaintext">
                                    <i class="fas fa-mountain mr-2"></i>
                                    {{ $event->depth ? number_format($event->depth, 1) . ' km' : 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($event->description)
                        <div class="row">
                            <div class="col-12">
                                <label class="font-weight-bold">Description</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-0">{{ $event->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Safety Instructions -->
        <div class="card shadow mb-4 border-left-{{ $safetyInstructions['color'] }}">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-{{ $safetyInstructions['color'] }}">
                    <i class="fas fa-{{ $safetyInstructions['icon'] }} mr-2"></i>
                    {{ $safetyInstructions['title'] }}
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-{{ $safetyInstructions['color'] }}">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <strong>Immediate Actions Required:</strong>
                </div>
                <ul class="list-group">
                    @foreach($safetyInstructions['instructions'] as $instruction)
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check-circle text-{{ $safetyInstructions['color'] }} mr-3"></i>
                        {{ $instruction }}
                    </li>
                    @endforeach
                </ul>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-phone-alt mr-2"></i>Emergency Contacts
                                </h6>
                                <ul class="mb-0">
                                    <li><strong>National Emergency:</strong> 112</li>
                                    <li><strong>Police:</strong> 110</li>
                                    <li><strong>Ambulance:</strong> 119</li>
                                    <li><strong>Fire Department:</strong> 113</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-first-aid mr-2"></i>First Aid Tips
                                </h6>
                                <ul class="mb-0">
                                    <li>Check for injuries first</li>
                                    <li>Apply pressure to stop bleeding</li>
                                    <li>Keep injured person warm</li>
                                    <li>Don't move seriously injured</li>
                                </ul>
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
                        <button type="button" class="btn btn-outline-danger btn-block" onclick="shareEvent()">
                            <i class="fas fa-share-alt"></i><br>
                            <small>Share Alert</small>
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <button type="button" class="btn btn-outline-warning btn-block" onclick="printEvent()">
                            <i class="fas fa-print"></i><br>
                            <small>Print Info</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info btn-block" onclick="saveEvent()">
                            <i class="fas fa-save"></i><br>
                            <small>Save Details</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-tachometer-alt"></i><br>
                            <small>Dashboard</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Events -->
        @if($similarEvents->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Similar Events</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($similarEvents as $similarEvent)
                    <a href="{{ route('user.events.show', $similarEvent) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <span class="badge badge-{{ $similarEvent->magnitude >= 5.0 ? 'danger' : 'warning' }}">
                                    {{ number_format($similarEvent->magnitude, 1) }}
                                </span>
                            </h6>
                            <small class="text-muted">{{ $similarEvent->occurred_at->format('M d') }}</small>
                        </div>
                        <p class="mb-1">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $similarEvent->occurred_at->format('H:i') }}
                        </p>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Magnitude Scale -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Magnitude Scale</h6>
            </div>
            <div class="card-body">
                <div class="magnitude-scale">
                    <div class="scale-item mb-2">
                        <div class="d-flex justify-content-between">
                            <span>2.0-2.9</span>
                            <span class="badge badge-success">Minor</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: 20%"></div>
                        </div>
                    </div>
                    <div class="scale-item mb-2">
                        <div class="d-flex justify-content-between">
                            <span>3.0-3.9</span>
                            <span class="badge badge-warning">Light</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-warning" style="width: 30%"></div>
                        </div>
                    </div>
                    <div class="scale-item mb-2">
                        <div class="d-flex justify-content-between">
                            <span>4.0-4.9</span>
                            <span class="badge badge-warning">Moderate</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-warning" style="width: 40%"></div>
                        </div>
                    </div>
                    <div class="scale-item mb-2">
                        <div class="d-flex justify-content-between">
                            <span>5.0-5.9</span>
                            <span class="badge badge-danger">Strong</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-danger" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="scale-item">
                        <div class="d-flex justify-content-between">
                            <span>6.0+</span>
                            <span class="badge badge-danger">Major</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-danger" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        Current event:
                        <span class="badge badge-{{ $event->magnitude >= 5.0 ? 'danger' : 'warning' }}">
                            {{ number_format($event->magnitude, 1) }} -
                            {{ $event->magnitude >= 5.0 ? 'Strong' : ($event->magnitude >= 3.0 ? 'Light' : 'Minor') }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Modal -->
@if($event->hasLocation())
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Location</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="map-placeholder text-center py-5">
                    <i class="fas fa-map-marked-alt fa-4x text-primary mb-3"></i>
                    <h5>Map View</h5>
                    <p class="text-muted">Coordinates: {{ $event->latitude }}, {{ $event->longitude }}</p>
                    <div class="mt-3">
                        <a href="https://www.google.com/maps?q={{ $event->latitude }},{{ $event->longitude }}"
                           target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt mr-2"></i>Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .magnitude-display .rounded-circle {
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .list-group-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        transform: translateX(5px);
        border-left-color: #4e73df;
    }

    .scale-item .progress {
        background-color: #f0f0f0;
    }

    .map-placeholder {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script>
    function showSafetyInstructions() {
        Swal.fire({
            title: 'Earthquake Safety Instructions',
            html: `
                <div class="text-left">
                    <div class="alert alert-{{ $safetyInstructions['color'] }}">
                        <strong>{{ $safetyInstructions['title'] }}</strong>
                    </div>
                    <ol>
                        @foreach($safetyInstructions['instructions'] as $instruction)
                            <li>{{ $instruction }}</li>
                        @endforeach
                    </ol>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-phone-alt mr-2"></i>
                        <strong>Emergency Contacts:</strong> 112 (All emergency)
                    </div>
                </div>
            `,
            icon: '{{ $safetyInstructions['color'] }}',
            confirmButtonText: 'Understood',
            width: 600
        });
    }

    function viewOnMap() {
        @if($event->hasLocation())
            $('#mapModal').modal('show');
        @endif
    }

    function shareEvent() {
        const eventData = {
            title: 'Earthquake Alert - Magnitude {{ $event->magnitude }}',
            text: `Earthquake detected: Magnitude {{ $event->magnitude }} at {{ $event->device->lokasi }}. Stay safe!`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(eventData)
                .then(() => console.log('Shared successfully'))
                .catch(error => console.log('Error sharing:', error));
        } else {
            navigator.clipboard.writeText(`${eventData.text}\n${eventData.url}`)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Event details copied to clipboard.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to copy event details.',
                    });
                });
        }
    }

    function printEvent() {
        const printContent = `
            <html>
            <head>
                <title>Earthquake Event Details - EQMonitor</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .section { margin-bottom: 20px; }
                    .badge { padding: 5px 10px; border-radius: 4px; color: white; }
                    .badge-danger { background: #e74a3b; }
                    .badge-warning { background: #ffc107; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Earthquake Event Details</h1>
                    <p>Generated on ${new Date().toLocaleString()}</p>
                </div>

                <div class="section">
                    <h3>Event Information</h3>
                    <table>
                        <tr>
                            <td><strong>Event ID:</strong></td>
                            <td>EV-{{ str_pad($event->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Magnitude:</strong></td>
                            <td><span class="badge badge-{{ $event->magnitude >= 5.0 ? 'danger' : 'warning' }}">
                                {{ number_format($event->magnitude, 1) }} Richter
                            </span></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><span class="badge badge-{{ $event->status === 'danger' ? 'danger' : 'warning' }}">
                                {{ strtoupper($event->status) }}
                            </span></td>
                        </tr>
                        <tr>
                            <td><strong>Occurred At:</strong></td>
                            <td>{{ $event->occurred_at->format('F d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Device:</strong></td>
                            <td>{{ $event->device->nama_device }}</td>
                        </tr>
                        <tr>
                            <td><strong>Location:</strong></td>
                            <td>{{ $event->device->lokasi }}</td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <h3>Safety Instructions</h3>
                    <h4>{{ $safetyInstructions['title'] }}</h4>
                    <ul>
                        @foreach($safetyInstructions['instructions'] as $instruction)
                            <li>{{ $instruction }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="section">
                    <h3>Emergency Contacts</h3>
                    <ul>
                        <li><strong>National Emergency:</strong> 112</li>
                        <li><strong>Police:</strong> 110</li>
                        <li><strong>Ambulance:</strong> 119</li>
                        <li><strong>Fire Department:</strong> 113</li>
                    </ul>
                </div>

                <div class="footer">
                    <p><em>Generated by EQMonitor Earthquake Monitoring System</em></p>
                </div>
            </body>
            </html>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    function saveEvent() {
        const eventDetails = `
EARTHQUAKE EVENT DETAILS
========================
Event ID: EV-{{ str_pad($event->id, 6, '0', STR_PAD_LEFT) }}
Magnitude: {{ number_format($event->magnitude, 1) }} Richter
Status: {{ strtoupper($event->status) }}
Occurred: {{ $event->occurred_at->format('F d, Y H:i:s') }}
Device: {{ $event->device->nama_device }}
Location: {{ $event->device->lokasi }}
Coordinates: {{ $event->latitude ?? 'N/A' }}, {{ $event->longitude ?? 'N/A' }}
Depth: {{ $event->depth ? $event->depth . ' km' : 'N/A' }}

SAFETY INSTRUCTIONS
===================
{{ $safetyInstructions['title'] }}
@foreach($safetyInstructions['instructions'] as $instruction)
- {{ $instruction }}
@endforeach

EMERGENCY CONTACTS
==================
National Emergency: 112
Police: 110
Ambulance: 119
Fire Department: 113

Generated on: ${new Date().toLocaleString()}
Source: ${window.location.href}
        `.trim();

        navigator.clipboard.writeText(eventDetails)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Event details copied to clipboard.',
                    timer: 2000,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save event details.',
                });
            });
    }

    // Auto-scroll to safety instructions if event is dangerous
    document.addEventListener('DOMContentLoaded', function() {
        @if($event->status === 'danger')
            setTimeout(() => {
                document.querySelector('.border-left-danger').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 1000);
        @endif
    });
</script>
@endpush
