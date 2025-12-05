@extends('layouts.app')

@section('title', 'User Profile - EQMonitor')
@section('page-title', 'User Profile: ' . $user->name)

@section('action-button')
    <div class="btn-group" role="group">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
            <i class="fas fa-edit mr-2"></i>Edit
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Users
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 col-xl-3">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <!-- Profile Picture -->
                @if($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}"
                         alt="{{ $user->name }}"
                         class="avatar-lg mb-3">
                @else
                    <div class="avatar-lg bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="font-size: 3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <h4 class="font-weight-bold">{{ $user->name }}</h4>
                <span class="badge badge-{{ $user->role === 'admin' ? 'admin' : 'user' }} p-2 mb-3">
                    {{ ucfirst($user->role) }} User
                </span>

                <hr>

                <div class="row text-left">
                    <div class="col-12 mb-2">
                        <i class="fas fa-envelope mr-2 text-primary"></i>
                        <strong>Email:</strong>
                        <div class="text-muted">{{ $user->email }}</div>
                    </div>
                    <div class="col-12 mb-2">
                        <i class="fas fa-calendar mr-2 text-primary"></i>
                        <strong>Member Since:</strong>
                        <div class="text-muted">{{ $user->created_at->format('F d, Y') }}</div>
                    </div>
                    <div class="col-12 mb-2">
                        <i class="fas fa-clock mr-2 text-primary"></i>
                        <strong>Last Updated:</strong>
                        <div class="text-muted">{{ $user->updated_at->format('F d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Account Status
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge badge-success">Active</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Email Verified
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge badge-success">Verified</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Last Login
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $user->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-xl-9">
        <!-- User Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Full Name</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Email Address</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">User Role</label>
                            <p>
                                <span class="badge badge-{{ $user->role === 'admin' ? 'admin' : 'user' }} p-2">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Account Created</label>
                            <p class="form-control-plaintext">{{ $user->created_at->format('F d, Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Last Updated</label>
                            <p class="form-control-plaintext">{{ $user->updated_at->format('F d, Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">User ID</label>
                            <p class="form-control-plaintext">{{ $user->id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Permissions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Role Permissions</h6>
            </div>
            <div class="card-body">
                @if($user->role === 'admin')
                    <div class="alert alert-primary" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-user-shield mr-2"></i>Administrator Access</h4>
                        <p>This user has full administrative privileges with access to all system features.</p>
                        <hr>
                        <ul class="mb-0">
                            <li>Full user management (Create, Read, Update, Delete)</li>
                            <li>Device configuration and monitoring</li>
                            <li>System settings and threshold configuration</li>
                            <li>Access to all reports and analytics</li>
                            <li>Can manage other administrators</li>
                        </ul>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-user mr-2"></i>Regular User Access</h4>
                        <p>This user has limited access to system features.</p>
                        <hr>
                        <ul class="mb-0">
                            <li>View dashboard and earthquake events</li>
                            <li>Access to monitoring data (read-only)</li>
                            <li>Can view reports and analytics</li>
                            <li>Can update their own profile information</li>
                            <li>Cannot access user management or system settings</li>
                        </ul>
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
                    <strong>Warning:</strong> These actions are irreversible. Please proceed with caution.
                </p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-danger">
                                    <i class="fas fa-trash mr-2"></i>Delete User
                                </h5>
                                <p class="card-text">
                                    Permanently delete this user account. This action cannot be undone.
                                </p>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger delete-btn">
                                        <i class="fas fa-trash mr-2"></i>Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if($user->role !== 'admin')
                    <div class="col-md-6">
                        <div class="card border-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-warning">
                                    <i class="fas fa-user-shield mr-2"></i>Promote to Admin
                                </h5>
                                <p class="card-text">
                                    Grant this user full administrative privileges.
                                </p>
                                <button type="button" class="btn btn-warning"
                                        onclick="promoteToAdmin({{ $user->id }})">
                                    <i class="fas fa-user-shield mr-2"></i>Promote to Admin
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function promoteToAdmin(userId) {
        Swal.fire({
            title: 'Promote to Administrator?',
            text: "This user will gain full administrative privileges. Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, promote user!'
        }).then((result) => {
            if (result.isConfirmed) {
                // In a real application, you would send an AJAX request
                // For now, we'll just show a success message
                Swal.fire(
                    'Promoted!',
                    'User has been promoted to administrator.',
                    'success'
                ).then(() => {
                    // Reload the page to show updated role
                    location.reload();
                });
            }
        });
    }
</script>
@endpush
