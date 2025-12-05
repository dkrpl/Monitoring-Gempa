@extends('layouts.app')

@section('title', 'Create User - EQMonitor')
@section('page-title', 'Create New User')

@section('action-button')
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Users
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="profile-picture-container">
                                <div class="mb-3">
                                    <img id="profilePreview"
                                         src="https://ui-avatars.com/api/?name=New+User&size=200&background=random"
                                         alt="Profile Preview"
                                         class="avatar-lg mb-3">
                                </div>
                                <div class="custom-file">
                                    <input type="file"
                                           class="custom-file-input"
                                           id="image"
                                           name="image"
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    <label class="custom-file-label" for="image">Choose profile picture</label>
                                </div>
                                <small class="form-text text-muted">Max 2MB, JPG, PNG, GIF</small>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">Full Name</label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">Email Address</label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label required">Password</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label required">Confirm Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="role" class="form-label required">User Role</label>
                                    <select class="form-control @error('role') is-invalid @enderror"
                                            id="role"
                                            name="role"
                                            required>
                                        <option value="">Select Role</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Regular User</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Create User
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Role Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="fas fa-user-shield mr-2"></i>Administrator
                                </h5>
                                <p class="card-text">
                                    Administrators have full access to the system including:
                                </p>
                                <ul>
                                    <li>User Management</li>
                                    <li>Device Configuration</li>
                                    <li>System Settings</li>
                                    <li>All Reports and Analytics</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <h5 class="card-title text-info">
                                    <i class="fas fa-user mr-2"></i>Regular User
                                </h5>
                                <p class="card-text">
                                    Regular users have limited access to:
                                </p>
                                <ul>
                                    <li>View Dashboard</li>
                                    <li>Monitor Earthquake Events</li>
                                    <li>View Reports (Read-only)</li>
                                    <li>Update their own profile</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('profilePreview');
        const label = input.nextElementSibling;

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
            label.textContent = input.files[0].name;
        }
    }

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        updateStrengthIndicator(strength);
    });

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

        return Math.min(strength, 5); // Max 5
    }

    function updateStrengthIndicator(strength) {
        const indicator = document.getElementById('passwordStrength');
        if (!indicator) {
            // Create indicator if it doesn't exist
            const passwordField = document.getElementById('password');
            const newIndicator = document.createElement('div');
            newIndicator.id = 'passwordStrength';
            newIndicator.className = 'mt-2';
            passwordField.parentNode.appendChild(newIndicator);
        }

        const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'][strength];
        const strengthColor = ['danger', 'danger', 'warning', 'info', 'success', 'success'][strength];

        document.getElementById('passwordStrength').innerHTML = `
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-${strengthColor}"
                     role="progressbar"
                     style="width: ${(strength / 5) * 100}%">
                </div>
            </div>
            <small class="text-${strengthColor}">Password strength: ${strengthText}</small>
        `;
    }
</script>
@endpush
