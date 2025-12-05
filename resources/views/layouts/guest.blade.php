<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Earthquake Monitoring System">
    <title>@yield('title', 'EQMonitor - Authentication')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --secondary: #1cc88a;
            --accent: #ff4081;
            --dark: #5a5c69;
            --light: #f8f9fc;
            --gray: #858796;
            --gray-light: #dddfeb;
            --danger: #e74a3b;
            --warning: #f6c23e;
            --info: #36b9cc;
            --success: #1cc88a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 550px;
        }

        .auth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .auth-logo {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .auth-logo i {
            color: var(--accent);
            font-size: 28px;
        }

        .auth-subtitle {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 400;
        }

        .auth-body {
            padding: 25px;
        }

        .auth-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
            text-align: center;
        }

        .auth-description {
            font-size: 13px;
            color: var(--gray);
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-label i {
            color: var(--primary);
            width: 14px;
            text-align: center;
            font-size: 12px;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            font-size: 13px;
            border: 1.5px solid var(--gray-light);
            border-radius: 6px;
            background: white;
            transition: all 0.2s ease;
            color: var(--dark);
            height: 40px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            padding: 4px;
            font-size: 12px;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .btn-auth {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 40px;
        }

        .btn-auth:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.2);
        }

        .auth-links {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--gray-light);
            text-align: center;
        }

        .auth-links a {
            color: var(--primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .alert-custom {
            padding: 10px 12px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: rgba(28, 200, 138, 0.1);
            color: #0f5132;
            border-left: 3px solid var(--success);
        }

        .alert-danger {
            background: rgba(231, 74, 59, 0.1);
            color: #721c24;
            border-left: 3px solid var(--danger);
        }

        .password-strength {
            height: 3px;
            background: var(--gray-light);
            border-radius: 2px;
            margin-top: 4px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.2s ease;
            border-radius: 2px;
        }

        .strength-0 { width: 20%; background: var(--danger); }
        .strength-1 { width: 40%; background: var(--warning); }
        .strength-2 { width: 60%; background: var(--info); }
        .strength-3 { width: 80%; background: var(--success); }
        .strength-4 { width: 100%; background: #1a8754; }

        .password-requirements {
            margin-top: 8px;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
        }

        .password-requirements li {
            font-size: 11px;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .terms-check {
            font-size: 12px;
            color: var(--dark);
        }

        .terms-check a {
            color: var(--primary);
            text-decoration: none;
        }

        .terms-check a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: var(--gray);
            font-size: 12px;
            margin-top: 8px;
        }

        .password-match {
            font-size: 11px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .compact-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }

        /* Compact form adjustments */
        .compact-form .form-group {
            margin-bottom: 12px;
        }

        .compact-form .auth-title {
            margin-bottom: 2px;
        }

        .compact-form .auth-description {
            margin-bottom: 15px;
        }

        @media (max-width: 576px) {
            .auth-wrapper {
                padding: 0;
            }

            .compact-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .password-requirements ul {
                grid-template-columns: 1fr;
            }

            body {
                padding: 15px;
            }
        }

        /* Make it fit in one screen */
        @media (max-height: 800px) {
            .auth-card {
                max-height: 95vh;
                overflow-y: auto;
            }

            .auth-card::-webkit-scrollbar {
                width: 4px;
            }

            .auth-card::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .auth-card::-webkit-scrollbar-thumb {
                background: var(--primary);
                border-radius: 2px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-earth-americas"></i>
                    EQMonitor
                </div>
                <div class="auth-subtitle">
                    Earthquake Monitoring System
                </div>
            </div>

            <!-- Body -->
            <div class="auth-body @if(request()->routeIs('register')) compact-form @endif">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.password-toggle');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Show validation errors with SweetAlert
            @if ($errors->any())
                @if ($errors->has('email') || $errors->has('password') || $errors->has('name'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: `
                            <div style="text-align: left; max-height: 150px; overflow-y: auto; font-size: 13px;">
                                @foreach ($errors->all() as $error)
                                    <p>• {{ $error }}</p>
                                @endforeach
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        width: 350
                    });
                @endif
            @endif

            // Show success message
            @if (session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session("status") }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
