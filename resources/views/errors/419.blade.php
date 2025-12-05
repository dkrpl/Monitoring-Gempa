@extends('layouts.error')

@section('title', '419 - Page Expired')

@section('error-icon')
    <i class="fas fa-clock"></i>
@endsection

@section('error-code')
    419
@endsection

@section('error-title')
    Page Expired
@endsection

@section('error-message')
    <p>Your session has expired due to inactivity.</p>
    <p>This is a security measure to protect your account. Please refresh the page and try again.</p>
@endsection

@section('error-details')
    <p><i class="fas fa-lightbulb mr-2"></i>Tip: Sessions expire after 120 minutes of inactivity for security reasons.</p>
@endsection

@section('error-actions')
    <button onclick="window.location.reload()" class="btn-error btn-error-primary">
        <i class="fas fa-redo mr-2"></i>Refresh Page
    </button>
    <a href="{{ route('login') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-sign-in-alt mr-2"></i>Login Again
    </a>
    <a href="{{ url('/') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
@endsection

@section('support-info')
    Session issues? Clear browser cookies
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #06d6a0, #0496ff);
    }

    .seismic-wave {
        border-color: rgba(6, 214, 160, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh countdown
        let countdown = 10;
        const countdownEl = document.createElement('div');
        countdownEl.className = 'text-center mt-3';
        countdownEl.innerHTML = `
            <p class="text-muted">
                <i class="fas fa-sync-alt mr-2"></i>
                Auto-refreshing in <span id="countdown">${countdown}</span> seconds
            </p>
        `;
        document.querySelector('.error-body').appendChild(countdownEl);

        const countdownInterval = setInterval(() => {
            countdown--;
            document.getElementById('countdown').textContent = countdown;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.reload();
            }
        }, 1000);

        // Add CSRF token refresh
        refreshCsrfToken();
    });

    function refreshCsrfToken() {
        fetch('/sanctum/csrf-cookie', {
            credentials: 'include'
        }).then(() => {
            console.log('CSRF token refreshed');
        });
    }
</script>
@endpush
