@extends('layouts.app')

@section('title', 'Activity Logs - EQMonitor')
@section('page-title', 'Activity Logs')

@section('action-button')
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-warning" onclick="clearOldLogs()">
            <i class="fas fa-trash-alt mr-2"></i>Clear Old Logs
        </button>
        <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#filterModal">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <a href="{{ route('activity-logs.export') . ($request->has('user_id') ? '?user_id=' . $request->user_id : '') . ($request->has('start_date') ? '&start_date=' . $request->start_date : '') . ($request->has('end_date') ? '&end_date=' . $request->end_date : '') }}"
           class="btn btn-success ml-2">
            <i class="fas fa-download mr-2"></i>Export
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
                            Total Logs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-history fa-2x text-gray-300"></i>
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
                            Today's Logs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Active Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Action Types</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['actions'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Activity Logs</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Active Filters -->
                @if($request->has('user_id') || $request->has('action') || $request->has('start_date') || $request->has('search'))
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-filter mr-2"></i>Active Filters
                    </h6>
                    <div class="d-flex flex-wrap">
                        @if($request->has('user_id') && $request->user_id)
                            @php
                                $user = \App\Models\User::find($request->user_id);
                            @endphp
                            <span class="badge badge-primary mr-2 mb-2">
                                User: {{ $user ? $user->name : 'Unknown' }}
                                <a href="{{ url()->current() . '?' . http_build_query(array_except(request()->query(), 'user_id')) }}"
                                   class="text-white ml-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if($request->has('action') && $request->action)
                            <span class="badge badge-primary mr-2 mb-2">
                                Action: {{ $request->action }}
                                <a href="{{ url()->current() . '?' . http_build_query(array_except(request()->query(), 'action')) }}"
                                   class="text-white ml-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if($request->has('start_date') && $request->start_date)
                            <span class="badge badge-primary mr-2 mb-2">
                                From: {{ $request->start_date }}
                                <a href="{{ url()->current() . '?' . http_build_query(array_except(request()->query(), 'start_date')) }}"
                                   class="text-white ml-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if($request->has('end_date') && $request->end_date)
                            <span class="badge badge-primary mr-2 mb-2">
                                To: {{ $request->end_date }}
                                <a href="{{ url()->current() . '?' . http_build_query(array_except(request()->query(), 'end_date')) }}"
                                   class="text-white ml-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        @if($request->has('search') && $request->search)
                            <span class="badge badge-primary mr-2 mb-2">
                                Search: {{ $request->search }}
                                <a href="{{ url()->current() . '?' . http_build_query(array_except(request()->query(), 'search')) }}"
                                   class="text-white ml-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endif

                        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-danger mb-2">
                            <i class="fas fa-times mr-1"></i>Clear All
                        </a>
                    </div>
                </div>
                @endif

                @if($logs->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No activity logs found</h5>
                        <p class="text-gray-500">Activity logs will appear here as users interact with the system</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered data-table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $log->created_at->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    @if($log->user->image)
                                                        <img src="{{ asset('storage/' . $log->user->image) }}"
                                                             alt="{{ $log->user->name }}"
                                                             class="user-avatar-sm">
                                                    @else
                                                        <div class="user-avatar-sm bg-primary text-white">
                                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $log->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $log->user->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $log->action_color }} p-2">
                                            <i class="{{ $log->action_icon }} mr-1"></i>
                                            {{ str_replace('_', ' ', ucwords($log->action)) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <code class="text-muted" style="font-size: 0.8rem;">
                                            {{ $log->ip_address }}
                                        </code>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('activity-logs.show', $log) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-warning btn-sm"
                                                    title="View Details JSON"
                                                    onclick="showLogDetails({{ $log->id }})">
                                                <i class="fas fa-code"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Activity Logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="GET" action="{{ route('activity-logs.index') }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <select class="form-control" id="user_id" name="user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $request->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="action">Action</label>
                        <select class="form-control" id="action" name="action">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ $request->action == $action ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucwords($action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ $request->start_date }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ $request->end_date }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ $request->search }}"
                               placeholder="Search in description, IP, or user agent...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .user-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .badge-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
    .badge-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .badge-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    .badge-danger { background: linear-gradient(45deg, #e74a3b, #d52a1e); }
    .badge-info { background: linear-gradient(45deg, #36b9cc, #258391); }
    .badge-secondary { background: linear-gradient(45deg, #858796, #6c757d); }
</style>
@endpush

@push('scripts')
<script>
    function clearOldLogs() {
        Swal.fire({
            title: 'Clear Old Logs?',
            text: "This will delete activity logs older than 30 days. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, clear them!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/activity-logs/clear', {
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
                            'Cleared!',
                            'Old activity logs have been cleared.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Failed to clear logs.', 'error');
                });
            }
        });
    }

    function refreshLogs() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    function showLogDetails(logId) {
        fetch(`/activity-logs/${logId}`)
            .then(response => response.json())
            .then(data => {
                if (data.log) {
                    const log = data.log;
                    const details = log.details ? JSON.stringify(log.details, null, 2) : 'No additional details';

                    Swal.fire({
                        title: 'Log Details',
                        html: `
                            <div class="text-left">
                                <p><strong>ID:</strong> ${log.id}</p>
                                <p><strong>Timestamp:</strong> ${log.created_at}</p>
                                <p><strong>User:</strong> ${log.user ? log.user.name : 'System'}</p>
                                <p><strong>Action:</strong> <span class="badge badge-${log.action_color}">${log.action}</span></p>
                                <p><strong>Description:</strong> ${log.description}</p>
                                <p><strong>IP Address:</strong> <code>${log.ip_address}</code></p>
                                <p><strong>User Agent:</strong> <small>${log.user_agent || 'N/A'}</small></p>
                                <hr>
                                <h6>Details:</h6>
                                <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 200px; overflow: auto;">${details}</pre>
                            </div>
                        `,
                        width: 700,
                        confirmButtonText: 'Close'
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Failed to load log details.', 'error');
            });
    }

    // Auto-refresh logs every 30 seconds if on first page
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page') || 1;

        if (page == 1) {
            setInterval(() => {
                // Only auto-refresh if not filtered
                if (!urlParams.has('user_id') && !urlParams.has('action') && !urlParams.has('search')) {
                    fetchLogsCount();
                }
            }, 30000);
        }
    });

    function fetchLogsCount() {
        fetch('/activity-logs/statistics')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const todayCount = data.statistics.today_logs;
                    const todayElement = document.querySelector('.card.border-left-success .h5');

                    if (todayElement && todayElement.textContent != todayCount) {
                        showToast(`${todayCount - parseInt(todayElement.textContent)} new activities logged`, 'info');
                        todayElement.textContent = todayCount;
                    }
                }
            });
    }

    function showToast(message, type = 'info') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }
</script>
@endpush
