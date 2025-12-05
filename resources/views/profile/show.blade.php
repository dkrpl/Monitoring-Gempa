@extends('layouts.app')

@section('title', 'My Profile - EQMonitor')
@section('page-title', 'My Profile')

@section('action-button')
    <a href="{{ route('profile.edit') }}" class="btn btn-warning">
        <i class="fas fa-edit mr-2"></i>Edit Profile
    </a>
@endsection

@section('content')
<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-4 col-xl-3">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <!-- Profile Picture -->
                <div class="profile-picture-container mb-4">
                    @if(Auth::user()->image)
                        <img src="{{ asset('storage/' . Auth::user()->image) }}"
                             alt="{{ Auth::user()->name }}"
                             class="avatar-lg mb-3">
                    @else
                        <div class="avatar-lg bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="font-size: 3rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif

                    @if(Auth::user()->image)
                        <div class="mt-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="deleteProfileImage()">
                                <i class="fas fa-trash mr-1"></i>Remove Photo
                            </button>
                        </div>
                    @endif
                </div>

                <h4 class="font-weight-bold">{{ Auth::user()->name }}</h4>
                <span class="badge badge-{{ Auth::user()->role === 'admin' ? 'admin' : 'user' }} p-2 mb-3">
                    <i class="fas fa-{{ Auth::user()->role === 'admin' ? 'user-shield' : 'user' }} mr-1"></i>
                    {{ ucfirst(Auth::user()->role) }}
                </span>

                <hr>

                <div class="text-left">
                    <div class="mb-3">
                        <i class="fas fa-envelope mr-2 text-primary"></i>
                        <strong>Email:</strong>
                        <div class="text-muted">{{ Auth::user()->email }}</div>
                    </div>

                    @if(Auth::user()->phone)
                    <div class="mb-3">
                        <i class="fas fa-phone mr-2 text-primary"></i>
                        <strong>Phone:</strong>
                        <div class="text-muted">{{ Auth::user()->phone }}</div>
                    </div>
                    @endif

                    @if(Auth::user()->full_address)
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                        <strong>Address:</strong>
                        <div class="text-muted">{{ Auth::user()->full_address }}</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <i class="fas fa-clock mr-2 text-primary"></i>
                        <strong>Timezone:</strong>
                        <div class="text-muted">{{ Auth::user()->timezone_name }}</div>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-language mr-2 text-primary"></i>
                        <strong>Language:</strong>
                        <div class="text-muted">{{ Auth::user()->language_name }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Statistics</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Member Since
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ Auth::user()->created_at->format('M d, Y') }}
                        </div>
                        <small class="text-muted">
                            {{ Auth::user()->created_at->diffForHumans() }}
                        </small>
                    </div>

                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Last Login
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ Auth::user()->updated_at->format('M d, Y H:i') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Account Status
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            <span class="badge badge-success">Active</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Email Verified
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            @if(Auth::user()->email_verified_at)
                                <span class="badge badge-success">Verified</span>
                            @else
                                <span class="badge badge-warning">Not Verified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-lg-8 col-xl-9">
        <!-- Bio Section -->
        @if(Auth::user()->bio)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-circle mr-2"></i>About Me
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ Auth::user()->bio }}</p>
            </div>
        </div>
        @endif

        <!-- Notification Preferences -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bell mr-2"></i>Notification Preferences
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#notificationsModal">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="notification-status">
                            <div class="status-icon {{ Auth::user()->email_notifications ? 'active' : 'inactive' }}">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <h6 class="mt-2">Email</h6>
                            <span class="badge badge-{{ Auth::user()->email_notifications ? 'success' : 'secondary' }}">
                                {{ Auth::user()->email_notifications ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4 text-center mb-3">
                        <div class="notification-status">
                            <div class="status-icon {{ Auth::user()->sms_notifications ? 'active' : 'inactive' }}">
                                <i class="fas fa-sms fa-2x"></i>
                            </div>
                            <h6 class="mt-2">SMS</h6>
                            <span class="badge badge-{{ Auth::user()->sms_notifications ? 'success' : 'secondary' }}">
                                {{ Auth::user()->sms_notifications ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4 text-center mb-3">
                        <div class="notification-status">
                            <div class="status-icon {{ Auth::user()->push_notifications ? 'active' : 'inactive' }}">
                                <i class="fas fa-mobile-alt fa-2x"></i>
                            </div>
                            <h6 class="mt-2">Push</h6>
                            <span class="badge badge-{{ Auth::user()->push_notifications ? 'success' : 'secondary' }}">
                                {{ Auth::user()->push_notifications ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Current notification method: {{ Auth::user()->notification_status }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-shield-alt mr-2"></i>Account Security
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Change Password</h6>
                        <p class="text-muted small">Update your password regularly for better security.</p>
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#passwordModal">
                            <i class="fas fa-key mr-2"></i>Change Password
                        </button>
                    </div>

                    <div class="col-md-6">
                        <h6>Two-Factor Authentication</h6>
                        <p class="text-muted small">Add an extra layer of security to your account.</p>
                        <button type="button" class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-mobile-alt mr-2"></i>Enable 2FA (Coming Soon)
                        </button>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6>Recent Activity</h6>
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-primary">Last password change</small>
                                    <small class="text-muted">{{ Auth::user()->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-primary">Profile last updated</small>
                                    <small class="text-muted">{{ Auth::user()->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Information -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-desktop mr-2"></i>Current Session
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>IP Address:</strong></p>
                        <p class="mb-1"><strong>Browser:</strong></p>
                        <p class="mb-1"><strong>Login Time:</strong></p>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-1">{{ request()->ip() }}</p>
                        <p class="mb-1">{{ request()->userAgent() }}</p>
                        <p class="mb-1">{{ now()->format('M d, Y H:i:s') }}</p>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <button type="button" class="btn btn-outline-danger" onclick="logoutOtherDevices()">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout from Other Devices
                    </button>
                    <small class="d-block text-muted mt-2">
                        This will log you out from all other devices except this one.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Preferences</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('profile.notifications') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="email_notifications"
                                   name="email_notifications" {{ Auth::user()->email_notifications ? 'checked' : '' }}>
                            <label class="custom-control-label" for="email_notifications">
                                Email Notifications
                            </label>
                        </div>
                        <small class="form-text text-muted">Receive earthquake alerts via email</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="sms_notifications"
                                   name="sms_notifications" {{ Auth::user()->sms_notifications ? 'checked' : '' }}>
                            <label class="custom-control-label" for="sms_notifications">
                                SMS Notifications
                            </label>
                        </div>
                        <small class="form-text text-muted">Receive earthquake alerts via SMS (requires phone number)</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="push_notifications"
                                   name="push_notifications" {{ Auth::user()->push_notifications ? 'checked' : '' }}>
                            <label class="custom-control-label" for="push_notifications">
                                Push Notifications
                            </label>
                        </div>
                        <small class="form-text text-muted">Receive earthquake alerts as push notifications</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('profile.password') }}" method="POST" id="passwordForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrengthBar" role="progressbar"></div>
                        </div>
                        <small class="form-text text-muted" id="passwordStrengthText">Password strength</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <small class="form-text text-muted" id="passwordMatchText"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #f8f9fc;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .notification-status .status-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s;
    }

    .notification-status .status-icon.active {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }

    .notification-status .status-icon.inactive {
        background: #e9ecef;
        color: #6c757d;
    }

    .list-group-item {
        border-left: 4px solid transparent;
    }

    .list-group-item:nth-child(odd) {
        border-left-color: #4e73df;
    }

    .list-group-item:nth-child(even) {
        border-left-color: #1cc88a;
    }
</style>
@endpush

@push('scripts')
<script>
    function deleteProfileImage() {
        Swal.fire({
            title: 'Remove Profile Photo?',
            text: "This will remove your profile photo. You can upload a new one anytime.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("profile.image.delete") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Removed!',
                            'Your profile photo has been removed.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                });
            }
        });
    }

    function logoutOtherDevices() {
        Swal.fire({
            title: 'Logout from Other Devices?',
            text: "This will log you out from all other devices except this one.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                // In a real application, you would implement this
                Swal.fire(
                    'Logged Out!',
                    'You have been logged out from all other devices.',
                    'success'
                );
            }
        });
    }

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        updatePasswordStrength(strength);
        checkPasswordMatch();
    });

    document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);

    function checkPasswordStrength(password) {
        let strength = 0;

        // Length check
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;

        // Character type checks
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        return Math.min(strength, 5);
    }

    function updatePasswordStrength(strength) {
        const bar = document.getElementById('passwordStrengthBar');
        const text = document.getElementById('passwordStrengthText');

        const strengthData = [
            { width: '20%', color: '#e74a3b', text: 'Very Weak' },
            { width: '40%', color: '#f6c23e', text: 'Weak' },
            { width: '60%', color: '#1cc88a', text: 'Fair' },
            { width: '80%', color: '#4e73df', text: 'Good' },
            { width: '100%', color: '#36b9cc', text: 'Strong' }
        ];

        const data = strengthData[strength - 1] || strengthData[0];

        bar.style.width = data.width;
        bar.style.backgroundColor = data.color;
        bar.className = 'progress-bar';

        if (strength > 0) {
            bar.classList.add('bg-' + (strength >= 4 ? 'success' : strength >= 3 ? 'info' : 'warning'));
        }

        text.textContent = 'Password strength: ' + data.text;
        text.className = 'form-text ' + (strength >= 4 ? 'text-success' : strength >= 3 ? 'text-info' : 'text-warning');
    }

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        const text = document.getElementById('passwordMatchText');

        if (!confirm) {
            text.textContent = '';
            text.className = 'form-text text-muted';
            return;
        }

        if (password === confirm) {
            text.textContent = '✓ Passwords match';
            text.className = 'form-text text-success';
        } else {
            text.textContent = '✗ Passwords do not match';
            text.className = 'form-text text-danger';
        }
    }

    // Form validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            e.preventDefault();
            Swal.fire('Error!', 'Please fill all password fields.', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire('Error!', 'Passwords do not match.', 'error');
            return;
        }

        if (newPassword.length < 8) {
            e.preventDefault();
            Swal.fire('Error!', 'Password must be at least 8 characters long.', 'error');
            return;
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        submitBtn.disabled = true;

        return true;
    });
</script>
@endpush
