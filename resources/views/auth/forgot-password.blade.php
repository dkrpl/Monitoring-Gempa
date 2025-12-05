@extends('layouts.guest')

@section('title', 'Forgot Password - EQMonitor')

@section('content')
    <h2 class="auth-title">Reset Password</h2>
    <p class="auth-description">Enter your email to receive a reset link</p>

    @if (session('status'))
        <div class="alert alert-success alert-custom">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">
                <i class="fas fa-envelope"></i> Email Address
            </label>
            <input id="email"
                   type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   placeholder="Enter your email">
            <small style="font-size: 11px; color: var(--gray); display: block; margin-top: 4px;">
                We'll send a password reset link
            </small>
        </div>

        <!-- Submit Button -->
        <div class="form-group" style="margin-top: 20px;">
            <button type="submit" class="btn-auth">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </div>

        <!-- Links -->
        <div class="auth-links">
            <p style="margin-bottom: 8px; font-size: 13px;">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </p>
            <p style="margin-bottom: 0; font-size: 13px;">
                Don't have an account?
                <a href="{{ route('register') }}">Sign up here</a>
            </p>
        </div>
    </form>
@endsection
