<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error - EQMonitor')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #00bcd4;
            --accent-color: #ff4081;
            --dark-color: #0d1b2a;
            --light-color: #f8f9fa;
        }

        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            animation: fadeIn 0.6s ease-out;
        }

        .error-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: float 20s linear infinite;
        }

        .error-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            text-shadow: 3px 3px 0 rgba(0,0,0,0.1);
        }

        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .error-body {
            padding: 40px;
        }

        .error-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
            text-align: center;
        }

        .error-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--accent-color);
        }

        .error-details pre {
            background: white;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 0.9rem;
            margin: 0;
        }

        .error-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-error {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .btn-error-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .btn-error-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-error-secondary {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-error-secondary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .error-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }

        .earthquake-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .seismic-wave {
            position: absolute;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: seismic 2s ease-out infinite;
        }

        .seismic-wave:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 50%;
            left: 50%;
            margin-top: -50px;
            margin-left: -50px;
            animation-delay: 0s;
        }

        .seismic-wave:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 50%;
            margin-top: -100px;
            margin-left: -100px;
            animation-delay: 0.2s;
        }

        .seismic-wave:nth-child(3) {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 50%;
            margin-top: -150px;
            margin-left: -150px;
            animation-delay: 0.4s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes seismic {
            0% {
                transform: scale(0.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-body {
                padding: 20px;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn-error {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 4rem;
            }

            .error-header {
                padding: 20px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="error-container">
        <!-- Seismic Wave Animation -->
        <div class="earthquake-animation">
            <div class="seismic-wave"></div>
            <div class="seismic-wave"></div>
            <div class="seismic-wave"></div>
        </div>

        <!-- Error Header -->
        <div class="error-header">
            <div class="error-icon">
                @yield('error-icon')
            </div>
            <div class="error-code">
                @yield('error-code')
            </div>
            <div class="error-title">
                @yield('error-title')
            </div>
        </div>

        <!-- Error Body -->
        <div class="error-body">
            <div class="error-message">
                @yield('error-message')
            </div>

            @hasSection('error-details')
            <div class="error-details">
                @yield('error-details')
            </div>
            @endif

            <div class="error-actions">
                @yield('error-actions')
            </div>
        </div>

        <!-- Error Footer -->
        <div class="error-footer">
            <p>
                <i class="fas fa-earth-americas mr-2"></i>
                Earthquake Monitoring System &copy; {{ date('Y') }}
                @hasSection('support-info')
                | @yield('support-info')
                @endif
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Add random earthquake effect
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.error-container');
            const errorCode = document.querySelector('.error-code');

            // Add subtle shake effect for certain error codes
            const shakeCodes = [500, 503];
            const currentCode = parseInt(errorCode.textContent);

            if (shakeCodes.includes(currentCode)) {
                setInterval(() => {
                    container.style.transform = 'translateX(' + (Math.random() * 4 - 2) + 'px)';
                    setTimeout(() => {
                        container.style.transform = 'translateX(0)';
                    }, 100);
                }, 3000);
            }

            // Add click effect to seismic waves
            const waves = document.querySelectorAll('.seismic-wave');
            waves.forEach(wave => {
                wave.addEventListener('click', function() {
                    this.style.animation = 'none';
                    setTimeout(() => {
                        this.style.animation = 'seismic 2s ease-out infinite';
                    }, 10);
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
