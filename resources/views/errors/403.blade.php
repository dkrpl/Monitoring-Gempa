@extends('layouts.error')

@section('title', '403 - Forbidden')

@section('error-icon')
    <i class="fas fa-ban"></i>
@endsection

@section('error-code')
    403
@endsection

@section('error-title')
    Access Forbidden
@endsection

@section('error-message')
    <p>You don't have permission to access this page or resource.</p>
    <p>This area is restricted to authorized personnel only.</p>
@endsection

@section('error-details')
    @if(config('app.debug') && isset($exception))
        <p><strong>Error Details:</strong></p>
        <pre>{{ $exception->getMessage() }}</pre>
    @else
        <p><i class="fas fa-shield-alt mr-2"></i>This area requires special authorization.</p>
    @endif
@endsection

@section('error-actions')
    <a href="{{ url('/') }}" class="btn-error btn-error-primary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>

    @if(Auth::check())
        <a href="{{ route('dashboard') }}" class="btn-error btn-error-secondary">
            <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-error btn-error-secondary">
            <i class="fas fa-sign-in-alt mr-2"></i>Login
        </a>
        @if(Route::has('register'))
            <a href="{{ route('register') }}" class="btn-error btn-error-secondary">
                <i class="fas fa-user-plus mr-2"></i>Register
            </a>
        @endif
    @endif
@endsection

@section('support-info')
    Request access? Contact admin@eqmonitor.com
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #ffd166, #f8961e);
    }

    .seismic-wave {
        border-color: rgba(255, 209, 102, 0.3);
    }
</style>
@endpush
