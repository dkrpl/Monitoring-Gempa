@extends('layouts.app')

@section('title', 'Earthquake Event Map - EQMonitor')
@section('page-title', 'Earthquake Events Map')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('user.events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Events
        </a>
        <button type="button" class="btn btn-info ml-2" onclick="refreshMap()">
            <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
        <button type="button" class="btn btn-primary ml-2" onclick="fitToMarkers()">
            <i class="fas fa-expand mr-2"></i>Fit View
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Earthquake Events Map</h6>
                <div class="d-flex">
                    <div class="mr-3">
                        <span class="badge badge-danger p-2">
                            <i class="fas fa-fire mr-1"></i> Danger (≥5.0)
                        </span>
                    </div>
                    <div>
                        <span class="badge badge-warning p-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Warning (≥3.0)
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Map Controls -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filterStatus">Filter by Status</label>
                            <select class="form-control" id="filterStatus" onchange="filterMarkers()">
                                <option value="all">All Events</option>
                                <option value="danger">Danger Only</option>
                                <option value="warning">Warning Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filterDate">Filter by Date</label>
                            <select class="form-control" id="filterDate" onchange="filterMarkers()">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">Last 7 Days</option>
                                <option value="month">Last 30 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="clusterMode">Marker Display</label>
                            <select class="form-control" id="clusterMode" onchange="toggleClustering()">
                                <option value="clustered">Clustered</option>
                                <option value="single">Single Markers</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="map-container" style="position: relative;">
                    <div id="map" style="height: 600px; border-radius: 8px; overflow: hidden;"></div>
                    <div class="map-overlay">
                        <div class="card">
                            <div class="card-body p-3">
                                <h6 class="mb-2">Map Legend</h6>
                                <div class="d-flex flex-wrap">
                                    <div class="mr-3 mb-2">
                                        <i class="fas fa-fire text-danger mr-1"></i>
                                        <small>Danger (≥5.0)</small>
                                    </div>
                                    <div class="mr-3 mb-2">
                                        <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                        <small>Warning (≥3.0)</small>
                                    </div>
                                    <div class="mr-3 mb-2">
                                        <i class="fas fa-circle text-primary mr-1"></i>
                                        <small>Cluster of events</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Events on Map</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalEvents">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Danger Events</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="dangerEvents">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-fire fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Warning Events</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="warningEvents">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Active Devices</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeDevices">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-microchip fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewDetailsBtn">View Full Details</button>
                <button type="button" class="btn btn-warning" onclick="showSafetyInstructions()">
                    <i class="fas fa-shield-alt mr-2"></i>Safety Info
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .map-container {
        position: relative;
    }

    .map-overlay {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 250px;
    }

    .leaflet-popup-content {
        min-width: 250px;
    }

    .event-marker {
        border-radius: 50%;
        text-align: center;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .danger-marker {
        background: linear-gradient(45deg, #e74a3b, #d52a1e);
        border: 3px solid #fff;
    }

    .warning-marker {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
        border: 3px solid #fff;
    }

    .cluster-marker {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: 3px solid #fff;
    }
</style>
@endpush

@push('scripts')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet Marker Cluster -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    let map;
    let markers = [];
    let markerCluster;
    let allEvents = @json($events);

    // Initialize map
    function initMap() {
        // Default center (Indonesia)
        const defaultCenter = [-2.5489, 118.0149];

        // Create map
        map = L.map('map').setView(defaultCenter, 5);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(map);

        // Initialize marker cluster
        markerCluster = L.markerClusterGroup({
            maxClusterRadius: 40,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        // Add events to map
        addEventsToMap(allEvents);

        // Update statistics
        updateStatistics(allEvents);
    }

    // Add events to map
    function addEventsToMap(events) {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        if (markerCluster) {
            markerCluster.clearLayers();
        }

        // Add each event as marker
        events.forEach(event => {
            const marker = createEventMarker(event);
            markers.push(marker);

            if (document.getElementById('clusterMode').value === 'clustered') {
                markerCluster.addLayer(marker);
            } else {
                marker.addTo(map);
            }
        });

        // Add cluster group to map if clustering enabled
        if (document.getElementById('clusterMode').value === 'clustered') {
            map.addLayer(markerCluster);
        }

        // Fit map to markers if we have any
        if (events.length > 0) {
            fitToMarkers();
        }
    }

    // Create event marker
    function createEventMarker(event) {
        const magnitude = parseFloat(event.magnitude);
        const status = event.status;
        const isDanger = status === 'danger';

        // Marker size based on magnitude
        const size = Math.max(30, Math.min(60, magnitude * 10));

        // Create custom icon
        const icon = L.divIcon({
            className: 'event-marker ' + (isDanger ? 'danger-marker' : 'warning-marker'),
            html: `<div style="width: ${size}px; height: ${size}px; font-size: ${size/3}px; line-height: ${size}px;">${magnitude.toFixed(1)}</div>`,
            iconSize: [size, size],
            iconAnchor: [size/2, size/2],
            popupAnchor: [0, -size/2]
        });

        // Create marker
        const marker = L.marker([event.latitude, event.longitude], { icon: icon });

        // Popup content
        const popupContent = `
            <div class="event-popup">
                <h6 class="font-weight-bold">
                    <i class="fas fa-${isDanger ? 'fire' : 'exclamation-triangle'} text-${isDanger ? 'danger' : 'warning'} mr-2"></i>
                    Earthquake Event
                </h6>
                <hr class="my-2">
                <p class="mb-1"><strong>Magnitude:</strong> <span class="badge badge-${isDanger ? 'danger' : 'warning'}">${magnitude.toFixed(1)}</span></p>
                <p class="mb-1"><strong>Status:</strong> <span class="badge badge-${isDanger ? 'danger' : 'warning'}">${status.toUpperCase()}</span></p>
                <p class="mb-1"><strong>Time:</strong> ${new Date(event.occurred_at).toLocaleString()}</p>
                <p class="mb-1"><strong>Device:</strong> ${event.device.nama_device}</p>
                <p class="mb-1"><strong>Location:</strong> ${event.device.lokasi}</p>
                ${event.depth ? `<p class="mb-1"><strong>Depth:</strong> ${event.depth} km</p>` : ''}
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-sm btn-info" onclick="viewEventDetails(${event.id})">
                        <i class="fas fa-eye mr-1"></i>Details
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="showSafetyInstructions()">
                        <i class="fas fa-shield-alt mr-1"></i>Safety
                    </button>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);

        // Click event for modal
        marker.on('click', function() {
            showEventModal(event);
        });

        return marker;
    }

    // Show event details modal
    function showEventModal(event) {
        const isDanger = event.status === 'danger';
        const modalBody = document.getElementById('eventModalBody');
        const viewDetailsBtn = document.getElementById('viewDetailsBtn');

        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-${isDanger ? 'danger' : 'warning'} text-white">
                            <h6 class="mb-0">Event Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Magnitude:</strong> <span class="badge badge-${isDanger ? 'danger' : 'warning'} p-2">${event.magnitude.toFixed(1)}</span></p>
                            <p><strong>Status:</strong> <span class="badge badge-${isDanger ? 'danger' : 'warning'}">${event.status.toUpperCase()}</span></p>
                            <p><strong>Occurred:</strong> ${new Date(event.occurred_at).toLocaleString()}</p>
                            <p><strong>Depth:</strong> ${event.depth ? event.depth + ' km' : 'Not specified'}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Device Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Device Name:</strong> ${event.device.nama_device}</p>
                            <p><strong>Location:</strong> ${event.device.lokasi}</p>
                            <p><strong>Status:</strong> <span class="badge badge-${event.device.status === 'aktif' ? 'success' : 'danger'}">${event.device.status === 'aktif' ? 'Active' : 'Inactive'}</span></p>
                            <p><strong>Coordinates:</strong> ${event.latitude.toFixed(4)}, ${event.longitude.toFixed(4)}</p>
                        </div>
                    </div>
                </div>
            </div>
            ${event.description ? `
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Description</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">${event.description}</p>
                </div>
            </div>
            ` : ''}
        `;

        // Set button action
        viewDetailsBtn.onclick = function() {
            window.location.href = `/user/events/${event.id}`;
        };

        // Show modal
        $('#eventModal').modal('show');
    }

    // Filter markers
    function filterMarkers() {
        const statusFilter = document.getElementById('filterStatus').value;
        const dateFilter = document.getElementById('filterDate').value;

        let filteredEvents = allEvents;

        // Filter by status
        if (statusFilter !== 'all') {
            filteredEvents = filteredEvents.filter(event => event.status === statusFilter);
        }

        // Filter by date
        if (dateFilter !== 'all') {
            const now = new Date();
            let cutoffDate;

            switch(dateFilter) {
                case 'today':
                    cutoffDate = new Date(now.setHours(0, 0, 0, 0));
                    break;
                case 'week':
                    cutoffDate = new Date(now.setDate(now.getDate() - 7));
                    break;
                case 'month':
                    cutoffDate = new Date(now.setMonth(now.getMonth() - 1));
                    break;
            }

            filteredEvents = filteredEvents.filter(event => {
                const eventDate = new Date(event.occurred_at);
                return eventDate >= cutoffDate;
            });
        }

        // Update map
        addEventsToMap(filteredEvents);
        updateStatistics(filteredEvents);
    }

    // Toggle clustering
    function toggleClustering() {
        addEventsToMap(allEvents);
    }

    // Fit map to markers
    function fitToMarkers() {
        if (markers.length === 0) return;

        const group = new L.FeatureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }

    // Refresh map
    function refreshMap() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
        btn.disabled = true;

        // Reload events data
        fetch('/api/user/recent-events')
            .then(response => response.json())
            .then(data => {
                allEvents = data.events.filter(event => event.latitude && event.longitude);
                addEventsToMap(allEvents);
                updateStatistics(allEvents);

                Swal.fire({
                    icon: 'success',
                    title: 'Map Refreshed',
                    text: 'Event data has been updated.',
                    timer: 1500,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Error refreshing map:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Refresh Failed',
                    text: 'Could not update event data.',
                });
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }

    // Update statistics
    function updateStatistics(events) {
        const totalEvents = events.length;
        const dangerEvents = events.filter(e => e.status === 'danger').length;
        const warningEvents = events.filter(e => e.status === 'warning').length;

        // Count unique active devices
        const activeDevices = [...new Set(events.map(e => e.device_id))].length;

        document.getElementById('totalEvents').textContent = totalEvents;
        document.getElementById('dangerEvents').textContent = dangerEvents;
        document.getElementById('warningEvents').textContent = warningEvents;
        document.getElementById('activeDevices').textContent = activeDevices;
    }

    // View event details
    function viewEventDetails(eventId) {
        window.location.href = `/user/events/${eventId}`;
    }

    // Show safety instructions
    function showSafetyInstructions() {
        Swal.fire({
            title: 'Earthquake Safety',
            html: `
                <div class="text-left">
                    <div class="alert alert-warning">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>Remember:</strong> Drop, Cover, and Hold On!
                    </div>
                    <ul>
                        <li>Drop to the ground</li>
                        <li>Take cover under sturdy furniture</li>
                        <li>Hold on until shaking stops</li>
                        <li>Stay away from windows</li>
                        <li>If outdoors, move to open area</li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Got it!'
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Show welcome message for first-time map users
        const firstMapVisit = !localStorage.getItem('eqmonitor_map_visited');
        if (firstMapVisit && allEvents.length > 0) {
            setTimeout(() => {
                Swal.fire({
                    title: 'Earthquake Events Map',
                    html: `
                        <div class="text-left">
                            <p>Welcome to the interactive earthquake map!</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Tips:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Click on markers to see event details</li>
                                    <li>Use filters to focus on specific events</li>
                                    <li>Clusters show multiple events in one area</li>
                                    <li>Click clusters to expand them</li>
                                </ul>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Explore Map'
                });
                localStorage.setItem('eqmonitor_map_visited', 'true');
            }, 1000);
        }
    });
</script>
@endpush
