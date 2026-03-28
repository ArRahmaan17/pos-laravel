@extends('template.single')
@section('title', 'Welcome')
@section('content')
<style>
    :root {
        --primary-glow: conic-gradient(from 180deg at 50% 50%, #16abff33 0deg, #0885ff33 55deg, #54d6ff33 120deg, #0071ff33 160deg, transparent 360deg);
        --secondary-glow: radial-gradient(rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    body {
        background: #1e293b;
        color: #fff;
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: hidden;
    }

    .hero-section {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding-top: 80px;
    }

    .hero-glow {
        position: absolute;
        width: 500px;
        height: 500px;
        background: var(--primary-glow);
        filter: blur(80px);
        z-index: -1;
        opacity: 0.5;
        animation: float-glow 20s infinite alternate;
    }

    @keyframes float-glow {
        from {
            transform: translate(-10%, -10%);
        }

        to {
            transform: translate(20%, 20%);
        }
    }

    .navbar-glass {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(15px);
        border-bottom: 1px solid var(--glass-border);
        padding: 1rem 0;
    }

    .brand-text {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #fff 0%, #888 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }

    .hero-title {
        font-size: clamp(2.5rem, 8vw, 4.5rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.5rem;
        background: linear-gradient(to right, #fff, #696cff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: #a1acb8;
        max-width: 600px;
        margin-bottom: 2.5rem;
    }

    .btn-premium {
        background: #696cff;
        color: #fff;
        padding: 1rem 2.5rem;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: inline-block;
        box-shadow: 0 10px 20px -5px rgba(105, 108, 255, 0.4);
    }

    .btn-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -5px rgba(105, 108, 255, 0.6);
        color: #fff;
        background: #5f61e6;
    }

    .btn-outline-premium {
        background: transparent;
        color: #fff;
        padding: 1rem 2.5rem;
        border-radius: 12px;
        font-weight: 700;
        border: 1px solid var(--glass-border);
        transition: all 0.3s ease;
        display: inline-block;
        margin-left: 1rem;
    }

    .btn-outline-premium:hover {
        background: var(--glass-bg);
        border-color: #696cff;
        color: #fff;
    }

    .hero-image-wrapper {
        position: relative;
        perspective: 1000px;
    }

    .hero-image {
        width: 100%;
        border-radius: 24px;
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.5);
        border: 1px solid var(--glass-border);
        transform: rotateY(-5deg) rotateX(5deg);
        transition: transform 0.5s ease;
    }

    .hero-image-wrapper:hover .hero-image {
        transform: rotateY(0deg) rotateX(0deg);
    }

    .stats-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        padding: 2rem;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        border-color: #696cff;
        transform: translateY(-5px);
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        background: rgba(105, 108, 255, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #696cff;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* Animation */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        animation: reveal 0.8s forwards;
    }

    @keyframes reveal {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .delay-1 {
        animation-delay: 0.2s;
    }

    .delay-2 {
        animation-delay: 0.4s;
    }

    .delay-3 {
        animation-delay: 0.6s;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-glass fixed-top">
    <div class="container">
        <a class="navbar-brand brand-text" href="{{ route('home') }}">
            {{ env('APP_NAME') }}
        </a>
        <div class="d-flex">
            @if(session('userLogged'))
            <a href="{{ route('dashboard.index') }}" class="btn btn-premium btn-sm px-4">Dashboard</a>
            @else
            <a href="{{ route('auth.login') }}" class="btn btn-premium btn-sm px-4">Login</a>
            @endif
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="hero-glow" style="top: 10%; left: -10%;"></div>
    <div class="hero-glow" style="bottom: 10%; right: -10%; background: var(--secondary-glow); opacity: 0.2;"></div>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 reveal">
                <h1 class="hero-title">Elevate Your <br>Business Flow</h1>
                <p class="hero-subtitle">The next generation Point of Sale system designed for clarity, speed, and growth. Manage everything from one sleek command center.</p>
                <div class="d-flex">
                    <a href="{{ route('auth.registration') }}" class="btn btn-premium">Get Started</a>
                    <a href="#features" class="btn btn-outline-premium">Learn More</a>
                </div>

                <div class="row mt-5">
                    <div class="col-4">
                        <h4 class="mb-0">10k+</h4>
                        <small class="text-muted">Active Users</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0">99.9%</h4>
                        <small class="text-muted">Uptime</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 reveal delay-1">
                <div class="hero-image-wrapper">
                    {{-- Note: User can see the image generated earlier embeded in the artifact --}}
                    <img src="{{ asset('pos_hero_banner.png') }}" class="hero-image" alt="POS Dashboard Preview">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <h2 class="fw-bold fs-1">Powering Your Success</h2>
            <p class="text-muted">Advanced features without the complexity.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 reveal delay-1">
                <div class="stats-card">
                    <div class="feature-icon">
                        <i class='bx bxs-bolt'></i>
                    </div>
                    <h4>Real-time Analytics</h4>
                    <p class="text-muted mb-0">Monitor your sales and inventory changes as they happen with pinpoint accuracy.</p>
                </div>
            </div>
            <div class="col-md-4 reveal delay-2">
                <div class="stats-card">
                    <div class="feature-icon">
                        <i class='bx bxs-shield-alt'></i>
                    </div>
                    <h4>Enterprise Security</h4>
                    <p class="text-muted mb-0">Role-based access control and high-level encryption for your sensitive data.</p>
                </div>
            </div>
            <div class="col-md-4 reveal delay-3">
                <div class="stats-card">
                    <div class="feature-icon">
                        <i class="bx bx-grid"></i>
                    </div>
                    <h4>Modular Design</h4>
                    <p class="text-muted mb-0">Scale your business by adding more modules as your needs grow over time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="py-5 mt-5 border-top border-dark">
    <div class="container text-center">
        <p class="text-muted">© {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
    </div>
</footer>

@endsection