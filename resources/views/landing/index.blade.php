@extends('layouts.landing')

@section('title', 'Home - Earthquake Monitoring System')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title animate__animated animate__fadeInUp">
                        Real-time Earthquake Monitoring with <span class="text-warning">IoT</span>
                    </h1>
                    <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                        Advanced monitoring system using SW-420 vibration sensors.
                        Get instant alerts and detailed analytics for earthquake detection.
                    </p>
                    <div class="d-flex flex-wrap gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                        <a href="{{ route('register') }}" class="btn btn-primary-custom">
                            Get Started <i class="fas fa-rocket ms-2"></i>
                        </a>
                        <a href="{{ route('features') }}" class="btn btn-outline-custom">
                            Learn More
                        </a>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="stats-card">
                                <div class="stats-number">24/7</div>
                                <div class="stats-label">Real-time Monitoring</div>
                            </div>
                        </div>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                            <div class="stats-card">
                                <div class="stats-number">&lt;5s</div>
                                <div class="stats-label">Alert Response Time</div>
                            </div>
                        </div>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                            <div class="stats-card">
                                <div class="stats-number">99.9%</div>
                                <div class="stats-label">System Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="device-animation">
                        <div class="sensor-visual">
                            <div class="text-center" style="position: relative; z-index: 10;">
                                <i class="fas fa-satellite-dish text-white" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-2 text-white fw-bold">SW-420 Sensor</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background: #f8f9fa;">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">
                    Our earthquake monitoring system comes with advanced features
                    to ensure safety and provide accurate data analysis
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Instant Alerts</h4>
                        <p class="text-muted">
                            Receive immediate notifications via SMS, Email, and Mobile App
                            when earthquake vibrations are detected above threshold levels.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Real-time Analytics</h4>
                        <p class="text-muted">
                            Monitor earthquake data with interactive charts and graphs.
                            Analyze historical data and vibration patterns for research.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h4 class="fw-bold mb-3">IoT Integration</h4>
                        <p class="text-muted">
                            Seamlessly connect multiple SW-420 sensors across different
                            locations for comprehensive earthquake monitoring network.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Location Mapping</h4>
                        <p class="text-muted">
                            Visualize earthquake events on interactive maps with precise
                            location tracking and affected area visualization.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Safety Protocols</h4>
                        <p class="text-muted">
                            Automated safety protocols and evacuation guidance based
                            on earthquake magnitude and location data.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Mobile Responsive</h4>
                        <p class="text-muted">
                            Access the monitoring dashboard from any device - desktop,
                            tablet, or smartphone with full functionality.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">
                    Our three-step process for effective earthquake monitoring
                </p>
            </div>

            <div class="row">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center p-4">
                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-4"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-satellite text-white" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">1. Sensor Detection</h4>
                        <p class="text-muted">
                            SW-420 vibration sensors detect seismic activity and
                            send data to our central server in real-time.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center p-4">
                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-4"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-server text-white" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">2. Data Processing</h4>
                        <p class="text-muted">
                            Our servers analyze the data, compare with thresholds,
                            and determine earthquake magnitude and risk level.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center p-4">
                        <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center mb-4"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-broadcast-tower text-white" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">3. Alert & Visualization</h4>
                        <p class="text-muted">
                            Instant alerts are sent to users, and data is visualized
                            on interactive dashboards for monitoring and analysis.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Alert -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="text-white fw-bold mb-4">Live Demo Alert</h2>
                    <p class="text-white mb-4">
                        Experience how our system sends earthquake alerts.
                        Below is a simulated earthquake alert that shows how
                        warnings are displayed in real-time.
                    </p>
                    <button class="btn btn-light btn-lg" onclick="triggerDemoAlert()">
                        <i class="fas fa-play me-2"></i> Trigger Demo Alert
                    </button>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div id="demoAlert" class="earthquake-alert" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center me-3"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">EARTHQUAKE ALERT!</h4>
                                <p class="mb-0">Simulated Demo - Magnitude: 5.8</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><i class="fas fa-map-pin me-2"></i> Location: Demo Area</p>
                                <p><i class="fas fa-clock me-2"></i> Time: Just Now</p>
                            </div>
                            <div class="col-md-6">
                                <p><i class="fas fa-shield-alt me-2"></i> Status: Danger Level</p>
                                <p><i class="fas fa-sensor me-2"></i> Device: SW-420 #001</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container py-5 text-center" data-aos="fade-up">
            <h2 class="section-title">Ready to Get Started?</h2>
            <p class="section-subtitle">
                Join hundreds of organizations using our earthquake monitoring system
                to ensure safety and preparedness.
            </p>
            <div class="mt-4">
                <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg me-3">
                    Start Free Trial
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline-custom btn-lg">
                    Contact Sales
                </a>
            </div>
        </div>
    </section>

    <script>
        function triggerDemoAlert() {
            const alertDiv = document.getElementById('demoAlert');
            alertDiv.style.display = 'block';

            // Auto-hide after 10 seconds
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 10000);

            // Play alert sound (optional)
            const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3');
            audio.play();
        }
    </script>
@endsection
