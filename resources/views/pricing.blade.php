@extends('layouts.app')

@section('title', 'Pricing - ' . config('app.name'))

@section('content')

<!-- ================= PAGE HEADER ================= -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">Simple, Transparent Pricing</h1>
                <p class="lead text-muted mb-0">
                    No hidden fees. Pay only for what you need. Quality service at affordable rates.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================= PRICING CARDS ================= -->
<section class="pricing-section py-5">
    <div class="container">
        <div class="row g-4 align-items-center justify-content-center">

            <!-- Basic Plan -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card text-center">
                    <div class="pricing-header">
                        <h3 class="fw-bold mb-2">Wash & Fold</h3>
                        <p class="text-muted">Perfect for daily laundry</p>
                        <div class="pricing-price">
                            <sup>$</sup>6<span class="price-period">/bag</span>
                        </div>
                        <p class="small text-muted">Up to 5 kg per bag</p>
                    </div>
                    <div class="pricing-features">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Free pickup & delivery</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Eco-friendly detergent</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Scent boost option</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Neatly folded</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> 24-hour turnaround</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-4 w-100">
                                Get Started
                            </a>
                        @else
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-outline-primary rounded-pill px-4 w-100">
                                Book Now
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

            <!-- Popular Plan -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card popular text-center">
                    <div class="popular-badge">⭐ Most Popular</div>
                    <div class="pricing-header">
                        <h3 class="fw-bold mb-2">Dry Cleaning</h3>
                        <p class="text-muted">Professional garment care</p>
                        <div class="pricing-price">
                            <sup>$</sup>12<span class="price-period">/item</span>
                        </div>
                        <p class="small text-muted">Suits, dresses, coats & more</p>
                    </div>
                    <div class="pricing-features">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Eco-friendly solvents</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Stain specialist treatment</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Professional press</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Hand finishing</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Delicate fabric care</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 w-100">
                                Get Started
                            </a>
                        @else
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-primary rounded-pill px-4 w-100">
                                Book Now
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

            <!-- Premium Plan -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card text-center">
                    <div class="pricing-header">
                        <h3 class="fw-bold mb-2">Monthly Unlimited</h3>
                        <p class="text-muted">Best value for households</p>
                        <div class="pricing-price">
                            <sup>$</sup>89<span class="price-period">/month</span>
                        </div>
                        <p class="small text-muted">Save up to 25%</p>
                    </div>
                    <div class="pricing-features">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> 2 bags per week</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> 1 free dry clean item</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Priority pickup</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Free express delivery</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Flexible pause/cancel</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-4 w-100">
                                Subscribe Now
                            </a>
                        @else
                            <a href="{{ route('customer.orders.create') }}" class="btn btn-outline-primary rounded-pill px-4 w-100">
                                Subscribe Now
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= PRICING TABLE ================= -->
<section class="pricing-table-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Complete Price List</h2>
            <p class="text-muted">Detailed pricing for all our services</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table table-hover pricing-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Description</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Wash & Fold</strong></td>
                                <td>Professional washing, drying, and folding</td>
                                <td>Per bag (5kg)</td>
                                <td class="text-primary fw-bold">$6.00</td>
                                <td>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @else
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @endguest
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dry Cleaning</strong></td>
                                <td>Eco-friendly dry cleaning with hand finishing</td>
                                <td>Per item</td>
                                <td class="text-primary fw-bold">$12.00</td>
                                <td>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @else
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @endguest
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ironing Only</strong></td>
                                <td>Professional steam pressing and wrinkle removal</td>
                                <td>Per piece</td>
                                <td class="text-primary fw-bold">$3.00</td>
                                <td>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @else
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @endguest
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Linen Service</strong></td>
                                <td>Deep cleaning for bedsheets, duvets, curtains</td>
                                <td>Per piece</td>
                                <td class="text-primary fw-bold">$15.00</td>
                                <td>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @else
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @endguest
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Shoe Cleaning</strong></td>
                                <td>Professional sneaker and leather shoe cleaning</td>
                                <td>Per pair</td>
                                <td class="text-primary fw-bold">$8.00</td>
                                <td>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @else
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @endguest
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Bag Spa</strong></td>
                                <td>Leather conditioning and handbag restoration</td>
                                <td>Per bag</td>
                                <td class="text-primary fw-bold">$25.00</td>
                                <td>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @else
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-outline-primary rounded-pill">Book</a>
                                    @endguest
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= BULK DISCOUNTS ================= -->
<section class="discounts-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title mb-4">Bulk & Corporate Discounts</h2>
                <p class="text-muted mb-4">
                    Special pricing available for hotels, restaurants, gyms, and corporate clients.
                    Contact us for a custom quote tailored to your business needs.
                </p>
                <a href="{{ route('contact') }}" class="btn btn-primary-custom btn-lg rounded-pill px-5">
                    Request Corporate Quote <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================= FAQ SECTION ================= -->
<section class="faq-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="text-muted">Got questions? We've got answers</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="pricingFaq">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Is pickup and delivery really free?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#pricingFaq">
                            <div class="accordion-body">
                                Yes! We offer free pickup and delivery within our service areas. No minimum order required.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do I pay for services?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body">
                                We accept credit/debit cards, mobile money, and cash on delivery. All payments are secure and encrypted.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What if I'm not satisfied with the service?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body">
                                We offer a 100% satisfaction guarantee. If you're not happy with any item, we'll re-clean it for free or issue a full refund.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                How long does service take?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body">
                                Standard turnaround is 24 hours. Express service (12 hours) is available for an additional fee.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Do you offer subscription plans?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                            <div class="accordion-body">
                                Yes! Our Monthly Unlimited plan gives you 2 bags per week plus 1 free dry clean item, priority service, and free express delivery.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= CTA BANNER ================= -->
<section class="cta-banner py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="cta-card text-center">
                    <h3 class="fw-bold mb-3">Ready to experience the best laundry service?</h3>
                    <p class="mb-4 text-white-50">Join thousands of satisfied customers who trust us with their laundry.</p>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5">
                            Sign Up & Get 20% Off First Order
                        </a>
                    @else
                        <a href="{{ route('customer.orders.create') }}" class="btn btn-light btn-lg rounded-pill px-5">
                            Schedule Your First Pickup
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
/* ================= VARIABLES ================= */
:root {
    --primary-navy: #0F2B3D;
    --primary-deep: #1A3A4F;
    --accent-teal: #2C8C8C;
    --accent-gold: #D4AF37;
    --text-dark: #1E2A32;
    --text-muted: #6B7280;
    --border-light: #E5E7EB;
}

/* ================= PAGE HEADER ================= */
.page-header {
    background: linear-gradient(135deg, #F8F9FA 0%, #E9ECEF 100%);
    border-bottom: 1px solid var(--border-light);
}

/* ================= PRICING CARDS ================= */
.pricing-card {
    background: white;
    border-radius: 24px;
    padding: 40px 30px;
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
    position: relative;
    height: 100%;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
}

.pricing-card:hover {
    transform: translateY(-8px);
    border-color: var(--accent-teal);
    box-shadow: 0 20px 40px rgba(15, 43, 61, 0.12);
}

.pricing-card.popular {
    border: 2px solid var(--accent-gold);
    transform: scale(1.02);
}

.pricing-card.popular:hover {
    transform: scale(1.03) translateY(-5px);
}

.popular-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--accent-gold);
    color: var(--primary-navy);
    padding: 5px 20px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    white-space: nowrap;
}

.pricing-price {
    font-size: 3rem;
    font-weight: 800;
    color: var(--primary-navy);
    margin: 20px 0 5px;
}

.pricing-price sup {
    font-size: 1.3rem;
    top: -1rem;
}

.price-period {
    font-size: 0.9rem;
    font-weight: 400;
    color: var(--text-muted);
}

.pricing-features ul li {
    padding: 10px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 0.95rem;
}

.pricing-features ul li:last-child {
    border-bottom: none;
}

.pricing-features i {
    width: 20px;
}

/* ================= PRICING TABLE ================= */
.pricing-table {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.pricing-table thead th {
    background: var(--primary-navy);
    color: white;
    padding: 15px;
    border: none;
    font-weight: 600;
}

.pricing-table tbody tr {
    transition: all 0.2s ease;
}

.pricing-table tbody tr:hover {
    background: rgba(44, 140, 140, 0.05);
}

.pricing-table tbody td {
    padding: 15px;
    vertical-align: middle;
    border-color: var(--border-light);
}

/* ================= DISCOUNTS SECTION ================= */
.discounts-section {
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
    color: white;
}

.discounts-section .section-title {
    color: white;
}

.discounts-section .section-title:after {
    background: linear-gradient(90deg, var(--accent-gold), var(--accent-teal));
}

.discounts-section .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
}

/* ================= FAQ SECTION ================= */
.faq-section {
    background: var(--gray-bg);
}

.accordion-item {
    border: 1px solid var(--border-light);
    margin-bottom: 15px;
    border-radius: 12px !important;
    overflow: hidden;
}

.accordion-button {
    font-weight: 600;
    padding: 18px 25px;
    background: white;
}

.accordion-button:not(.collapsed) {
    background: white;
    color: var(--accent-teal);
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: var(--accent-teal);
}

.accordion-body {
    padding: 20px 25px;
    color: var(--text-muted);
    background: #FAFBFC;
}

/* ================= CTA BANNER ================= */
.cta-banner {
    background: linear-gradient(135deg, #0F2B3D, #1A3A4F);
}

.cta-card {
    background: linear-gradient(135deg, rgba(44, 140, 140, 0.9), rgba(44, 140, 140, 0.95));
    border-radius: 30px;
    padding: 50px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.cta-card h3 {
    font-size: 1.8rem;
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
    color: white;
}

/* ================= SECTION TITLES ================= */
.section-title {
    font-size: 2rem;
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

/* ================= RESPONSIVE ================= */
@media (max-width: 992px) {
    .pricing-card.popular {
        transform: scale(1);
    }
    
    .pricing-card.popular:hover {
        transform: translateY(-8px);
    }
    
    .cta-card {
        padding: 35px 25px;
    }
    
    .cta-card h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .pricing-card {
        padding: 30px 20px;
    }
    
    .pricing-price {
        font-size: 2.5rem;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .pricing-table thead th {
        font-size: 0.85rem;
    }
    
    .pricing-table tbody td {
        font-size: 0.85rem;
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .page-header h1 {
        font-size: 1.8rem;
    }
    
    .pricing-table {
        font-size: 0.8rem;
    }
    
    .pricing-table .btn-sm {
        font-size: 0.75rem;
        padding: 5px 12px;
    }
    
    .cta-card {
        padding: 30px 20px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any pricing page specific JavaScript here
});
</script>
@endpush