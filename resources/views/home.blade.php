@extends('layouts.app')

@section('title', 'Fresh Laundry & Dry Cleaning Services - ' . config('app.name'))

@section('content')

<!-- ================= HERO SECTION ================= -->
<section class="hero-section position-relative overflow-hidden">
    <div class="hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center min-vh-75">

            <div class="col-lg-6 text-white">
                <span class="hero-badge mb-3 d-inline-block">
                    <i class="fas fa-star me-2"></i>Trusted Laundry & Dry Cleaning Partner
                </span>

                <h1 class="display-4 fw-bold mb-4">
                    Professional Laundry Services You Can Trust
                </h1>

                <p class="lead mb-4 text-white-50">
                    We handle pickup, cleaning, ironing, and delivery with precision and care.
                    Reliable, affordable, and built for your convenience.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Get Started
                        </a>
                        <a href="{{ route('services') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-list me-2"></i>View Services
                        </a>
                    @else
                        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary-custom btn-lg">
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

            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <img src="{{ asset('images/hero-laundry.svg') }}"
                     class="img-fluid hero-image"
                     style="max-height: 400px;"
                     alt="Laundry Service"
                     onerror="this.src='https://placehold.co/500x400?text=Premium+Laundry+Service'">
            </div>

        </div>
    </div>
</section>

<!-- ================= STATS SECTION ================= -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row g-4">

            @foreach([
                ['icon' => 'tshirt', 'label' => 'Orders Delivered', 'value' => $stats['orders_delivered'] ?? 12580, 'suffix' => '+'],
                ['icon' => 'smile', 'label' => 'Happy Customers', 'value' => $stats['happy_customers'] ?? 9850, 'suffix' => '+'],
                ['icon' => 'motorcycle', 'label' => 'Delivery Riders', 'value' => $stats['active_riders'] ?? 35, 'suffix' => '+'],
                ['icon' => 'award', 'label' => 'Years Experience', 'value' => $stats['years_experience'] ?? 5, 'suffix' => '+'],
            ] as $stat)

            <div class="col-md-3 col-6">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-{{ $stat['icon'] }}"></i>
                    </div>
                    <h2 class="stat-number mb-1" data-target="{{ $stat['value'] }}">0</h2>
                    <p class="text-muted mb-0">{{ $stat['label'] }}</p>
                </div>
            </div>

            @endforeach

        </div>
    </div>
</section>

<!-- ================= SERVICES SECTION ================= -->
<section class="services-section py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">Our Services</h2>
            <p class="text-muted">Professional garment care solutions for every need</p>
        </div>

        <div class="row g-4">

            @forelse($featuredServices ?? [] as $service)
            <div class="col-lg-4 col-md-6">

                <div class="service-card">
                    <div class="service-image">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}"
                                 alt="{{ $service->name }}"
                                 loading="lazy">
                        @else
                            <div class="placeholder-image">
                                <i class="fas fa-tshirt"></i>
                            </div>
                        @endif
                    </div>

                    <div class="service-content">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">{{ $service->name }}</h5>
                            <div class="price-tag">
                                ${{ number_format($service->price, 2) }}
                            </div>
                        </div>

                        <p class="text-muted small">
                            {{ Str::limit($service->description ?? 'Professional laundry service with care and quality.', 100) }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-weight-hanging me-1"></i> Per {{ $service->unit ?? 'kg' }}
                            </small>

                            <a href="{{ route('customer.orders.create') }}"
                               class="btn btn-outline-primary rounded-pill px-4">
                                Book Now <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            @empty
            <!-- Demo Service Cards -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-image">
                        <div class="placeholder-image">
                            <i class="fas fa-tshirt"></i>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">Wash & Fold</h5>
                            <div class="price-tag">$6.00</div>
                        </div>
                        <p class="text-muted small">Professional washing, careful folding, and fresh scent guaranteed. Perfect for daily wear.</p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted"><i class="fas fa-weight-hanging me-1"></i> Per 5kg bag</small>
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-outline-primary rounded-pill px-4">Book Now <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-image">
                        <div class="placeholder-image">
                            <i class="fas fa-dryer"></i>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">Dry Cleaning</h5>
                            <div class="price-tag">$12.00</div>
                        </div>
                        <p class="text-muted small">Eco-friendly solvents, stain specialists, hand finishing for suits, dresses, and silk.</p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted"><i class="fas fa-weight-hanging me-1"></i> Per item</small>
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-outline-primary rounded-pill px-4">Book Now <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-image">
                        <div class="placeholder-image">
                            <i class="fas fa-bed"></i>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">Linen Service</h5>
                            <div class="price-tag">$15.00</div>
                        </div>
                        <p class="text-muted small">Deep cleaning for bedsheets, duvets, curtains, and household linens with steam ironing.</p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted"><i class="fas fa-weight-hanging me-1"></i> Per piece</small>
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-outline-primary rounded-pill px-4">Book Now <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse

        </div>

        <div class="text-center mt-5">
            <a href="{{ route('services') }}" class="btn btn-dark btn-lg rounded-pill px-5">
                View All Services <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

    </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="how-section py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">How It Works</h2>
            <p class="text-muted">Simple 3-step process to fresh, clean clothes</p>
        </div>

        <div class="row g-4">

            @foreach([
                ['icon' => 'calendar-check', 'title' => 'Schedule Pickup', 'desc' => 'Book your pickup online in just a few clicks. Choose a time that works for you.'],
                ['icon' => 'soap', 'title' => 'We Clean', 'desc' => 'Your clothes are professionally cleaned, ironed, and inspected for quality.'],
                ['icon' => 'truck', 'title' => 'Fast Delivery', 'desc' => 'Fresh laundry delivered back to your doorstep, neatly packed and ready to wear.'],
            ] as $step)

            <div class="col-md-4">
                <div class="how-card text-center">
                    <div class="how-icon">
                        <i class="fas fa-{{ $step['icon'] }}"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ $step['title'] }}</h4>
                    <p class="text-muted mb-0">{{ $step['desc'] }}</p>
                </div>
            </div>

            @endforeach

        </div>

    </div>
</section>

<!-- ================= CTA SECTION ================= -->
<section class="cta-section py-5">
    <div class="container text-center text-white">

        <h2 class="display-5 fw-bold mb-4">
            Ready to simplify your laundry?
        </h2>

        <p class="lead mb-4 text-white-50">
            Join hundreds of satisfied customers today and experience premium laundry care.
        </p>

        @guest
            <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 py-3">
                <i class="fas fa-user-plus me-2"></i>Get Started Today
            </a>
        @else
            <a href="{{ route('customer.orders.create') }}" class="btn btn-light btn-lg rounded-pill px-5 py-3">
                <i class="fas fa-calendar-alt me-2"></i>Schedule Pickup
            </a>
        @endguest

    </div>
</section>

@endsection

<!-- ================= STYLES ================= -->
@push('styles')
<style>
/* ================= VARIABLES ================= */
:root {
    --primary-navy: #0F2B3D;
    --primary-deep: #1A3A4F;
    --accent-teal: #2C8C8C;
    --accent-gold: #D4AF37;
    --accent-soft: #F8F5F0;
    --gray-bg: #F4F6F9;
    --text-dark: #1E2A32;
    --text-muted: #6B7280;
    --border-light: #E5E7EB;
}

/* ================= BASE ================= */
body {
    font-family: 'Inter', 'Poppins', sans-serif;
    background: #ffffff;
    color: var(--text-dark);
    line-height: 1.6;
}

/* ================= HERO SECTION ================= */
.hero-section {
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
    min-height: 85vh;
    position: relative;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.1);
}

.min-vh-75 {
    min-height: 75vh;
}

.hero-badge {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
}

.hero-image {
    animation: float 4s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.feature-mini {
    text-align: center;
}

.feature-mini i {
    display: block;
    font-size: 28px;
    margin-bottom: 8px;
    color: var(--accent-gold);
}

.feature-mini small {
    font-size: 0.85rem;
    font-weight: 500;
}

/* ================= BUTTONS ================= */
.btn-primary-custom {
    background: var(--accent-teal);
    border: none;
    padding: 12px 32px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    color: white;
}

.btn-primary-custom:hover {
    background: #206e6e;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(44, 140, 140, 0.3);
}

.btn-outline-light {
    border: 2px solid white;
    background: transparent;
    padding: 12px 32px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    color: white;
}

.btn-outline-light:hover {
    background: white;
    color: var(--primary-navy);
    transform: translateY(-2px);
}

/* ================= STATS SECTION ================= */
.stats-section {
    background: var(--gray-bg);
    margin-top: -40px;
    position: relative;
    z-index: 10;
    padding: 60px 0;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 30px 20px;
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--accent-teal);
    box-shadow: 0 15px 30px rgba(15, 43, 61, 0.1);
}

.stat-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 15px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(44, 140, 140, 0.1), rgba(212, 175, 55, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 32px;
    color: var(--accent-teal);
}

.stat-number {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--primary-navy);
}

/* ================= SECTION TITLES ================= */
.section-title {
    font-size: 2.3rem;
    font-weight: 700;
    color: var(--primary-navy);
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}

.section-title:after {
    content: '';
    position: absolute;
    bottom: -12px;
    left: 50%;
    transform: translateX(-50%);
    width: 70px;
    height: 4px;
    background: linear-gradient(90deg, var(--accent-teal), var(--accent-gold));
    border-radius: 4px;
}

/* ================= SERVICES SECTION ================= */
.services-section {
    background: white;
    padding: 80px 0;
}

.service-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
    height: 100%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
}

.service-card:hover {
    transform: translateY(-8px);
    border-color: var(--accent-gold);
    box-shadow: 0 20px 35px rgba(15, 43, 61, 0.1);
}

.service-image {
    height: 200px;
    overflow: hidden;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.service-card:hover .service-image img {
    transform: scale(1.05);
}

.placeholder-image {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
}

.placeholder-image i {
    font-size: 60px;
    color: white;
}

.service-content {
    padding: 20px;
}

.price-tag {
    background: var(--accent-teal);
    color: white;
    padding: 5px 12px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
}

/* ================= HOW IT WORKS ================= */
.how-section {
    background: var(--gray-bg);
    padding: 80px 0;
}

.how-card {
    background: white;
    border-radius: 20px;
    padding: 40px 25px;
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
    height: 100%;
}

.how-card:hover {
    transform: translateY(-5px);
    border-color: var(--accent-teal);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
}

.how-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(44, 140, 140, 0.1), rgba(212, 175, 55, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
}

.how-icon i {
    font-size: 36px;
    color: var(--accent-teal);
}

/* ================= CTA SECTION ================= */
.cta-section {
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
    padding: 80px 0;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 992px) {
    .hero-section {
        min-height: auto;
        text-align: center;
    }
    
    .min-vh-75 {
        min-height: auto;
        padding: 60px 0;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .hero-features {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .stat-number {
        font-size: 1.6rem;
    }
    
    .stat-card {
        padding: 20px 15px;
    }
    
    .stat-icon {
        width: 55px;
        height: 55px;
    }
    
    .stat-icon i {
        font-size: 24px;
    }
    
    .service-card {
        margin-bottom: 20px;
    }
    
    .how-card {
        padding: 30px 20px;
    }
    
    .display-5 {
        font-size: 1.8rem;
    }
}

@media (max-width: 576px) {
    .hero-badge {
        font-size: 0.8rem;
    }
    
    .btn-primary-custom, .btn-outline-light {
        padding: 10px 24px;
        font-size: 0.9rem;
    }
    
    .feature-mini i {
        font-size: 22px;
    }
    
    .feature-mini small {
        font-size: 0.75rem;
    }
}
</style>
@endpush

<!-- ================= SCRIPTS ================= -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animated counter for statistics
    const counters = document.querySelectorAll('.stat-number');
    let animated = false;
    
    function animateCounters() {
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            let current = 0;
            const increment = Math.ceil(target / 80);
            
            const updateCounter = () => {
                current += increment;
                if (current >= target) {
                    counter.innerText = target.toLocaleString();
                    return;
                }
                counter.innerText = current.toLocaleString();
                setTimeout(updateCounter, 20);
            };
            updateCounter();
        });
    }
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animateCounters();
                animated = true;
                observer.disconnect();
            }
        });
    }, { threshold: 0.3 });
    
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        observer.observe(statsSection);
    }
});
</script>
@endpush