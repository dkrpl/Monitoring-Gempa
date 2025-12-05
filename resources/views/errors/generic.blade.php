@extends('layouts.error')

@section('title', 'Error - EQMonitor')

@section('error-icon')
    <i class="fas fa-exclamation-triangle"></i>
@endsection

@section('error-code')
    {{ $statusCode ?? 'Error' }}
@endsection

@section('error-title')
    {{ $message ?? 'Something went wrong' }}
@endsection

@section('error-message')
    <p>An unexpected error has occurred. Our team has been notified.</p>
    <p>Please try again later or contact support if the problem persists.</p>
@endsection

@section('error-details')
    @if(config('app.debug') && isset($exception))
        <p><strong>Error Details:</strong></p>
        <pre>{{ $exception->getMessage() }}</pre>
        @if(isset($exception) && method_exists($exception, 'getFile'))
            <p class="mt-2"><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
        @endif
    @else
        <p><i class="fas fa-bug mr-2"></i>Error code: {{ $statusCode ?? 'Unknown' }}</p>
    @endif
@endsection

@section('error-actions')
    <a href="{{ url('/') }}" class="btn-error btn-error-primary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
    <button onclick="window.history.back()" class="btn-error btn-error-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Go Back
    </button>
    <button onclick="window.location.reload()" class="btn-error btn-error-secondary">
        <i class="fas fa-redo mr-2"></i>Try Again
    </button>
@endsection

@section('support-info')
    Need help? Contact support@eqmonitor.com
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #6a040f, #9d0208);
    }

    .seismic-wave {
        border-color: rgba(106, 4, 15, 0.3);
    }
</style>
@endpush
