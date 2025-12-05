@extends('layouts.error')

@section('title', '404 - Page Not Found')

@section('error-icon')
    <i class="fas fa-map-signs"></i>
@endsection

@section('error-code')
    404
@endsection

@section('error-title')
    Page Not Found
@endsection

@section('error-message')
    <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <p>It seems like you've encountered a seismic gap in our website!</p>
@endsection

@section('error-details')
    @if(config('app.debug') && isset($exception))
        <p><strong>Error Details:</strong></p>
        <pre>{{ $exception->getMessage() }}</pre>
        <p class="mt-2"><strong>Request URL:</strong> {{ request()->fullUrl() }}</p>
    @else
        <p><i class="fas fa-info-circle mr-2"></i>Debug mode is disabled. Contact administrator for more details.</p>
    @endif
@endsection

@section('error-actions')
    <a href="{{ url('/') }}" class="btn-error btn-error-primary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
    <a href="{{ route('dashboard') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
    </a>
    <button onclick="window.history.back()" class="btn-error btn-error-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Go Back
    </button>
@endsection

@section('support-info')
    Need help? Contact support@eqmonitor.com
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    }

    .seismic-wave {
        border-color: rgba(255, 107, 107, 0.3);
    }
</style>
@endpush
