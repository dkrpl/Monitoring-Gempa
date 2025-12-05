@extends('layouts.guest')

@section('title', 'Login - EQMonitor')

@section('content')
    <h2 class="auth-title">Welcome Back</h2>
    <p class="auth-description">Sign in to your earthquake monitoring dashboard</p>

    @if (session('status'))
        <div class="alert alert-success alert-custom">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
                   autocomplete="email"
                   autofocus
                   placeholder="Enter your email">
        </div>

        <!-- Password -->
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label" for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 12px;">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <input id="password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="Enter your password">
                <button type="button" class="password-toggle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="remember"
                       id="remember"
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember" style="font-size: 13px;">
                    Remember me
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group" style="margin-top: 20px;">
            <button type="submit" class="btn-auth">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </div>

        <!-- Links -->
        <div class="auth-links">
            <p style="margin-bottom: 8px; font-size: 13px;">
                Don't have an account?
                <a href="{{ route('register') }}">Create one here</a>
            </p>
            <div class="back-home">
                <i class="fas fa-arrow-left"></i>
                <a href="{{ route('landing') }}">Back to Homepage</a>
            </div>
        </div>
    </form>
@endsection
