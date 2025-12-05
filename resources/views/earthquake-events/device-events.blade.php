@extends('layouts.app')

@section('title', 'Device Events - EQMonitor')
@section('page-title', 'Earthquake Events for: ' . $device->nama_device)

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('devices.show', $device) }}" class="btn btn-info">
            <i class="fas fa-microchip mr-2"></i>Device Details
        </a>
        <a href="{{ route('earthquake-events.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-2"></i>All Events
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-earthquake mr-2"></i>
                    Earthquake Events for {{ $device->nama_device }}
                </h6>
                <span class="badge badge-{{ $device->status_color }}">
                    {{ ucfirst($device->status) }}
                </span>
            </div>
            <div class="card-body">
                <!-- Device Info -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h5>{{ $device->nama_device }}</h5>
                        <p class="text-muted">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            {{ $device->lokasi }}
                        </p>
                        <p class="text-muted">
                            <i class="fas fa-fingerprint mr-2"></i>
                            UUID: <code>{{ $device->uuid }}</code>
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <small class="text-muted">Last Seen:</small>
                                <div class="font-weight-bold">
                                    @if($device->last_seen)
                                        {{ $device->last_seen->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($events->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-earthquake fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No earthquake events for this device</h5>
                        <p class="text-gray-500">This device hasn't recorded any earthquake events yet</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered data-table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Magnitude</th>
                                    <th>Status</th>
                                    <th>Occurred At</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $event->magnitude_color }} p-2">
                                            {{ number_format($event->magnitude, 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $event->status_color }} p-2">
                                            <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} mr-1"></i>
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $event->occurred_at->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $event->occurred_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($event->hasLocation())
                                            <div class="text-success">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <small>Lat: {{ number_format($event->latitude, 4) }}</small><br>
                                                <small>Lng: {{ number_format($event->longitude, 4) }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('earthquake-events.show', $event) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('earthquake-events.edit', $event) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
