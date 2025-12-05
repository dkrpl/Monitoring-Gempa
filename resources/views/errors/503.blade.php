@extends('layouts.error')

@section('title', '503 - Service Unavailable')

@section('error-icon')
    <i class="fas fa-tools"></i>
@endsection

@section('error-code')
    503
@endsection

@section('error-title')
    Service Unavailable
@endsection

@section('error-message')
    <p>The server is temporarily unable to service your request due to maintenance or capacity problems.</p>
    <p>We're working hard to restore service as quickly as possible. Thank you for your patience.</p>
@endsection

@section('error-details')
    <div class="alert alert-warning">
        <i class="fas fa-clock mr-2"></i>
        <strong>Scheduled Maintenance:</strong><br>
        • Every Sunday: 02:00 - 04:00 UTC<br>
        • Emergency maintenance as needed<br>
        • Estimated downtime: 30 minutes
    </div>

    <div class="progress mt-3" style="height: 10px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated"
             role="progressbar"
             style="width: 75%">
        </div>
    </div>
    <p class="text-center small text-muted mt-2">System restoration in progress...</p>
@endsection

@section('error-actions')
    <button onclick="window.location.reload()" class="btn-error btn-error-primary" id="statusCheckBtn">
        <i class="fas fa-sync-alt mr-2"></i>Check Status
    </button>
    <a href="https://status.eqmonitor.com" target="_blank" class="btn-error btn-error-secondary">
        <i class="fas fa-heartbeat mr-2"></i>Status Page
    </a>
    <a href="mailto:support@eqmonitor.com" class="btn-error btn-error-secondary">
        <i class="fas fa-envelope mr-2"></i>Get Updates
    </a>
@endsection

@section('support-info')
    Live status: status.eqmonitor.com
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #f8961e, #f3722c);
    }

    .seismic-wave {
        border-color: rgba(248, 150, 30, 0.3);
    }

    .progress-bar {
        background: linear-gradient(90deg, #f8961e, #f3722c);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusBtn = document.getElementById('statusCheckBtn');
        let checkCount = 0;

        statusBtn.addEventListener('click', function() {
            checkCount++;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
            this.disabled = true;

            // Simulate status check
            setTimeout(() => {
                this.disabled = false;

                if (checkCount % 3 === 0) {
                    // Every 3rd check shows success
                    this.innerHTML = '<i class="fas fa-check mr-2"></i>Service Restored!';
                    showStatusMessage('success', 'Service has been restored. Refreshing...');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.innerHTML = originalText;
                    showStatusMessage('warning', 'Service is still being restored. Please wait.');
                }
            }, 2000);
        });

        function showStatusMessage(type, message) {
            // Remove existing status message
            const existingMsg = document.querySelector('.status-message');
            if (existingMsg) existingMsg.remove();

            const statusDiv = document.createElement('div');
            statusDiv.className = `alert alert-${type} mt-3 status-message`;
            statusDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>
                ${message}
            `;
            document.querySelector('.error-body').appendChild(statusDiv);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (statusDiv.parentNode) {
                    statusDiv.remove();
                }
            }, 5000);
        }

        // Auto-check status every 30 seconds
        setInterval(() => {
            if (!statusBtn.disabled) {
                statusBtn.click();
            }
        }, 30000);

        // Animate progress bar
        const progressBar = document.querySelector('.progress-bar');
        let progress = 75;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 5;
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);
                showStatusMessage('success', 'Maintenance completed!');
                setTimeout(() => window.location.reload(), 3000);
            }
            progressBar.style.width = `${progress}%`;
        }, 3000);
    });
</script>
@endpush
