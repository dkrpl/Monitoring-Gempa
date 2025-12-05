@extends('layouts.error')

@section('title', '429 - Too Many Requests')

@section('error-icon')
    <i class="fas fa-traffic-light"></i>
@endsection

@section('error-code')
    429
@endsection

@section('error-title')
    Too Many Requests
@endsection

@section('error-message')
    <p>You have sent too many requests in a short amount of time.</p>
    <p>Please wait a moment before trying again. This helps us prevent abuse and ensure service quality for all users.</p>
@endsection

@section('error-details')
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Rate Limit Info:</strong><br>
        • Maximum 60 requests per minute<br>
        • Maximum 1000 requests per hour<br>
        • Limits reset automatically
    </div>
@endsection

@section('error-actions')
    <button onclick="window.location.reload()" class="btn-error btn-error-primary" id="retryBtn" disabled>
        <i class="fas fa-redo mr-2"></i>
        Try Again <span id="retryTimer">(60s)</span>
    </button>
    <a href="{{ url('/') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
    <a href="{{ route('contact') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-question-circle mr-2"></i>Need Higher Limits?
    </a>
@endsection

@section('support-info')
    API limits? Contact api@eqmonitor.com
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #7209b7, #3a0ca3);
    }

    .seismic-wave {
        border-color: rgba(114, 9, 183, 0.3);
    }

    .alert-info {
        background: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    #retryBtn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Countdown timer for retry button
        let secondsLeft = 60;
        const retryBtn = document.getElementById('retryBtn');
        const retryTimer = document.getElementById('retryTimer');

        function updateTimer() {
            secondsLeft--;
            retryTimer.textContent = `(${secondsLeft}s)`;

            if (secondsLeft <= 0) {
                retryBtn.disabled = false;
                retryBtn.innerHTML = '<i class="fas fa-redo mr-2"></i>Try Again';
                clearInterval(timerInterval);
            }
        }

        const timerInterval = setInterval(updateTimer, 1000);

        // When retry is clicked
        retryBtn.addEventListener('click', function() {
            if (!this.disabled) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Retrying...';
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        });

        // Show estimated reset time
        const resetTime = new Date(Date.now() + 60000);
        document.querySelector('.error-details').innerHTML += `
            <p class="mt-2 mb-0">
                <i class="fas fa-clock mr-2"></i>
                <strong>Estimated reset:</strong> ${resetTime.toLocaleTimeString()}
            </p>
        `;
    });
</script>
@endpush
