@extends('layouts.guest')

@section('title', 'Confirm Password - EQMonitor')

@section('content')
    <div class="text-center mb-4">
        <div class="security-icon mb-3">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--warning) 0%, #f8b229 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-shield-alt fa-2x text-white"></i>
            </div>
        </div>
        <h2 class="auth-title">Security Check</h2>
        <p class="auth-description">Please confirm your password to continue</p>
    </div>

    <div class="alert alert-custom" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; border-left: 4px solid #ffc107;">
        <i class="fas fa-info-circle"></i>
        This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
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
                       autocomplete="current-password"
                       autofocus
                       placeholder="Enter your current password">
                <button type="button" class="password-toggle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block" style="font-size: 13px; margin-top: 5px;">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button type="submit" class="btn-auth" style="background: linear-gradient(135deg, var(--warning) 0%, #f8b229 100%);">
                <i class="fas fa-check-circle"></i> Confirm Password
            </button>
        </div>

        <!-- Links -->
        <div class="auth-links">
            <div class="back-home">
                <i class="fas fa-arrow-left"></i>
                <a href="{{ url()->previous() }}">Go Back</a>
            </div>
        </div>
    </form>

    <style>
        .security-icon {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .alert-custom {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show error if exists
            @error('password')
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Password',
                    text: '{{ $message }}',
                    timer: 3000,
                    showConfirmButton: true
                });
            @enderror

            // Auto-focus password field
            document.getElementById('password').focus();
        });
    </script>
@endsection
