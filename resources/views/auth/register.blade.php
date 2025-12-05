@extends('layouts.guest')

@section('title', 'Register - EQMonitor')

@section('content')
    <h2 class="auth-title">Create Account</h2>
    <p class="auth-description">Join our earthquake monitoring community</p>

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name and Email in one row -->
        <div class="compact-row">
            <div class="form-group">
                <label class="form-label" for="name">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input id="name"
                       type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autocomplete="name"
                       autofocus
                       placeholder="Full name">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input id="email"
                       type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       placeholder="Email address">
            </div>
        </div>

        <!-- Password and Confirm Password in one row -->
        <div class="compact-row">
            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="input-group">
                    <input id="password"
                           type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password"
                           required
                           autocomplete="new-password"
                           placeholder="Password">
                    <button type="button" class="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <!-- Password Strength -->
                <div class="password-strength">
                    <div class="password-strength-bar" id="password-strength"></div>
                </div>

                <!-- Password Match Indicator -->
                <div id="password-match" class="password-match"></div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">
                    <i class="fas fa-lock"></i> Confirm
                </label>
                <div class="input-group">
                    <input id="password_confirmation"
                           type="password"
                           class="form-control"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           placeholder="Confirm password">
                    <button type="button" class="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Password Requirements (Compact Grid) -->
        <div class="password-requirements">
            <ul>
                <li id="req-length"><i class="fas fa-check-circle"></i> 8+ characters</li>
                <li id="req-uppercase"><i class="fas fa-check-circle"></i> Uppercase</li>
                <li id="req-lowercase"><i class="fas fa-check-circle"></i> Lowercase</li>
                <li id="req-number"><i class="fas fa-check-circle"></i> Number</li>
                <li id="req-special"><i class="fas fa-check-circle"></i> Special char</li>
            </ul>
        </div>

        <!-- Terms -->
        <div class="form-group" style="margin-top: 15px;">
            <div class="form-check">
                <input class="form-check-input @error('terms') is-invalid @enderror"
                       type="checkbox"
                       name="terms"
                       id="terms"
                       required>
                <label class="form-check-label terms-check" for="terms">
                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms</a>
                    & <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy</a>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group" style="margin-top: 20px;">
            <button type="submit" class="btn-auth">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </div>

        <!-- Links -->
        <div class="auth-links">
            <p style="margin-bottom: 8px; font-size: 13px;">
                Already have an account?
                <a href="{{ route('login') }}">Sign in here</a>
            </p>
            <div class="back-home">
                <i class="fas fa-arrow-left"></i>
                <a href="{{ route('landing') }}">Back to Homepage</a>
            </div>
        </div>
    </form>

    <!-- Terms Modal (Minimal) -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Terms</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-2">
                    <p style="font-size: 12px;">By registering, you agree to our Terms of Service.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Modal (Minimal) -->
    <div class="modal fade" id="privacyModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Privacy</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-2">
                    <p style="font-size: 12px;">Your data is protected by our Privacy Policy.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthBar = document.getElementById('password-strength');
            const matchText = document.getElementById('password-match');

            // Update requirement icons
            function updateRequirement(id, isValid) {
                const element = document.getElementById(id);
                if (element) {
                    const icon = element.querySelector('i');
                    if (isValid) {
                        icon.className = 'fas fa-check-circle';
                        icon.style.color = 'var(--success)';
                    } else {
                        icon.className = 'fas fa-times-circle';
                        icon.style.color = 'var(--danger)';
                    }
                }
            }

            // Check password strength
            function checkPasswordStrength(password) {
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[^A-Za-z0-9]/.test(password)
                };

                // Update requirement indicators
                Object.keys(requirements).forEach(req => {
                    updateRequirement(`req-${req}`, requirements[req]);
                });

                // Calculate strength score
                const score = Object.values(requirements).filter(Boolean).length;

                // Update strength bar
                if (strengthBar) {
                    strengthBar.className = 'password-strength-bar';
                    strengthBar.classList.add(`strength-${score}`);
                }

                return score;
            }

            // Check password match
            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirm = confirmInput.value;

                if (!matchText) return;

                if (confirm === '') {
                    matchText.innerHTML = '';
                    return;
                }

                if (password === confirm) {
                    matchText.innerHTML = '<i class="fas fa-check-circle" style="color: var(--success);"></i> Passwords match';
                } else {
                    matchText.innerHTML = '<i class="fas fa-times-circle" style="color: var(--danger);"></i> Passwords do not match';
                }
            }

            // Event listeners
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });

            confirmInput.addEventListener('input', checkPasswordMatch);

            // Initialize requirements (all false)
            ['length', 'uppercase', 'lowercase', 'number', 'special'].forEach(req => {
                updateRequirement(`req-${req}`, false);
            });

            // Form validation
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirm = confirmInput.value;
                const terms = document.getElementById('terms');

                // Check password match
                if (password !== confirm) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Please make sure your passwords match.',
                        confirmButtonText: 'OK',
                        width: 300
                    });
                    return;
                }

                // Check terms
                if (!terms.checked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terms Required',
                        text: 'Please agree to the Terms and Privacy Policy.',
                        confirmButtonText: 'OK',
                        width: 300
                    });
                    return;
                }

                // Check password strength
                const score = checkPasswordStrength(password);
                if (score < 3) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Weak Password',
                        text: 'Please choose a stronger password.',
                        confirmButtonText: 'OK',
                        width: 300
                    });
                }
            });
        });
    </script>
    @endpush
@endsection
