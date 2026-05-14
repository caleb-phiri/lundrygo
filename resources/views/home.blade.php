@extends('layouts.app')

@section('title', 'Fresh Laundry & Dry Cleaning Services - ' . config('app.name'))

@section('content')

<!-- HERO SECTION -->
<section class="hero-section position-relative overflow-hidden">
    <div class="hero-overlay"></div>

    <div class="container position-relative py-5">
        <div class="row align-items-center min-vh-75">

            <div class="col-lg-6 text-white">
                <span class="hero-badge mb-3 d-inline-block">
                    <i class="fas fa-star me-2"></i>Trusted Laundry Experts
                </span>

                <h1 class="display-3 fw-bold mb-4">
                    Fast & Reliable Laundry Pickup Service
                </h1>

                <p class="lead mb-4 text-light">
                    We collect, wash, iron, fold, and deliver your clothes with care.
                    Affordable, eco-friendly, and always on time.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 py-3 rounded-pill">
                            <i class="fas fa-user-plus me-2"></i>Get Started
                        </a>

                        <a href="{{ route('services') }}" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill">
                            <i class="fas fa-list me-2"></i>Explore Services
                        </a>
                    @else
                        <a href="{{ route('customer.orders.create') }}" class="btn btn-light btn-lg px-4 py-3 rounded-pill">
                            <i class="fas fa-shopping-bag me-2"></i>Place Order
                        </a>
                    @endguest
                </div>

                <div class="hero-features mt-5">
                    <div class="row">
                        <div class="col-4">
                            <div class="feature-mini">
                                <i class="fas fa-truck"></i>
                                <small>Free Pickup</small>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="feature-mini">
                                <i class="fas fa-clock"></i>
                                <small>24hr Delivery</small>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="feature-mini">
                                <i class="fas fa-leaf"></i>
                                <small>Eco Friendly</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <img src="{{ asset('images/hero-laundry.svg') }}"
                     alt="Laundry Service"
                     class="img-fluid hero-image">
            </div>

        </div>
    </div>
</section>


<!-- STATS SECTION -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row g-4">

            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>

                    <h2>{{ number_format($stats['orders_delivered']) }}+</h2>
                    <p>Orders Delivered</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-smile"></i>
                    </div>

                    <h2>{{ number_format($stats['happy_customers']) }}+</h2>
                    <p>Happy Customers</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-motorcycle"></i>
                    </div>

                    <h2>{{ number_format($stats['active_riders']) }}+</h2>
                    <p>Delivery Riders</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>

                    <h2>{{ $stats['years_experience'] }}+</h2>
                    <p>Years Experience</p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- SERVICES -->
<section class="services-section py-5">
    <div class="container">

        <div class="section-title text-center mb-5">
            <h2 class="fw-bold">Our Laundry Services</h2>
            <p class="text-muted">
                Professional care for all your clothing and fabrics
            </p>
        </div>

        <div class="row g-4">

            @foreach($featuredServices as $service)
            <div class="col-lg-4 col-md-6">

                <div class="service-card">

                    <div class="service-image">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}"
                                 alt="{{ $service->name }}">
                        @else
                            <div class="placeholder-image">
                                <i class="fas fa-shirt"></i>
                            </div>
                        @endif
                    </div>

                    <div class="service-content">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ $service->name }}</h5>

                            <div class="price-tag">
                                ${{ number_format($service->price, 2) }}
                            </div>
                        </div>

                        <p class="text-muted">
                            {{ Str::limit($service->description, 100) }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted">
                                Per {{ $service->unit }}
                            </small>

                            <a href="{{ route('customer.orders.create') }}"
                               class="btn btn-primary rounded-pill px-4">
                                Book Now
                            </a>
                        </div>

                    </div>

                </div>

            </div>
            @endforeach

        </div>

        <div class="text-center mt-5">
            <a href="{{ route('services') }}"
               class="btn btn-dark btn-lg rounded-pill px-5">
                View All Services
            </a>
        </div>

    </div>
</section>


<!-- HOW IT WORKS -->
<section class="how-section py-5 bg-light">
    <div class="container">

        <div class="section-title text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">
                Easy steps to get your laundry done
            </p>
        </div>

        <div class="row text-center">

            <div class="col-md-4">
                <div class="how-card">
                    <div class="how-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>

                    <h4>Schedule Pickup</h4>

                    <p>
                        Book your pickup online in just a few clicks.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="how-card">
                    <div class="how-icon">
                        <i class="fas fa-soap"></i>
                    </div>

                    <h4>We Clean</h4>

                    <p>
                        Your clothes are professionally cleaned and ironed.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="how-card">
                    <div class="how-icon">
                        <i class="fas fa-truck"></i>
                    </div>

                    <h4>Fast Delivery</h4>

                    <p>
                        Fresh laundry delivered back to your doorstep.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- REVIEWS -->
<section class="reviews-section py-5">
    <div class="container">

        <div class="section-title text-center mb-5">
            <h2 class="fw-bold">Customer Reviews</h2>
        </div>

        <div class="row g-4">

            @foreach($reviews as $review)
            <div class="col-lg-4">

                <div class="review-card h-100">

                    <div class="mb-3 text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                        @endfor
                    </div>

                    <p class="review-text">
                        "{{ Str::limit($review->review_text, 140) }}"
                    </p>

                    <div class="review-user">
                        <div class="avatar">
                            {{ substr($review->user->name, 0, 1) }}
                        </div>

                        <div>
                            <h6 class="mb-0">{{ $review->user->name }}</h6>
                            <small class="text-muted">
                                {{ $review->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>

                </div>

            </div>
            @endforeach

        </div>

    </div>
</section>


<!-- CTA -->
<section class="cta-section py-5">
    <div class="container text-center text-white">

        <h2 class="display-5 fw-bold mb-4">
            Ready for Fresh & Clean Clothes?
        </h2>

        <p class="lead mb-4">
            Book your first pickup today and enjoy premium laundry care.
        </p>

        @guest
            <a href="{{ route('register') }}"
               class="btn btn-light btn-lg rounded-pill px-5 py-3">
                Get Started Today
            </a>
        @else
            <a href="{{ route('customer.orders.create') }}"
               class="btn btn-light btn-lg rounded-pill px-5 py-3">
                Schedule Pickup
            </a>
        @endguest

    </div>
</section>

@endsection


@push('styles')
<style>

:root{
    --primary:#2563eb;
    --secondary:#0f172a;
    --light:#f8fafc;
}

body{
    font-family: 'Poppins', sans-serif;
    background:#fff;
}

/* HERO */

.hero-section{
    background: linear-gradient(135deg,#2563eb,#1e40af);
    min-height: 90vh;
}

.hero-overlay{
    position:absolute;
    inset:0;
    background: rgba(0,0,0,0.15);
}

.min-vh-75{
    min-height:75vh;
}

.hero-badge{
    background: rgba(255,255,255,0.15);
    padding:10px 20px;
    border-radius:50px;
    backdrop-filter: blur(10px);
}

.hero-image{
    max-height:500px;
    animation: float 4s ease-in-out infinite;
}

@keyframes float{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-10px);
    }
}

.feature-mini{
    text-align:center;
}

.feature-mini i{
    display:block;
    font-size:24px;
    margin-bottom:10px;
}

/* STATS */

.stats-section{
    background:#f8fafc;
    margin-top:-60px;
    position:relative;
    z-index:10;
}

.stat-card{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    transition:0.3s;
}

.stat-card:hover{
    transform:translateY(-8px);
}

.stat-icon{
    width:80px;
    height:80px;
    margin:auto;
    border-radius:50%;
    background:#eff6ff;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:20px;
}

.stat-icon i{
    font-size:32px;
    color:var(--primary);
}

/* SERVICES */

.section-title h2{
    font-size:42px;
}

.service-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 8px 30px rgba(0,0,0,0.08);
    transition:0.3s;
    height:100%;
}

.service-card:hover{
    transform:translateY(-10px);
}

.service-image img{
    width:100%;
    height:240px;
    object-fit:cover;
}

.placeholder-image{
    height:240px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#2563eb,#1e40af);
}

.placeholder-image i{
    font-size:70px;
    color:white;
}

.service-content{
    padding:25px;
}

.price-tag{
    background:#2563eb;
    color:white;
    padding:8px 15px;
    border-radius:50px;
    font-weight:600;
}

/* HOW IT WORKS */

.how-card{
    padding:40px 25px;
}

.how-icon{
    width:90px;
    height:90px;
    border-radius:50%;
    background:white;
    margin:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    margin-bottom:25px;
}

.how-icon i{
    font-size:38px;
    color:var(--primary);
}

/* REVIEWS */

.review-card{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.review-text{
    font-style:italic;
    color:#555;
}

.review-user{
    display:flex;
    align-items:center;
    margin-top:25px;
}

.avatar{
    width:55px;
    height:55px;
    border-radius:50%;
    background:var(--primary);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
    margin-right:15px;
}

/* CTA */

.cta-section{
    background:linear-gradient(135deg,#0f172a,#1e293b);
}

/* BUTTONS */

.btn{
    transition:0.3s;
}

.btn:hover{
    transform:translateY(-2px);
}

/* RESPONSIVE */

@media(max-width:768px){

    .hero-section{
        text-align:center;
    }

    .display-3{
        font-size:2.5rem;
    }

    .section-title h2{
        font-size:32px;
    }

}

</style>
@endpush