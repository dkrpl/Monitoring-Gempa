@extends('layouts.app')

@section('title', 'Edit User - EQMonitor')
@section('page-title', 'Edit User: ' . $user->name)

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
                <h6 class="m-0 font-weight-bold text-primary">Edit User Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" id="userForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="profile-picture-container">
                                <div class="mb-3">
                                    @if($user->image)
                                        <img id="profilePreview"
                                             src="{{ asset('storage/' . $user->image) }}"
                                             alt="{{ $user->name }}"
                                             class="avatar-lg mb-3">
                                    @else
                                        <img id="profilePreview"
                                             src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=random"
                                             alt="{{ $user->name }}"
                                             class="avatar-lg mb-3">
                                    @endif
                                </div>
                                <div class="custom-file">
                                    <input type="file"
                                           class="custom-file-input"
                                           id="image"
                                           name="image"
                                           accept="image/*"
                                           onchange="previewImage(event)">
                                    <label class="custom-file-label" for="image">Change profile picture</label>
                                </div>
                                @if($user->image)
                                    <div class="mt-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="removeImage()">
                                            <i class="fas fa-trash mr-1"></i>Remove Image
                                        </button>
                                    </div>
                                @endif
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
                                           value="{{ old('name', $user->name) }}"
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
                                           value="{{ old('email', $user->email) }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password (Leave blank to keep current)</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation">
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
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Regular User</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Update User
                                    </button>
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-info">
                                        <i class="fas fa-eye mr-2"></i>View Profile
                                    </a>
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

    function removeImage() {
        if (confirm('Are you sure you want to remove the profile picture?')) {
            // In a real application, you would send an AJAX request to remove the image
            // For now, we'll just clear the preview and show a message
            const preview = document.getElementById('profilePreview');
            preview.src = 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=random';

            Swal.fire({
                icon: 'success',
                title: 'Image Removed',
                text: 'Profile picture will be removed when you save changes.',
                timer: 2000,
                showConfirmButton: false
            });

            // You could also add a hidden input to indicate image removal
        }
    }
</script>
@endpush
