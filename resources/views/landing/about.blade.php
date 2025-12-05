@extends('layouts.landing')

@section('title', 'About Us - Earthquake Monitoring System')

@section('content')
    <!-- Hero -->
    <section class="hero-section" style="background: linear-gradient(135deg, #1a237e 0%, #00bcd4 100%);">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">About EQMonitor</h1>
                    <p class="hero-subtitle">
                        Pioneering earthquake monitoring technology for a safer tomorrow
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-5">
        <div class="container py-5">
            <div class="row mb-5" data-aos="fade-up">
                <div class="col-lg-10 mx-auto text-center">
                    <h2 class="fw-bold mb-4">Our Mission</h2>
                    <p class="lead text-muted">
                        To provide reliable, real-time earthquake monitoring solutions that
                        save lives and protect property through early warning systems and
                        advanced analytics.
                    </p>
                </div>
            </div>

            <!-- Mission, Vision, Values would continue here -->
        </div>
    </section>
@endsection
