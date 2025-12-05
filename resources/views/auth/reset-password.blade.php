@extends('layouts.guest')

@section('title', 'Reset Password - EQMonitor')

@section('content')
    <h2 class="auth-title">Set New Password</h2>
    <p class="auth-description">Enter your new password below</p>

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">
                <i class="fas fa-envelope"></i> Email Address
            </label>
            <input id="email"
                   type="email"
                   class="form-control"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   required
                   readonly
                   style="background-color: #f8f9fc;">
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">
                <i class="fas fa-lock"></i> New Password
            </label>
            <div class="input-group">
                <input id="password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       required
                       autocomplete="new-password"
                       placeholder="Enter new password">
                <button type="button" class="password-toggle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <!-- Password Strength -->
            <div class="password-strength mt-2">
                <div class="password-strength-bar" id="password-strength"></div>
            </div>

            <!-- Password Requirements -->
            <div class="password-requirements mt-3">
                <ul>
                    <li id="req-length"><i class="fas fa-check-circle"></i> At least 8 characters</li>
                    <li id="req-uppercase"><i class="fas fa-check-circle"></i> One uppercase letter</li>
                    <li id="req-lowercase"><i class="fas fa-check-circle"></i> One lowercase letter</li>
                    <li id="req-number"><i class="fas fa-check-circle"></i> One number</li>
                    <li id="req-special"><i class="fas fa-check-circle"></i> One special character</li>
                </ul>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label class="form-label" for="password_confirmation">
                <i class="fas fa-lock"></i> Confirm Password
            </label>
            <div class="input-group">
                <input id="password_confirmation"
                       type="password"
                       class="form-control"
                       name="password_confirmation"
                       required
                       autocomplete="new-password"
                       placeholder="Confirm new password">
                <button type="button" class="password-toggle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div id="password-match" class="mt-2" style="font-size: 13px;"></div>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button type="submit" class="btn-auth">
                <i class="fas fa-sync-alt"></i> Reset Password
            </button>
        </div>

        <!-- Links -->
        <div class="auth-links">
            <p style="margin-bottom: 0;">
                Remember your password?
                <a href="{{ route('login') }}">Sign in here</a>
            </p>
        </div>
    </form>

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

            // Form validation
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirm = confirmInput.value;

                // Check password match
                if (password !== confirm) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Mismatch',
                        text: 'Please make sure your passwords match.',
                        confirmButtonText: 'OK'
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
                        text: 'Please choose a stronger password with at least 8 characters including uppercase, lowercase, and numbers.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
    @endpush
@endsection
