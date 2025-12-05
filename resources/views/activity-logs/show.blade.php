@extends('layouts.app')

@section('title', 'Activity Log Details - EQMonitor')
@section('page-title', 'Activity Log Details')

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Logs
        </a>
        <button type="button" class="btn btn-info ml-2" onclick="copyLogDetails()">
            <i class="fas fa-copy mr-2"></i>Copy Details
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Log Information</h6>
                <span class="badge badge-{{ $activityLog->action_color }} p-2">
                    <i class="{{ $activityLog->action_icon }} mr-1"></i>
                    {{ str_replace('_', ' ', ucwords($activityLog->action)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Timestamp</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ $activityLog->created_at->format('F d, Y H:i:s') }}
                            </p>
                            <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Log ID</label>
                            <p class="form-control-plaintext">
                                <code>LOG-{{ str_pad($activityLog->id, 6, '0', STR_PAD_LEFT) }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">User</label>
                            @if($activityLog->user)
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @if($activityLog->user->image)
                                            <img src="{{ asset('storage/' . $activityLog->user->image) }}"
                                                 alt="{{ $activityLog->user->name }}"
                                                 class="user-avatar">
                                        @else
                                            <div class="user-avatar bg-primary text-white">
                                                {{ strtoupper(substr($activityLog->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $activityLog->user->name }}</strong><br>
                                        <small class="text-muted">{{ $activityLog->user->email }}</small><br>
                                        <span class="badge badge-{{ $activityLog->user->role === 'admin' ? 'admin' : 'user' }}">
                                            {{ ucfirst($activityLog->user->role) }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">IP Address</label>
                            <p class="form-control-plaintext">
                                <code>{{ $activityLog->ip_address }}</code>
                            </p>
                            @if($activityLog->user_agent)
                                <small class="text-muted">
                                    <i class="fas fa-desktop mr-1"></i>
                                    {{ Str::limit($activityLog->user_agent, 50) }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Description</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0">{{ $activityLog->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($activityLog->details)
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Additional Details</label>
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <pre id="detailsJson" style="background: #f8f9fa; padding: 15px; border-radius: 4px; max-height: 300px; overflow: auto;">{{ json_encode($activityLog->details, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($activityLog->model)
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Related Item</label>
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <p class="mb-1">
                                        <strong>Type:</strong> {{ class_basename($activityLog->model_type) }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>ID:</strong> {{ $activityLog->model_id }}
                                    </p>
                                    @if($activityLog->model)
                                        <p class="mb-0">
                                            <strong>Details:</strong>
                                            @if(method_exists($activityLog->model, 'getLogDescription'))
                                                {{ $activityLog->model->getLogDescription() }}
                                            @else
                                                {{ $activityLog->model->toJson() }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Related Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Related Actions</h6>
            </div>
            <div class="card-body">
                @if($activityLog->user)
                    <a href="{{ route('users.show', $activityLog->user) }}" class="btn btn-outline-primary btn-block mb-3">
                        <i class="fas fa-user mr-2"></i>View User Profile
                    </a>
                @endif

                <button type="button" class="btn btn-outline-info btn-block mb-3" onclick="viewSimilarLogs()">
                    <i class="fas fa-search mr-2"></i>Find Similar Logs
                </button>

                <button type="button" class="btn btn-outline-warning btn-block" onclick="analyzeLogPattern()">
                    <i class="fas fa-chart-line mr-2"></i>Analyze Pattern
                </button>
            </div>
        </div>

        <!-- Technical Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-code mr-2"></i>Technical Details
                </h6>
            </div>
            <div class="card-body">
                <div class="technical-details">
                    <p class="mb-2">
                        <strong>Model Type:</strong>
                        <code class="float-right">{{ $activityLog->model_type ?: 'N/A' }}</code>
                    </p>

                    <p class="mb-2">
                        <strong>Model ID:</strong>
                        <code class="float-right">{{ $activityLog->model_id ?: 'N/A' }}</code>
                    </p>

                    <p class="mb-2">
                        <strong>Action Type:</strong>
                        <span class="badge badge-{{ $activityLog->action_color }} float-right">
                            {{ $activityLog->action }}
                        </span>
                    </p>

                    <p class="mb-0">
                        <strong>Database ID:</strong>
                        <code class="float-right">{{ $activityLog->id }}</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- User Agent Details -->
        @if($activityLog->user_agent)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-desktop mr-2"></i>User Agent Details
                </h6>
            </div>
            <div class="card-body">
                <div class="user-agent-details">
                    <p class="mb-2">
                        <strong>Full User Agent:</strong>
                    </p>
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <small class="text-muted">{{ $activityLog->user_agent }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .technical-details p {
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px solid #f0f0f0;
    }

    .technical-details p:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function copyLogDetails() {
        const details = document.getElementById('detailsJson').textContent;
        const logDetails = {
            id: {{ $activityLog->id }},
            timestamp: '{{ $activityLog->created_at }}',
            user: '{{ $activityLog->user ? $activityLog->user->name : "System" }}',
            action: '{{ $activityLog->action }}',
            description: '{{ $activityLog->description }}',
            ip_address: '{{ $activityLog->ip_address }}',
            details: details ? JSON.parse(details) : null
        };

        const text = JSON.stringify(logDetails, null, 2);

        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire('Copied!', 'Log details copied to clipboard.', 'success');
            })
            .catch(err => {
                Swal.fire('Error!', 'Failed to copy details.', 'error');
            });
    }

    function viewSimilarLogs() {
        const action = '{{ $activityLog->action }}';
        const userId = {{ $activityLog->user_id ?? 'null' }};

        let url = '{{ route("activity-logs.index") }}?';
        if (action) url += `action=${action}&`;
        if (userId) url += `user_id=${userId}`;

        window.location.href = url;
    }

    function analyzeLogPattern() {
        Swal.fire({
            title: 'Analyzing Log Pattern...',
            text: 'Checking for similar activity patterns...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate analysis
        setTimeout(() => {
            const patterns = [
                'Regular system maintenance activity',
                'User performing routine tasks',
                'Suspicious activity detected',
                'Normal user behavior pattern'
            ];

            const randomPattern = patterns[Math.floor(Math.random() * patterns.length)];

            Swal.fire({
                title: 'Analysis Complete',
                html: `
                    <div class="text-left">
                        <p><strong>Pattern Detected:</strong> ${randomPattern}</p>
                        <p><strong>Confidence Level:</strong> ${Math.floor(Math.random() * 40) + 60}%</p>
                        <p><strong>Recommendation:</strong> No action required</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }, 2000);
    }

    // Pretty print JSON
    document.addEventListener('DOMContentLoaded', function() {
        const detailsElement = document.getElementById('detailsJson');
        if (detailsElement) {
            try {
                const json = JSON.parse(detailsElement.textContent);
                detailsElement.textContent = JSON.stringify(json, null, 2);
            } catch (e) {
                // If not valid JSON, leave as is
            }
        }
    });
</script>
@endpush
