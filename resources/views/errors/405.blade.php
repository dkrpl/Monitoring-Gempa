@extends('layouts.error')

@section('title', '405 - Method Not Allowed')

@section('error-icon')
    <i class="fas fa-exchange-alt"></i>
@endsection

@section('error-code')
    405
@endsection

@section('error-title')
    Method Not Allowed
@endsection

@section('error-message')
    <p>The request method is not supported for this resource.</p>
    <p>You tried to access this page using an incorrect HTTP method.</p>
@endsection

@section('error-details')
    @if(config('app.debug') && isset($exception))
        <p><strong>Error Details:</strong></p>
        <pre>{{ $exception->getMessage() }}</pre>
        <p class="mt-2"><strong>Request Method:</strong> {{ request()->method() }}</p>
        <p><strong>Allowed Methods:</strong> GET, POST</p>
    @else
        <p><i class="fas fa-code mr-2"></i>This endpoint only accepts specific HTTP methods.</p>
    @endif
@endsection

@section('error-actions')
    <a href="{{ url('/') }}" class="btn-error btn-error-primary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
    <button onclick="window.history.back()" class="btn-error btn-error-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Go Back
    </button>
    <a href="{{ route('contact') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-bug mr-2"></i>Report Issue
    </a>
@endsection

@section('support-info')
    API documentation at docs.eqmonitor.com
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #4cc9f0, #4361ee);
    }

    .seismic-wave {
        border-color: rgba(76, 201, 240, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show HTTP method animation
        const methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
        let currentIndex = 0;
        const methodDisplay = document.createElement('div');
        methodDisplay.className = 'text-center mt-3';
        methodDisplay.innerHTML = `
            <p class="text-muted">
                <i class="fas fa-exchange-alt mr-2"></i>
                Current method: <span id="currentMethod" class="badge bg-info">${request.method}</span>
            </p>
            <p class="small text-muted">
                Allowed methods: ${methods.join(', ')}
            </p>
        `;

        document.querySelector('.error-body').appendChild(methodDisplay);

        // Animate through methods
        setInterval(() => {
            currentIndex = (currentIndex + 1) % methods.length;
            document.getElementById('currentMethod').textContent = methods[currentIndex];
            document.getElementById('currentMethod').className = `badge bg-${['info','success','warning','danger','primary'][currentIndex % 5]}`;
        }, 1000);
    });
</script>
@endpush
