@extends('layouts.error')

@section('title', '401 - Unauthorized')

@section('error-icon')
    <i class="fas fa-user-lock"></i>
@endsection

@section('error-code')
    401
@endsection

@section('error-title')
    Unauthorized Access
@endsection

@section('error-message')
    <p>You need to be authenticated to access this resource.</p>
    <p>Please log in with your credentials to continue.</p>
@endsection

@section('error-details')
    <p><i class="fas fa-key mr-2"></i>This area requires valid authentication credentials.</p>
    @if(config('app.debug') && isset($exception))
        <p class="mt-2"><strong>Debug Info:</strong> {{ $exception->getMessage() }}</p>
    @endif
@endsection

@section('error-actions')
    <a href="{{ route('login') }}" class="btn-error btn-error-primary">
        <i class="fas fa-sign-in-alt mr-2"></i>Login
    </a>
    @if(Route::has('register'))
        <a href="{{ route('register') }}" class="btn-error btn-error-secondary">
            <i class="fas fa-user-plus mr-2"></i>Register
        </a>
    @endif
    <a href="{{ url('/') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
@endsection

@section('support-info')
    Forgot password? Use password reset
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #3d348b, #7678ed);
    }

    .seismic-wave {
        border-color: rgba(61, 52, 139, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-redirect to login if on protected page
        const currentPath = window.location.pathname;
        if (currentPath.includes('/admin') || currentPath.includes('/dashboard')) {
            setTimeout(() => {
                window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(currentPath);
            }, 5000);

            // Show redirect countdown
            const redirectMsg = document.createElement('div');
            redirectMsg.className = 'alert alert-info mt-3';
            redirectMsg.innerHTML = `
                <i class="fas fa-info-circle mr-2"></i>
                Redirecting to login page in <span id="redirectCountdown">5</span> seconds...
            `;
            document.querySelector('.error-body').appendChild(redirectMsg);

            let countdown = 5;
            const countdownInterval = setInterval(() => {
                countdown--;
                document.getElementById('redirectCountdown').textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }

        // Show login form if on same page
        if (window.self === window.top) {
            const loginForm = document.createElement('div');
            loginForm.className = 'mt-4';
            loginForm.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-sign-in-alt mr-2"></i>Quick Login
                        </h6>
                        <form id="quickLoginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="admin@eqmonitor.com">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="••••••••">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </button>
                        </form>
                    </div>
                </div>
            `;
            document.querySelector('.error-body').appendChild(loginForm);

            document.getElementById('quickLoginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // In real app, this would submit to login endpoint
                window.location.href = '{{ route("login") }}';
            });
        }
    });
</script>
@endpush
