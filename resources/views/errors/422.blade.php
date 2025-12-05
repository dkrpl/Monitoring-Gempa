@extends('layouts.error')

@section('title', '422 - Unprocessable Entity')

@section('error-icon')
    <i class="fas fa-exclamation-circle"></i>
@endsection

@section('error-code')
    422
@endsection

@section('error-title')
    Unprocessable Entity
@endsection

@section('error-message')
    <p>The server understands the content type of the request entity, but was unable to process the contained instructions.</p>
    <p>This is usually caused by invalid form data or missing required fields.</p>
@endsection

@section('error-details')
    @if(config('app.debug') && isset($exception) && $exception instanceof \Illuminate\Validation\ValidationException)
        <p><strong>Validation Errors:</strong></p>
        <ul class="list-group">
            @foreach($exception->errors() as $field => $errors)
                @foreach($errors as $error)
                    <li class="list-group-item list-group-item-danger">
                        <strong>{{ $field }}:</strong> {{ $error }}
                    </li>
                @endforeach
            @endforeach
        </ul>
    @elseif(config('app.debug') && isset($exception))
        <pre>{{ $exception->getMessage() }}</pre>
    @else
        <p><i class="fas fa-edit mr-2"></i>Please check your form data and try again.</p>
    @endif
@endsection

@section('error-actions')
    <button onclick="window.history.back()" class="btn-error btn-error-primary">
        <i class="fas fa-edit mr-2"></i>Go Back & Edit
    </button>
    <a href="{{ url('/') }}" class="btn-error btn-error-secondary">
        <i class="fas fa-home mr-2"></i>Go to Homepage
    </a>
    <button onclick="clearFormData()" class="btn-error btn-error-secondary">
        <i class="fas fa-eraser mr-2"></i>Clear Form
    </button>
@endsection

@section('support-info')
    Form issues? Check validation rules
@endsection

@push('styles')
<style>
    .error-header {
        background: linear-gradient(135deg, #f94144, #f3722c);
    }

    .seismic-wave {
        border-color: rgba(249, 65, 68, 0.3);
    }

    .list-group-item-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Try to recover form data
        try {
            const formData = sessionStorage.getItem('lastFormData');
            if (formData) {
                showFormRecovery(JSON.parse(formData));
            }
        } catch (e) {
            console.log('No form data to recover');
        }
    });

    function showFormRecovery(data) {
        const recoveryDiv = document.createElement('div');
        recoveryDiv.className = 'alert alert-info mt-3';
        recoveryDiv.innerHTML = `
            <h6><i class="fas fa-history mr-2"></i>Form Data Recovery</h6>
            <p>We saved your form data. Click below to restore:</p>
            <button onclick="restoreFormData()" class="btn btn-sm btn-outline-info">
                <i class="fas fa-undo mr-2"></i>Restore Form Data
            </button>
            <button onclick="discardFormData()" class="btn btn-sm btn-outline-secondary ml-2">
                <i class="fas fa-trash mr-2"></i>Discard
            </button>
        `;
        document.querySelector('.error-body').appendChild(recoveryDiv);
    }

    function restoreFormData() {
        try {
            const formData = JSON.parse(sessionStorage.getItem('lastFormData'));
            alert('Form data would be restored in a real application');
            // In real app, this would populate the form
            window.history.back();
        } catch (e) {
            alert('Unable to restore form data');
        }
    }

    function discardFormData() {
        sessionStorage.removeItem('lastFormData');
        document.querySelector('.alert-info').remove();
    }

    function clearFormData() {
        sessionStorage.removeItem('lastFormData');
        localStorage.removeItem('formDraft');
        alert('All form data has been cleared');
        window.history.back();
    }

    // Save form data on form submit (this would be in your main JS)
    if (typeof window.saveFormData === 'undefined') {
        window.saveFormData = function(formId) {
            const form = document.getElementById(formId);
            if (form) {
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                sessionStorage.setItem('lastFormData', JSON.stringify(data));
            }
        };
    }
</script>
@endpush
