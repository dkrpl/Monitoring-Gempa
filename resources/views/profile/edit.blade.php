@extends('layouts.app')

@section('title', 'Edit Profile - EQMonitor')
@section('page-title', 'Edit Profile')

@section('action-button')
    <a href="{{ route('profile.show') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Profile
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Profile Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
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
                                    <label class="custom-file-label" for="image">Choose new photo</label>
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
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label required">Timezone</label>
                                    <select class="form-control @error('timezone') is-invalid @enderror"
                                            id="timezone"
                                            name="timezone"
                                            required>
                                        @foreach($timezones as $tz => $label)
                                            <option value="{{ $tz }}" {{ old('timezone', $user->timezone) == $tz ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="language" class="form-label required">Language</label>
                                    <select class="form-control @error('language') is-invalid @enderror"
                                            id="language"
                                            name="language"
                                            required>
                                        @foreach($languages as $code => $name)
                                            <option value="{{ $code }}" {{ old('language', $user->language) == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text"
                                           class="form-control @error('country') is-invalid @enderror"
                                           id="country"
                                           name="country"
                                           value="{{ old('country', $user->country) }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text"
                                   class="form-control @error('address') is-invalid @enderror"
                                   id="address"
                                   name="address"
                                   value="{{ old('address', $user->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text"
                                   class="form-control @error('city') is-invalid @enderror"
                                   id="city"
                                   name="city"
                                   value="{{ old('city', $user->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label for="bio" class="form-label">Bio / About Me</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio"
                                      name="bio"
                                      rows="4"
                                      placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 500 characters</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo mr-2"></i>Reset Changes
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Profile Preview -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Preview</h6>
            </div>
            <div class="card-body text-center">
                <img id="previewImage"
                     src="{{ $user->image ? asset('storage/' . $user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=random' }}"
                     alt="Preview"
                     class="avatar-lg mb-3">

                <h5 id="previewName">{{ $user->name }}</h5>
                <p class="text-muted" id="previewEmail">{{ $user->email }}</p>

                <div class="text-left mt-3">
                    <p><strong>Timezone:</strong> <span id="previewTimezone">{{ $timezones[$user->timezone] ?? $user->timezone }}</span></p>
                    <p><strong>Language:</strong> <span id="previewLanguage">{{ $languages[$user->language] ?? $user->language }}</span></p>
                    <p><strong>Location:</strong> <span id="previewLocation">{{ $user->city ? $user->city . ', ' . $user->country : 'Not specified' }}</span></p>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Use a clear profile photo
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Keep contact information updated
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Set correct timezone for accurate timestamps
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Add a bio to help others know you
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Regular updates ensure better communication
                    </li>
                </ul>
            </div>
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

    .custom-file-label::after {
        content: "Browse";
    }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('profilePreview');
        const previewImage = document.getElementById('previewImage');
        const label = input.nextElementSibling;

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                previewImage.src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
            label.textContent = input.files[0].name;
        }
    }

    function resetForm() {
        Swal.fire({
            title: 'Reset Changes?',
            text: 'This will revert all changes to original values.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('profileForm').reset();
                updatePreview();
                Swal.fire(
                    'Reset!',
                    'All changes have been reverted.',
                    'success'
                );
            }
        });
    }

    function updatePreview() {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const timezone = document.getElementById('timezone').options[document.getElementById('timezone').selectedIndex].text;
        const language = document.getElementById('language').options[document.getElementById('language').selectedIndex].text;
        const city = document.getElementById('city').value;
        const country = document.getElementById('country').value;

        document.getElementById('previewName').textContent = name || '{{ $user->name }}';
        document.getElementById('previewEmail').textContent = email || '{{ $user->email }}';
        document.getElementById('previewTimezone').textContent = timezone;
        document.getElementById('previewLanguage').textContent = language;

        const location = (city && country) ? `${city}, ${country}` : (city || country || 'Not specified');
        document.getElementById('previewLocation').textContent = location;
    }

    // Update preview on input
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('email').addEventListener('input', updatePreview);
    document.getElementById('timezone').addEventListener('change', updatePreview);
    document.getElementById('language').addEventListener('change', updatePreview);
    document.getElementById('city').addEventListener('input', updatePreview);
    document.getElementById('country').addEventListener('input', updatePreview);

    // Form validation
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;

        if (!name || !email) {
            e.preventDefault();
            Swal.fire('Error!', 'Please fill all required fields.', 'error');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            Swal.fire('Error!', 'Please enter a valid email address.', 'error');
            return;
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;

        return true;
    });

    // Character counter for bio
    document.getElementById('bio').addEventListener('input', function() {
        const charCount = this.value.length;
        const maxLength = 500;
        const counter = document.getElementById('bioCounter') || (function() {
            const counter = document.createElement('small');
            counter.id = 'bioCounter';
            counter.className = 'form-text text-right';
            this.parentNode.appendChild(counter);
            return counter;
        }.bind(this)());

        counter.textContent = `${charCount}/${maxLength} characters`;
        counter.className = `form-text text-right ${charCount > maxLength ? 'text-danger' : 'text-muted'}`;

        if (charCount > maxLength) {
            this.value = this.value.substring(0, maxLength);
        }
    });
</script>
@endpush
