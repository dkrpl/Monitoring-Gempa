@extends('layouts.app')

@section('title', 'Earthquake Events - EQMonitor')
@section('page-title', 'Earthquake Events Monitoring')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('user.events.map') }}" class="btn btn-info">
            <i class="fas fa-map mr-2"></i>Event Map
        </a>
        <a href="{{ route('user.events.alerts') }}" class="btn btn-danger ml-2">
            <i class="fas fa-bell mr-2"></i>Active Alerts
        </a>
        <a href="{{ route('user.events.statistics') }}" class="btn btn-success ml-2">
            <i class="fas fa-chart-bar mr-2"></i>Statistics
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-earthquake fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Warning Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['warning'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Danger Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['danger'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-fire fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Today's Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Earthquake Events</h6>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Search events..." id="searchEvents">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="button" onclick="searchEvents()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($events->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-earthquake fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No earthquake events recorded</h5>
                        <p class="text-gray-500">All systems are currently normal</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered data-table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Time</th>
                                    <th>Device</th>
                                    <th>Magnitude</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                <tr>
                                    <td>EV-{{ str_pad($event->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <strong>{{ $event->occurred_at->format('M d, Y') }}</strong><br>
                                        <small class="text-muted">{{ $event->occurred_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <i class="fas fa-microchip text-primary mr-2"></i>
                                        {{ $event->device->nama_device }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $event->magnitude >= 5.0 ? 'danger' : ($event->magnitude >= 3.0 ? 'warning' : 'success') }} p-2">
                                            {{ number_format($event->magnitude, 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $event->status === 'danger' ? 'danger' : 'warning' }}">
                                            <i class="fas fa-{{ $event->status === 'danger' ? 'fire' : 'exclamation-triangle' }} mr-1"></i>
                                            {{ strtoupper($event->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($event->latitude && $event->longitude)
                                            <i class="fas fa-map-marker-alt text-success mr-1"></i>
                                            <small>{{ number_format($event->latitude, 4) }}, {{ number_format($event->longitude, 4) }}</small>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('user.events.show', $event) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-warning btn-sm"
                                                    onclick="showSafetyInstructions()"
                                                    title="Safety Instructions">
                                                <i class="fas fa-shield-alt"></i>
                                            </button>
                                            @if($event->latitude && $event->longitude)
                                            <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    onclick="viewOnMap({{ $event->latitude }}, {{ $event->longitude }})"
                                                    title="View on Map">
                                                <i class="fas fa-map"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{-- <div class="d-flex justify-content-center">
                        {{ $events->links() }}
                    </div> --}}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Safety Instructions Modal -->
<div class="modal fade" id="safetyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Earthquake Safety
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-lightbulb mr-2"></i>
                    <strong>Remember:</strong> Drop, Cover, and Hold On!
                </div>
                <ul>
                    <li>Stay calm and take cover immediately</li>
                    <li>Protect your head and neck</li>
                    <li>Stay away from windows and glass</li>
                    <li>If outdoors, move to open area</li>
                    <li>Follow official instructions</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); }
    .badge-danger { background: linear-gradient(45deg, #e74a3b, #d52a1e); }
    .badge-success { background: linear-gradient(45deg, #1cc88a, #13855c); }

    .btn-group .btn {
        margin-right: 2px;
        border-radius: 4px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .dataTable tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    function searchEvents() {
        const searchTerm = document.getElementById('searchEvents').value.toLowerCase();
        const rows = document.querySelectorAll('#dataTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    function showSafetyInstructions() {
        $('#safetyModal').modal('show');
    }

    function viewOnMap(latitude, longitude) {
        const url = `https://www.google.com/maps?q=${latitude},${longitude}`;
        window.open(url, '_blank');
    }

    // Make table rows clickable
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#dataTable tbody tr');
        rows.forEach(row => {
            const viewBtn = row.querySelector('a[href*="/events/"]');
            if (viewBtn) {
                row.style.cursor = 'pointer';
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('button') && !e.target.closest('a')) {
                        window.location.href = viewBtn.href;
                    }
                });
            }
        });

        // Enable search on enter
        document.getElementById('searchEvents').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchEvents();
            }
        });
    });
</script>
@endpush
