@extends('layouts.error')

@section('title', '500 - Server Error')

@section('error-icon')
    <i class="fas fa-server"></i>
@endsection

@section('error-code')
    500
@endsection

@section('error-title')
    Internal Server Error
@endsection

@section('error-message')
    <p>Something went wrong on our servers. Our team has been notified and is working to fix the issue.</p>
    <p>Please try again later or contact support if the problem persists.</p>
@endsection

@section('error-details')
    @if(config('app.debug') && isset($exception))
        <p><strong>Error Details:</strong></p>
        <pre>{{ $exception->getMessage() }}</pre>
        <p class="mt-2"><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
        <p><strong>Trace:</strong></p>
        <pre>{{ $exception->getTraceAsString() }}</pre>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-tools mr-2"></i>
            Our technical team is already working to fix this issue. Thank you for your patience.
        </div>
    @endif
@endsection

@section('error-actions')
    <a href="{{ url('/') }}" class="btn-error btn-error-primary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
    <button onclick="window.location.reload()" class="btn-error btn-error-secondary">
        <i class="fas fa-redo mr-2"></i>Try Again
    </button>
    <a href="mailto:support@eqmonitor.com" class="btn-error btn-error-secondary">
        <i class="fas fa-envelope mr-2"></i>Contact Support
    </a>
@endsection

@section('support-info')
    Emergency? Call +62 21 1234 5678
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #ef476f, #d90429);
    }

    .seismic-wave {
        border-color: rgba(239, 71, 111, 0.3);
    }

    .alert-warning {
        background: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }
</style>
@endpush

@push('scripts')
<script>
    // Add more dramatic earthquake effect for 500 error
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.error-container');
        let shakeIntensity = 0;

        function shake() {
            shakeIntensity += 0.5;
            const x = (Math.random() * shakeIntensity - shakeIntensity/2);
            const y = (Math.random() * shakeIntensity - shakeIntensity/2);
            container.style.transform = `translate(${x}px, ${y}px)`;

            if (shakeIntensity < 5) {
                setTimeout(shake, 100);
            } else {
                // Reset shake
                setTimeout(() => {
                    shakeIntensity = 0;
                    container.style.transform = 'translate(0, 0)';
                    setTimeout(shake, 2000);
                }, 1000);
            }
        }

        shake();

        // Add server status check
        checkServerStatus();
    });

    function checkServerStatus() {
        fetch('/api/health')
            .then(response => {
                if (response.ok) {
                    showStatus('success', 'Server is responding normally');
                } else {
                    showStatus('warning', 'Server is experiencing issues');
                }
            })
            .catch(error => {
                showStatus('danger', 'Cannot connect to server');
            });
    }

    function showStatus(type, message) {
        const statusDiv = document.createElement('div');
        statusDiv.className = `alert alert-${type} mt-3`;
        statusDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>
            ${message}
        `;
        document.querySelector('.error-body').appendChild(statusDiv);
    }
</script>
@endpush
