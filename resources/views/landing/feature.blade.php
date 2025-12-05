@extends('layouts.landing')

@section('title', 'Features - Earthquake Monitoring System')

@section('content')
    <!-- Hero -->
    <section class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">Advanced Features</h1>
                    <p class="hero-subtitle">
                        Discover the powerful capabilities of our earthquake monitoring system
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Detail -->
    <section class="py-5">
        <div class="container py-5">
            <div class="row align-items-center mb-5" data-aos="fade-up">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         class="img-fluid rounded-3 shadow" alt="Sensor Network">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">IoT Sensor Network</h2>
                    <p class="text-muted mb-4">
                        Deploy multiple SW-420 vibration sensors across different locations
                        to create a comprehensive earthquake monitoring network.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Real-time data transmission</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Low power consumption</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Wireless connectivity options</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Weather-resistant casing</li>
                    </ul>
                </div>
            </div>

            <div class="row align-items-center mb-5" data-aos="fade-up">
                <div class="col-lg-6 order-lg-2">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         class="img-fluid rounded-3 shadow" alt="Dashboard">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h2 class="fw-bold mb-4">Interactive Dashboard</h2>
                    <p class="text-muted mb-4">
                        Monitor all your sensors from a single, intuitive dashboard with
                        real-time updates and comprehensive analytics.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Live data visualization</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Customizable widgets</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Multi-device support</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Export data functionality</li>
                    </ul>
                </div>
            </div>

            <!-- More features would continue here -->
        </div>
    </section>
@endsection
