@extends('layouts.guest')

@section('title', 'Verify Email - EQMonitor')

@section('content')
    <div class="text-center mb-4">
        <div class="verify-icon mb-3">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-envelope fa-2x text-white"></i>
            </div>
        </div>
        <h2 class="auth-title">Verify Your Email</h2>
        <p class="auth-description">Please verify your email address to continue</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-custom">
            <i class="fas fa-check-circle"></i>
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="text-center mb-4">
        <p style="color: var(--gray); font-size: 14px;">
            Before proceeding, please check your email for a verification link.
            If you did not receive the email,
        </p>
    </div>

    <!-- Resend Form -->
    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn-auth">
            <i class="fas fa-paper-plane"></i> Resend Verification Email
        </button>
    </form>

    <!-- Logout Form -->
    <form method="POST" action="{{ route('logout') }}" class="mb-4">
        @csrf
        <button type="submit" class="btn-auth" style="background: linear-gradient(135deg, var(--gray) 0%, var(--dark) 100%);">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>

    <!-- Links -->
    <div class="auth-links">
        <p style="margin-bottom: 8px;">
            <a href="{{ route('landing') }}">
                <i class="fas fa-home"></i> Back to Homepage
            </a>
        </p>
        <p style="margin-bottom: 0;">
            Need help? <a href="{{ route('contact') }}">Contact Support</a>
        </p>
    </div>

    <style>
        .verify-icon {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
@endsection
