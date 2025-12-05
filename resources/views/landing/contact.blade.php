@extends('layouts.landing')

@section('title', 'Contact Us - Earthquake Monitoring System')

@section('content')
    <!-- Hero -->
    <section class="hero-section" style="background: linear-gradient(135deg, #00bcd4 0%, #ff4081 100%);">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">Get In Touch</h1>
                    <p class="hero-subtitle">
                        Have questions? We're here to help you with your earthquake monitoring needs
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="py-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-form" data-aos="fade-up">
                        <h2 class="fw-bold mb-4 text-center">Send us a Message</h2>
                        <form id="contactForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <input type="text" class="form-control form-control-custom" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <input type="email" class="form-control form-control-custom" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Subject</label>
                                <input type="text" class="form-control form-control-custom" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Message</label>
                                <textarea class="form-control form-control-custom" rows="5" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100">
                                Send Message <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
