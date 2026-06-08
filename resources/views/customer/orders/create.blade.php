@extends('layouts.app')

@section('title', 'Place New Order')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
<style>
/* ================= CSS VARIABLES ================= */
:root {
    --primary-navy: #0F2B3D;
    --primary-deep: #1A3A4F;
    --accent-teal: #2C8C8C;
    --accent-teal-light: #4FB3B3;
    --accent-gold: #D4AF37;
    --accent-soft-blue: #E8F4F8;
    --accent-soft-green: #E8F5E9;
    --bg-white: #FFFFFF;
    --bg-light: #F8FAFC;
    --text-dark: #1E293B;
    --text-muted: #64748B;
    --border-light: #E2E8F0;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
    --shadow-hover: 0 12px 28px rgba(44,140,140,0.15);
}

* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: linear-gradient(135deg, var(--bg-light) 0%, #F1F5F9 100%);
    min-height: 100vh;
}

/* ================= 3D ANIMATED BASKET ICON STYLES ================= */
.basket-3d {
    width: 80px;
    height: 80px;
    margin: 0 auto 16px;
    position: relative;
    cursor: pointer;
    transform-style: preserve-3d;
    perspective: 500px;
}

.basket-container {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.service-card:hover .basket-container {
    transform: translateY(-6px) rotateX(5deg);
    animation: floatBasket 3s ease-in-out infinite;
}

@keyframes floatBasket {
    0%, 100% { transform: translateY(-6px) rotateX(5deg); }
    50% { transform: translateY(-10px) rotateX(8deg); }
}

.basket-body-3d {
    position: absolute;
    width: 65px;
    height: 50px;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(145deg, #D4A574, #B8874A);
    border-radius: 50% 50% 30% 30% / 60% 60% 40% 40%;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2), inset 0 2px 4px rgba(255,255,255,0.3), inset 0 -2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.basket-body-3d::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(90deg, transparent, transparent 8px, rgba(139, 69, 19, 0.3) 8px, rgba(139, 69, 19, 0.3) 10px),
                repeating-linear-gradient(0deg, transparent, transparent 8px, rgba(139, 69, 19, 0.2) 8px, rgba(139, 69, 19, 0.2) 10px);
    border-radius: inherit;
}

.basket-rim {
    position: absolute;
    width: 72px;
    height: 7px;
    bottom: 48px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(180deg, #C49A6C, #A67B4E);
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.basket-handle {
    position: absolute;
    width: 45px;
    height: 25px;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid #C49A6C;
    border-radius: 30px 30px 20px 20px;
    border-top: none;
    background: transparent;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
}

.clothes-3d {
    position: absolute;
    bottom: 6px;
    left: 50%;
    transform: translateX(-50%);
    width: 85%;
    height: 35px;
    display: flex;
    gap: 3px;
    justify-content: center;
    z-index: 2;
}

.cloth-item {
    position: relative;
    width: 18px;
    height: 24px;
    background: linear-gradient(135deg, #FFFFFF, #F0F0F0);
    border-radius: 4px 4px 6px 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transform-origin: center bottom;
    transition: all 0.3s ease;
}

.service-card:hover .cloth-item {
    animation: foldCloth3d 2s ease infinite;
}

.cloth-item:nth-child(1) { animation-delay: 0s; background: linear-gradient(135deg, #FFFFFF, #F5F5F5); }
.cloth-item:nth-child(2) { animation-delay: 0.2s; background: linear-gradient(135deg, #E3F2FD, #BBDEFB); }
.cloth-item:nth-child(3) { animation-delay: 0.4s; background: linear-gradient(135deg, #E8EAF6, #C5CAE9); }
.cloth-item:nth-child(4) { animation-delay: 0.6s; background: linear-gradient(135deg, #FFF3E0, #FFE0B2); }
.cloth-item:nth-child(5) { animation-delay: 0.8s; background: linear-gradient(135deg, #E0F2F1, #B2DFDB); }

@keyframes foldCloth3d {
    0%, 100% { transform: rotate(0deg) translateY(0px); }
    25% { transform: rotate(-2deg) translateY(-2px); }
    75% { transform: rotate(2deg) translateY(-2px); }
}

.cloth-item::before {
    content: '';
    position: absolute;
    top: 5px;
    left: 2px;
    right: 2px;
    height: 2px;
    background: rgba(0,0,0,0.08);
    border-radius: 1px;
}

.cloth-item::after {
    content: '';
    position: absolute;
    top: 10px;
    left: 2px;
    right: 2px;
    height: 2px;
    background: rgba(0,0,0,0.06);
    border-radius: 1px;
}

.steam-particle-3d {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255,255,255,0.6);
    border-radius: 50%;
    filter: blur(1px);
    opacity: 0;
    pointer-events: none;
}

.service-card:hover .steam-particle-3d {
    animation: steamRise3d 2s ease infinite;
}

.steam-particle-3d:nth-child(1) { top: 15px; left: 30%; animation-delay: 0s; }
.steam-particle-3d:nth-child(2) { top: 10px; left: 50%; animation-delay: 0.4s; width: 5px; height: 5px; }
.steam-particle-3d:nth-child(3) { top: 18px; left: 70%; animation-delay: 0.8s; }
.steam-particle-3d:nth-child(4) { top: 12px; left: 40%; animation-delay: 1.2s; width: 3px; height: 3px; }
.steam-particle-3d:nth-child(5) { top: 20px; left: 60%; animation-delay: 1.6s; }

@keyframes steamRise3d {
    0% { opacity: 0; transform: translateY(0px) scale(1); }
    20% { opacity: 0.6; }
    80% { opacity: 0.3; transform: translateY(-20px) scale(1.5); }
    100% { opacity: 0; transform: translateY(-25px) scale(2); }
}

.sparkle-3d {
    position: absolute;
    width: 5px;
    height: 5px;
    background: radial-gradient(circle, var(--accent-gold), transparent);
    border-radius: 50%;
    opacity: 0;
    pointer-events: none;
}

.service-card:hover .sparkle-3d {
    animation: sparkle3d 1.5s ease infinite;
}

.sparkle-3d:nth-child(6) { top: 8px; right: 25%; animation-delay: 0.2s; }
.sparkle-3d:nth-child(7) { top: 3px; left: 35%; animation-delay: 0.6s; width: 3px; height: 3px; }
.sparkle-3d:nth-child(8) { top: 12px; right: 40%; animation-delay: 1s; }
.sparkle-3d:nth-child(9) { top: 5px; left: 25%; animation-delay: 1.4s; width: 4px; height: 4px; }

@keyframes sparkle3d {
    0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
    50% { opacity: 0.8; transform: scale(1.2) rotate(180deg); }
}

.basket-shadow {
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 65px;
    height: 10px;
    background: radial-gradient(ellipse, rgba(0,0,0,0.15), transparent);
    border-radius: 50%;
    filter: blur(4px);
    transition: all 0.3s ease;
}

.service-card:hover .basket-shadow {
    width: 75px;
    height: 14px;
    bottom: -12px;
    opacity: 0.6;
}

/* ================= SIDEBAR STYLES ================= */
.sidebar-card {
    background: var(--bg-white);
    border-radius: 20px;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

.sidebar-avatar {
    background: linear-gradient(135deg, var(--accent-teal), var(--accent-gold));
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-md);
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    color: var(--text-muted);
    transition: all 0.2s ease;
    text-decoration: none;
    font-weight: 500;
}

.nav-item:hover {
    background: var(--accent-soft-blue);
    color: var(--accent-teal);
    transform: translateX(4px);
}

.nav-item.active {
    background: linear-gradient(135deg, var(--accent-teal), var(--accent-teal-light));
    color: white;
    box-shadow: var(--shadow-sm);
}

/* ================= HERO SECTION ================= */
.hero-section {
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
    border-radius: 24px;
    padding: 28px 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '🧺';
    position: absolute;
    right: -20px;
    bottom: -20px;
    font-size: 120px;
    opacity: 0.08;
    pointer-events: none;
}

.welcome-text {
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin-bottom: 8px;
}

.welcome-subtitle {
    color: rgba(255,255,255,0.8);
    margin-bottom: 20px;
}

.stat-chip {
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 10px 20px;
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

/* ================= SERVICE CONTAINERS ================= */
.service-category {
    background: var(--bg-white);
    border-radius: 24px;
    margin-bottom: 28px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
}

.service-category.collapsed .category-content {
    display: none;
}

.category-header {
    padding: 20px 24px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid var(--border-light);
}

.category-header h4 {
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.category-desc {
    font-size: 14px;
    color: var(--text-muted);
    margin-top: 5px;
}

.toggle-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.service-category.collapsed .toggle-icon {
    transform: rotate(-90deg);
}

.category-content {
    padding: 24px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ================= SERVICE CARDS GRID ================= */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.service-card {
    background: var(--bg-white);
    border: 2px solid var(--border-light);
    border-radius: 24px;
    padding: 24px 20px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    text-align: center;
}

.service-card:hover {
    transform: translateY(-6px);
    border-color: var(--accent-teal);
    box-shadow: var(--shadow-hover);
}

.service-card.selected {
    border-color: var(--accent-teal);
    background: linear-gradient(135deg, rgba(44,140,140,0.03), rgba(212,175,55,0.02));
    box-shadow: 0 0 0 3px rgba(44,140,140,0.1);
}

.service-name {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 8px;
}

.service-description {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 16px;
    line-height: 1.4;
    min-height: 40px;
}

.service-price {
    margin-bottom: 16px;
}

.currency {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-muted);
    vertical-align: top;
}

.amount {
    font-size: 32px;
    font-weight: 800;
    color: var(--accent-teal);
    line-height: 1;
}

.period {
    font-size: 13px;
    color: var(--text-muted);
}

.service-features {
    list-style: none;
    padding: 0;
    margin: 0;
    text-align: left;
}

.service-features li {
    font-size: 11px;
    color: var(--text-muted);
    padding: 4px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.service-features li i {
    color: var(--accent-teal);
    font-size: 10px;
}

/* Badges */
.badge-recommended {
    position: absolute;
    top: -12px;
    left: 20px;
    background: linear-gradient(135deg, var(--accent-gold), #F59E0B);
    color: var(--primary-navy);
    padding: 4px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    z-index: 1;
}

.badge-value {
    position: absolute;
    top: -12px;
    right: 20px;
    background: linear-gradient(135deg, var(--accent-teal), var(--accent-teal-light));
    color: white;
    padding: 4px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    z-index: 1;
}

/* ================= MODAL STYLES ================= */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-container {
    background: var(--bg-white);
    border-radius: 32px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    box-shadow: var(--shadow-lg);
}

.modal-overlay.active .modal-container {
    transform: scale(1);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
    padding: 24px;
    color: white;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modal-body {
    padding: 32px;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid var(--border-light);
    display: flex;
    gap: 12px;
    position: sticky;
    bottom: 0;
    background: var(--bg-white);
}

.modal-btn {
    flex: 1;
    padding: 14px;
    border-radius: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.modal-btn-cancel {
    background: var(--bg-light);
    border: 1px solid var(--border-light);
    color: var(--text-muted);
}

.modal-btn-cancel:hover {
    background: #e2e8f0;
}

.modal-btn-confirm {
    background: linear-gradient(135deg, var(--accent-teal), var(--accent-teal-light));
    border: none;
    color: white;
}

.modal-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44,140,140,0.3);
}

/* Service Options Modal (Small) */
.service-modal {
    max-width: 500px;
}

.service-modal .modal-option {
    background: var(--bg-light);
    border: 2px solid var(--border-light);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.service-modal .modal-option:hover {
    border-color: var(--accent-teal);
    background: var(--accent-soft-blue);
    transform: translateX(5px);
}

.service-modal .modal-option.selected {
    border-color: var(--accent-teal);
    background: linear-gradient(135deg, rgba(44,140,140,0.1), rgba(212,175,55,0.05));
}

.service-modal .modal-option-title {
    font-weight: 700;
    font-size: 18px;
    color: var(--text-dark);
    margin-bottom: 4px;
}

.service-modal .modal-option-price {
    font-size: 24px;
    font-weight: 800;
    color: var(--accent-teal);
}

.service-modal .modal-option-desc {
    font-size: 12px;
    color: var(--text-muted);
}

/* Checkout Card */
.checkout-card {
    position: sticky;
    top: 24px;
    background: var(--bg-white);
    border-radius: 24px;
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.checkout-header {
    background: linear-gradient(135deg, var(--primary-navy), var(--primary-deep));
    padding: 20px 24px;
    color: white;
}

.form-floating-label {
    position: relative;
    margin-bottom: 20px;
}

.form-floating-label input,
.form-floating-label select,
.form-floating-label textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-light);
    border-radius: 14px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: var(--bg-white);
    color: var(--text-dark);
}

.form-floating-label select {
    cursor: pointer;
}

.form-floating-label label {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--bg-white);
    padding: 0 6px;
    color: var(--text-muted);
    font-size: 14px;
    transition: all 0.2s ease;
    pointer-events: none;
}

.form-floating-label textarea ~ label {
    top: 16px;
    transform: none;
}

.form-floating-label input:focus,
.form-floating-label select:focus,
.form-floating-label textarea:focus {
    outline: none;
    border-color: var(--accent-teal);
    box-shadow: 0 0 0 3px rgba(44,140,140,0.1);
}

.form-floating-label input:focus ~ label,
.form-floating-label select:focus ~ label,
.form-floating-label textarea:focus ~ label,
.form-floating-label input:not(:placeholder-shown) ~ label,
.form-floating-label select:has(option:checked:not([value=""])) ~ label {
    top: 0;
    transform: translateY(-50%);
    font-size: 11px;
    color: var(--accent-teal);
}

.price-summary {
    background: var(--bg-light);
    border-radius: 16px;
    padding: 16px;
    margin: 20px 0;
}

.price-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.price-row.total {
    font-size: 20px;
    font-weight: 800;
    border-top: 2px solid var(--border-light);
    margin-top: 8px;
    padding-top: 12px;
    color: var(--accent-teal);
}

.selected-services-list {
    max-height: 300px;
    overflow-y: auto;
}

.selected-service-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid var(--border-light);
    font-size: 13px;
}

.selected-service-item .remove-item {
    color: #dc2626;
    cursor: pointer;
    font-size: 14px;
}

/* Order Summary Styles */
.order-summary-items {
    margin-bottom: 16px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 13px;
}

.summary-total {
    font-size: 18px;
    font-weight: 800;
    color: var(--accent-teal);
    padding-top: 12px;
    margin-top: 8px;
    border-top: 2px solid var(--border-light);
}

/* Payment Modal Buttons */
.payment-options-modal {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.payment-btn-modal {
    flex: 1;
    padding: 14px;
    border: 2px solid var(--border-light);
    border-radius: 14px;
    background: var(--bg-white);
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}

.payment-btn-modal.active {
    background: var(--accent-teal) !important;
    border-color: var(--accent-teal) !important;
    color: white !important;
}

.payment-btn-modal:hover:not(.active) {
    border-color: var(--accent-teal) !important;
    background: var(--accent-soft-blue) !important;
}

/* Confirmation Modal */
.confirmation-details {
    background: var(--bg-light);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
}

.confirmation-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-light);
}

.confirmation-row:last-child {
    border-bottom: none;
}

.confirmation-label {
    font-weight: 600;
    color: var(--text-dark);
}

.confirmation-value {
    color: var(--text-muted);
    text-align: right;
}

/* Responsive */
@media (max-width: 992px) {
    .checkout-card {
        position: relative;
        top: 0;
        margin-top: 24px;
    }
    
    .basket-3d {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 768px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .amount {
        font-size: 28px;
    }
    
    .modal-container {
        width: 95%;
        margin: 16px;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .payment-options-modal {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar Column -->
        <div class="col-lg-3">
            <div class="sidebar-card">
                <div class="text-center p-4">
                    <div class="sidebar-avatar mb-3">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                    <hr class="my-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="border rounded-3 p-2">
                                <small class="text-muted d-block">Active Orders</small>
                                <strong class="fs-4" style="color: var(--accent-teal);">{{ $activeOrders ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-2">
                                <small class="text-muted d-block">Completed</small>
                                <strong class="fs-4" style="color: var(--accent-gold);">{{ $completedOrders ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('customer.dashboard') }}" class="nav-item">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('customer.orders') }}" class="nav-item">
                    <i class="fas fa-box"></i> My Orders
                </a>
                <a href="{{ route('customer.orders.create') }}" class="nav-item active">
                    <i class="fas fa-plus-circle"></i> New Order
                </a>
                <a href="{{ route('customer.addresses') }}" class="nav-item">
                    <i class="fas fa-map-pin"></i> Addresses
                </a>
                <a href="{{ route('customer.profile') }}" class="nav-item">
                    <i class="fas fa-user-circle"></i> Profile
                </a>
            </div>
        </div>
        
        <!-- Main Content Column -->
        <div class="col-lg-6">
            <div class="hero-section">
                <div class="welcome-text">
                    Fresh laundry, <br>delivered 🧺
                </div>
                <div class="welcome-subtitle">
                    Choose your services and we'll handle the rest
                </div>
                <div class="stat-chip">
                    <i class="fas fa-star" style="color: var(--accent-gold);"></i>
                    <span>98% customer satisfaction</span>
                </div>
            </div>
            
            <form method="POST" action="{{ route('customer.orders.store') }}" id="orderForm">
                @csrf
                
                <!-- STANDARD SERVICES -->
                <div class="service-category collapsed" id="category-standard">
                    <div class="category-header" onclick="toggleCategory('standard')">
                        <div>
                            <h4><i class="fas fa-box-open" style="color: var(--accent-teal);"></i> Standard Services</h4>
                            <div class="category-desc">Pay-as-you-go • No commitment • Perfect for occasional laundry</div>
                        </div>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="category-content" id="standard-content">
                        <div class="services-grid">
                            @foreach($standardServices as $service)
                            <div class="service-card" 
                                 data-service-id="{{ $service->id }}" 
                                 data-base-name="{{ $service->name }}" 
                                 data-base-price-fold="{{ $service->price }}" 
                                 data-base-price-iron="{{ $service->price + ($service->ironing_included ? 0 : 5) }}" 
                                 data-unit="{{ $service->unit }}" 
                                 data-basket-size="{{ $service->basket_size }}"
                                 data-iron-included="{{ $service->ironing_included ? 'true' : 'false' }}">
                                @if($service->is_popular)
                                <div class="badge-recommended">⭐ MOST POPULAR</div>
                                @endif
                                <div class="basket-3d">
                                    <div class="basket-container">
                                        <div class="basket-handle"></div>
                                        <div class="basket-rim"></div>
                                        <div class="basket-body-3d"></div>
                                        <div class="clothes-3d">
                                            @for($i = 0; $i < min(5, ($service->basket_size / 3)); $i++)
                                            <div class="cloth-item"></div>
                                            @endfor
                                        </div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="basket-shadow"></div>
                                    </div>
                                </div>
                                <div class="service-name">{{ $service->name }}</div>
                                <div class="service-description">{{ Str::limit($service->description ?? 'Professional laundry service with care and quality.', 50) }}</div>
                                <div class="service-price">
                                    <span class="currency">$</span>
                                    <span class="amount">{{ number_format($service->price, 0) }}</span>
                                    <span class="period">/{{ $service->unit }}</span>
                                </div>
                                <ul class="service-features">
                                    <li><i class="fas fa-check-circle"></i> Up to {{ $service->basket_size }}kg</li>
                                    <li><i class="fas fa-truck"></i> Free pickup & delivery</li>
                                    <li><i class="fas fa-clock"></i> {{ $service->turnaround_hours }}hr turnaround</li>
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- WEEKLY SUBSCRIPTIONS -->
                <div class="service-category collapsed" id="category-weekly">
                    <div class="category-header" onclick="toggleCategory('weekly')">
                        <div>
                            <h4><i class="fas fa-calendar-week" style="color: var(--accent-teal);"></i> Weekly Subscriptions</h4>
                            <div class="category-desc">Save more • Auto-renew weekly • Cancel anytime</div>
                        </div>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="category-content" id="weekly-content">
                        <div class="services-grid">
                            @foreach($weeklySubscriptions as $service)
                            <div class="service-card" 
                                 data-service-id="{{ $service->id }}" 
                                 data-base-name="{{ $service->name }}" 
                                 data-base-price-fold="{{ $service->price }}" 
                                 data-base-price-iron="{{ $service->price + ($service->ironing_included ? 0 : 10) }}" 
                                 data-unit="{{ $service->unit }}" 
                                 data-baskets-per-week="{{ $service->baskets_per_week }}"
                                 data-iron-included="{{ $service->ironing_included ? 'true' : 'false' }}">
                                @if($service->is_popular)
                                <div class="badge-recommended">⭐ MOST POPULAR</div>
                                @endif
                                @if($service->is_best_value)
                                <div class="badge-value">✨ BEST VALUE</div>
                                @endif
                                <div class="basket-3d">
                                    <div class="basket-container">
                                        <div class="basket-handle"></div>
                                        <div class="basket-rim"></div>
                                        <div class="basket-body-3d"></div>
                                        <div class="clothes-3d">
                                            @for($i = 0; $i < min(4, $service->baskets_per_week + 1); $i++)
                                            <div class="cloth-item"></div>
                                            @endfor
                                        </div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="basket-shadow"></div>
                                    </div>
                                </div>
                                <div class="service-name">{{ $service->name }}</div>
                                <div class="service-description">{{ Str::limit($service->description ?? 'Weekly subscription for regular laundry needs.', 50) }}</div>
                                <div class="service-price">
                                    <span class="currency">$</span>
                                    <span class="amount">{{ number_format($service->price, 0) }}</span>
                                    <span class="period">/{{ $service->unit }}</span>
                                </div>
                                <ul class="service-features">
                                    <li><i class="fas fa-check-circle"></i> {{ $service->baskets_per_week }} basket(s) per week</li>
                                    <li><i class="fas fa-truck"></i> Free pickup & delivery</li>
                                    @if($service->ironing_included)
                                    <li><i class="fas fa-iron"></i> Ironing included</li>
                                    @endif
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- MONTHLY SUBSCRIPTIONS -->
                <div class="service-category collapsed" id="category-monthly">
                    <div class="category-header" onclick="toggleCategory('monthly')">
                        <div>
                            <h4><i class="fas fa-calendar-alt" style="color: var(--accent-teal);"></i> Monthly Subscriptions</h4>
                            <div class="category-desc">Best value • Save up to 20% • Perfect for consistent users</div>
                        </div>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="category-content" id="monthly-content">
                        <div class="services-grid">
                            @foreach($monthlySubscriptions as $service)
                            <div class="service-card" 
                                 data-service-id="{{ $service->id }}" 
                                 data-base-name="{{ $service->name }}" 
                                 data-base-price-fold="{{ $service->price }}" 
                                 data-base-price-iron="{{ $service->price + ($service->ironing_included ? 0 : 40) }}" 
                                 data-unit="{{ $service->unit }}" 
                                 data-baskets-per-month="{{ $service->baskets_per_month }}"
                                 data-iron-included="{{ $service->ironing_included ? 'true' : 'false' }}"
                                 data-bedding="{{ $service->bedding_cleaning ? 'true' : 'false' }}"
                                 data-priority="{{ $service->priority_service ? 'true' : 'false' }}">
                                @if($service->is_popular)
                                <div class="badge-recommended">🏆 CORE SELLER</div>
                                @endif
                                @if($service->is_best_value)
                                <div class="badge-value">💎 PREMIUM</div>
                                @endif
                                <div class="basket-3d">
                                    <div class="basket-container">
                                        <div class="basket-handle"></div>
                                        <div class="basket-rim"></div>
                                        <div class="basket-body-3d"></div>
                                        <div class="clothes-3d">
                                            @for($i = 0; $i < min(5, $service->baskets_per_month); $i++)
                                            <div class="cloth-item"></div>
                                            @endfor
                                        </div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="basket-shadow"></div>
                                    </div>
                                </div>
                                <div class="service-name">{{ $service->name }}</div>
                                <div class="service-description">{{ Str::limit($service->description ?? 'Monthly subscription for regular laundry needs.', 50) }}</div>
                                <div class="service-price">
                                    <span class="currency">$</span>
                                    <span class="amount">{{ number_format($service->price, 0) }}</span>
                                    <span class="period">/{{ $service->unit }}</span>
                                </div>
                                <ul class="service-features">
                                    <li><i class="fas fa-check-circle"></i> {{ $service->baskets_per_month }} baskets per month</li>
                                    <li><i class="fas fa-truck"></i> Free pickup & delivery</li>
                                    @if($service->bedding_cleaning)
                                    <li><i class="fas fa-bed"></i> Blanket cleaning included</li>
                                    @endif
                                    @if($service->priority_service)
                                    <li><i class="fas fa-bolt"></i> Priority service</li>
                                    @endif
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- EXPRESS SERVICES -->
                <div class="service-category collapsed" id="category-express">
                    <div class="category-header" onclick="toggleCategory('express')">
                        <div>
                            <h4><i class="fas fa-bolt" style="color: var(--accent-gold);"></i> Express Services</h4>
                            <div class="category-desc">Fast 12-24 hour turnaround • Priority processing</div>
                        </div>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="category-content" id="express-content">
                        <div class="services-grid">
                            @foreach($expressServices as $service)
                            <div class="service-card" 
                                 data-service-id="{{ $service->id }}" 
                                 data-base-name="{{ $service->name }}" 
                                 data-base-price-fold="{{ $service->price }}" 
                                 data-base-price-iron="{{ $service->price + 6 }}" 
                                 data-unit="{{ $service->unit }}" 
                                 data-basket-size="{{ $service->basket_size }}"
                                 data-priority="true">
                                <div class="badge-value">⚡ EXPRESS</div>
                                <div class="basket-3d">
                                    <div class="basket-container">
                                        <div class="basket-handle"></div>
                                        <div class="basket-rim"></div>
                                        <div class="basket-body-3d"></div>
                                        <div class="clothes-3d">
                                            @for($i = 0; $i < min(4, ($service->basket_size / 3)); $i++)
                                            <div class="cloth-item"></div>
                                            @endfor
                                        </div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="steam-particle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="sparkle-3d"></div>
                                        <div class="basket-shadow"></div>
                                    </div>
                                </div>
                                <div class="service-name">{{ $service->name }}</div>
                                <div class="service-description">{{ Str::limit($service->description ?? 'Fast turnaround for urgent laundry needs.', 50) }}</div>
                                <div class="service-price">
                                    <span class="currency">$</span>
                                    <span class="amount">{{ number_format($service->price, 0) }}</span>
                                    <span class="period">/{{ $service->unit }}</span>
                                </div>
                                <ul class="service-features">
                                    <li><i class="fas fa-clock"></i> {{ $service->turnaround_hours }}-24 hour turnaround</li>
                                    <li><i class="fas fa-bolt"></i> Priority processing</li>
                                    <li><i class="fas fa-truck"></i> Express delivery</li>
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Checkout Column -->
        <div class="col-lg-3">
            <div class="checkout-card">
                <div class="checkout-header">
                    <h6 class="mb-0"><i class="fas fa-shopping-bag me-2"></i> Your Order</h6>
                    <small>Review your selection</small>
                </div>
                <div class="p-4">
                    <div class="selected-services-list" id="selectedServicesList">
                        <p class="text-muted text-center">No services selected yet</p>
                    </div>
                    
                    <div class="price-summary">
                        <div class="price-row">
                            <span>Subtotal:</span>
                            <span>$<span id="subtotal">0.00</span></span>
                        </div>
                        <div class="price-row">
                            <span>Delivery Fee:</span>
                            <span>$<span id="delivery_fee">5.00</span></span>
                        </div>
                        <div class="price-row total">
                            <span>Total:</span>
                            <span>$<span id="total">0.00</span></span>
                        </div>
                    </div>
                    
                    <button type="button" class="submit-btn" onclick="openCheckoutModal()">
                        Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step 1: Service Options Modal -->
<div class="modal-overlay" id="serviceModal">
    <div class="modal-container service-modal">
        <div class="modal-header">
            <h3 id="modalTitle">Select Service Option</h3>
            <p style="margin: 8px 0 0; opacity: 0.8;">Choose how you want your laundry processed</p>
        </div>
        <div class="modal-body">
            <div class="modal-option" id="optionWashFold" onclick="selectModalOption('wash_fold')">
                <div>
                    <div class="modal-option-title">🧼 Wash & Fold</div>
                    <div class="modal-option-desc">Professional washing, careful folding, fresh scent</div>
                </div>
                <div class="modal-option-price" id="priceFold">$0.00</div>
            </div>
            <div class="modal-option" id="optionWashIron" onclick="selectModalOption('wash_iron')">
                <div>
                    <div class="modal-option-title">👔 Wash + Iron</div>
                    <div class="modal-option-desc">Complete care with professional ironing & pressing</div>
                </div>
                <div class="modal-option-price" id="priceIron">$0.00</div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="modal-btn modal-btn-cancel" onclick="closeServiceModal()">Cancel</div>
            <div class="modal-btn modal-btn-confirm" onclick="confirmAddToCart()">Add to Cart</div>
        </div>
    </div>
</div>

<!-- Step 2: Pickup & Delivery + Payment Modal -->
<div class="modal-overlay" id="checkoutModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-truck me-2"></i> Pickup & Delivery Details</h3>
            <p style="margin: 8px 0 0; opacity: 0.8;">Step 2 of 3: Tell us when and where to pick up your laundry</p>
        </div>
        <div class="modal-body">
            <!-- Pickup & Delivery Section -->
            <div class="service-category" style="margin-bottom: 24px; box-shadow: none; border: 1px solid var(--border-light);">
                <div class="category-header" style="background: var(--accent-soft-blue); border-radius: 20px 20px 0 0;">
                    <div>
                        <h4><i class="fas fa-truck" style="color: var(--accent-teal);"></i> Pickup & Delivery</h4>
                        <div class="category-desc">Schedule your pickup and delivery times</div>
                    </div>
                </div>
                <div class="category-content" style="padding: 20px;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating-label">
                                <select class="form-control" required id="modalPickupAddress">
                                    <option value="">Select Pickup Address</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                            {{ $address->label }} - {{ $address->full_address }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>Pickup Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-label">
                                <select class="form-control" required id="modalDeliveryAddress">
                                    <option value="">Select Delivery Address</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->label }} - {{ $address->full_address }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>Delivery Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-label">
                                <input type="datetime-local" class="form-control" required id="modalPickupDate" placeholder=" ">
                                <label>Pickup Date & Time</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating-label">
                                <input type="datetime-local" class="form-control" required id="modalDeliveryDate" placeholder=" ">
                                <label>Delivery Date & Time</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Method Section -->
            <div class="service-category" style="margin-bottom: 24px; box-shadow: none; border: 1px solid var(--border-light);">
                <div class="category-header" style="background: var(--accent-soft-blue); border-radius: 20px 20px 0 0;">
                    <div>
                        <h4><i class="fas fa-credit-card" style="color: var(--accent-teal);"></i> Payment Method</h4>
                        <div class="category-desc">Select how you want to pay</div>
                    </div>
                </div>
                <div class="category-content" style="padding: 20px;">
                    <div class="payment-options-modal">
                        <button type="button" class="payment-btn-modal active" data-payment="card" onclick="selectPaymentMethodModal('card', this)">
                            💳 Credit/Debit Card
                        </button>
                        <button type="button" class="payment-btn-modal" data-payment="cash" onclick="selectPaymentMethodModal('cash', this)">
                            💵 Cash on Delivery
                        </button>
                        <button type="button" class="payment-btn-modal" data-payment="wallet" onclick="selectPaymentMethodModal('wallet', this)">
                            👛 Wallet Balance
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Special Instructions Section -->
            <div class="service-category" style="margin-bottom: 0; box-shadow: none; border: 1px solid var(--border-light);">
                <div class="category-header" style="background: var(--accent-soft-blue); border-radius: 20px 20px 0 0;">
                    <div>
                        <h4><i class="fas fa-clipboard-list" style="color: var(--accent-teal);"></i> Special Instructions</h4>
                        <div class="category-desc">Let us know any special requirements (optional)</div>
                    </div>
                </div>
                <div class="category-content" style="padding: 20px;">
                    <div class="form-floating-label">
                        <textarea class="form-control" rows="3" placeholder=" " id="modalSpecialInstructions"></textarea>
                        <label>Special Instructions (optional)</label>
                    </div>
                    <div class="form-floating-label mt-3">
                        <input type="text" class="form-control" placeholder=" " id="modalPromotionCode">
                        <label>Promotion Code (optional)</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="modal-btn modal-btn-cancel" onclick="closeCheckoutModal()">Back to Cart</div>
            <div class="modal-btn modal-btn-confirm" onclick="showOrderSummary()">Review Order →</div>
        </div>
    </div>
</div>

<!-- Step 3: Order Summary & Confirmation Modal -->
<div class="modal-overlay" id="summaryModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-clipboard-list me-2"></i> Order Summary</h3>
            <p style="margin: 8px 0 0; opacity: 0.8;">Step 3 of 3: Please review your order before placing</p>
        </div>
        <div class="modal-body">
            <div class="confirmation-details">
                <h5 style="margin-bottom: 16px; color: var(--accent-teal);">📋 Selected Services</h5>
                <div id="summaryServicesList"></div>
                
                <h5 style="margin: 20px 0 16px; color: var(--accent-teal);">📍 Pickup & Delivery</h5>
                <div class="confirmation-row">
                    <span class="confirmation-label">Pickup Address:</span>
                    <span class="confirmation-value" id="summaryPickupAddress"></span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Delivery Address:</span>
                    <span class="confirmation-value" id="summaryDeliveryAddress"></span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Pickup Date:</span>
                    <span class="confirmation-value" id="summaryPickupDate"></span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Delivery Date:</span>
                    <span class="confirmation-value" id="summaryDeliveryDate"></span>
                </div>
                
                <h5 style="margin: 20px 0 16px; color: var(--accent-teal);">💳 Payment Method</h5>
                <div class="confirmation-row">
                    <span class="confirmation-label">Payment Method:</span>
                    <span class="confirmation-value" id="summaryPaymentMethod"></span>
                </div>
                
                <h5 style="margin: 20px 0 16px; color: var(--accent-teal);">💰 Price Breakdown</h5>
                <div id="summaryPriceBreakdown"></div>
                
                <div class="summary-total" id="summaryTotal">
                    Total: $0.00
                </div>
                
                <div class="form-floating-label mt-3">
                    <textarea class="form-control" rows="2" placeholder=" " id="modalSpecialInstructions"></textarea>
                    <label>Special Instructions (optional)</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="modal-btn modal-btn-cancel" onclick="closeSummaryModal()">Back to Edit</div>
            <div class="modal-btn modal-btn-confirm" onclick="submitOrder()">Confirm & Place Order ✓</div>
        </div>
    </div>
</div>

<script>
// Selected services storage
let selectedServices = [];
let pendingService = null;
let selectedOption = null;
let selectedPaymentMethod = 'card';

// Store pickup/delivery data for summary
let pickupAddressText = '';
let deliveryAddressText = '';
let pickupDateValue = '';
let deliveryDateValue = '';

// Toggle categories
function toggleCategory(category) {
    const content = document.getElementById(`${category}-content`);
    const container = content.closest('.service-category');
    container.classList.toggle('collapsed');
}

// Open service modal
function openServiceModal(serviceCard) {
    const serviceId = serviceCard.dataset.serviceId;
    const baseName = serviceCard.dataset.baseName;
    const priceFold = parseFloat(serviceCard.dataset.basePriceFold);
    const priceIron = parseFloat(serviceCard.dataset.basePriceIron);
    const unit = serviceCard.dataset.unit;
    const ironIncluded = serviceCard.dataset.ironIncluded === 'true';
    
    pendingService = {
        id: serviceId,
        name: baseName,
        priceFold: priceFold,
        priceIron: priceIron,
        unit: unit,
        ironIncluded: ironIncluded,
        card: serviceCard,
        basketsPerWeek: serviceCard.dataset.basketsPerWeek,
        basketsPerMonth: serviceCard.dataset.basketsPerMonth,
        freeIroning: serviceCard.dataset.freeIroning === 'true',
        bedding: serviceCard.dataset.bedding === 'true',
        priority: serviceCard.dataset.priority === 'true'
    };
    
    document.getElementById('modalTitle').innerText = baseName;
    document.getElementById('priceFold').innerText = `$${priceFold.toFixed(2)}/${unit}`;
    document.getElementById('priceIron').innerText = `$${priceIron.toFixed(2)}/${unit}`;
    
    selectedOption = null;
    document.getElementById('optionWashFold').classList.remove('selected');
    document.getElementById('optionWashIron').classList.remove('selected');
    
    if (ironIncluded) {
        document.getElementById('optionWashFold').style.display = 'none';
        document.getElementById('optionWashIron').style.display = 'flex';
        selectedOption = 'wash_iron';
        document.getElementById('optionWashIron').classList.add('selected');
    } else {
        document.getElementById('optionWashFold').style.display = 'flex';
        document.getElementById('optionWashIron').style.display = 'flex';
    }
    
    document.getElementById('serviceModal').classList.add('active');
}

function selectModalOption(option) {
    selectedOption = option;
    document.getElementById('optionWashFold').classList.remove('selected');
    document.getElementById('optionWashIron').classList.remove('selected');
    document.getElementById(`option${option === 'wash_fold' ? 'WashFold' : 'WashIron'}`).classList.add('selected');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
    pendingService = null;
    selectedOption = null;
}

function confirmAddToCart() {
    if (!selectedOption && !pendingService?.ironIncluded) {
        alert('Please select an option');
        return;
    }
    
    if (!pendingService) return;
    
    const serviceId = pendingService.id;
    const existingIndex = selectedServices.findIndex(s => s.id === serviceId);
    
    const isIron = pendingService.ironIncluded ? true : (selectedOption === 'wash_iron');
    const price = isIron ? pendingService.priceIron : pendingService.priceFold;
    const serviceType = isIron ? 'Wash + Iron' : 'Wash & Fold';
    const fullName = `${pendingService.name} - ${serviceType}`;
    const quantity = 1;
    
    if (existingIndex !== -1) {
        selectedServices[existingIndex] = {
            id: serviceId,
            name: fullName,
            price: price,
            unit: pendingService.unit,
            quantity: quantity,
            serviceType: serviceType,
            basketsPerWeek: pendingService.basketsPerWeek,
            basketsPerMonth: pendingService.basketsPerMonth,
            freeIroning: pendingService.freeIroning,
            bedding: pendingService.bedding,
            priority: pendingService.priority
        };
    } else {
        selectedServices.push({
            id: serviceId,
            name: fullName,
            price: price,
            unit: pendingService.unit,
            quantity: quantity,
            serviceType: serviceType,
            basketsPerWeek: pendingService.basketsPerWeek,
            basketsPerMonth: pendingService.basketsPerMonth,
            freeIroning: pendingService.freeIroning,
            bedding: pendingService.bedding,
            priority: pendingService.priority
        });
    }
    
    document.querySelectorAll('.service-card').forEach(card => {
        if (card.dataset.serviceId == serviceId) {
            card.classList.add('selected');
        }
    });
    
    updateSelectedServicesList();
    calculateTotal();
    closeServiceModal();
}

function updateSelectedServicesList() {
    const container = document.getElementById('selectedServicesList');
    
    if (selectedServices.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No services selected yet</p>';
        return;
    }
    
    let html = '';
    selectedServices.forEach(service => {
        const total = service.price * service.quantity;
        html += `
            <div class="selected-service-item">
                <div>
                    <strong>${service.name}</strong>
                    <small class="text-muted d-block">Qty: ${service.quantity} × $${service.price.toFixed(2)}</small>
                    ${service.basketsPerWeek ? `<small class="text-muted d-block">📅 ${service.basketsPerWeek} basket(s) per week</small>` : ''}
                    ${service.basketsPerMonth ? `<small class="text-muted d-block">📆 ${service.basketsPerMonth} baskets per month</small>` : ''}
                    ${service.bedding ? `<small class="text-muted d-block">🛏️ Blanket cleaning included</small>` : ''}
                    ${service.priority ? `<small class="text-muted d-block">⚡ Priority service</small>` : ''}
                </div>
                <div>
                    <span class="fw-bold">$${total.toFixed(2)}</span>
                    <i class="fas fa-trash-alt remove-item ms-2" onclick="removeService('${service.id}')"></i>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function removeService(serviceId) {
    const index = selectedServices.findIndex(s => s.id == serviceId);
    if (index !== -1) {
        selectedServices.splice(index, 1);
        
        document.querySelectorAll('.service-card').forEach(card => {
            if (card.dataset.serviceId == serviceId) {
                card.classList.remove('selected');
            }
        });
        
        updateSelectedServicesList();
        calculateTotal();
    }
}

function calculateTotal() {
    let subtotal = 0;
    selectedServices.forEach(service => {
        subtotal += service.price * service.quantity;
    });
    
    const deliveryFee = 5.00;
    const total = subtotal + deliveryFee;
    
    document.getElementById('subtotal').innerText = subtotal.toFixed(2);
    document.getElementById('total').innerText = total.toFixed(2);
}

function selectPaymentMethodModal(method, element) {
    selectedPaymentMethod = method;
    document.querySelectorAll('.payment-btn-modal').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
}

function openCheckoutModal() {
    if (selectedServices.length === 0) {
        alert('Please select at least one service first');
        return;
    }
    
    // Set default dates
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const modalPickupDate = document.getElementById('modalPickupDate');
    if(modalPickupDate) {
        modalPickupDate.min = now.toISOString().slice(0, 16);
        modalPickupDate.value = now.toISOString().slice(0, 16);
    }
    
    const deliveryMin = new Date();
    deliveryMin.setDate(deliveryMin.getDate() + 1);
    deliveryMin.setMinutes(deliveryMin.getMinutes() - deliveryMin.getTimezoneOffset());
    const modalDeliveryDate = document.getElementById('modalDeliveryDate');
    if(modalDeliveryDate) {
        modalDeliveryDate.min = deliveryMin.toISOString().slice(0, 16);
        modalDeliveryDate.value = deliveryMin.toISOString().slice(0, 16);
    }
    
    document.getElementById('checkoutModal').classList.add('active');
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.remove('active');
}

function showOrderSummary() {
    // Validate form
    if (!document.getElementById('modalPickupAddress').value) {
        alert('Please select a pickup address');
        return;
    }
    
    if (!document.getElementById('modalDeliveryAddress').value) {
        alert('Please select a delivery address');
        return;
    }
    
    if (!document.getElementById('modalPickupDate').value) {
        alert('Please select a pickup date');
        return;
    }
    
    if (!document.getElementById('modalDeliveryDate').value) {
        alert('Please select a delivery date');
        return;
    }
    
    // Store values for summary
    const pickupSelect = document.getElementById('modalPickupAddress');
    const deliverySelect = document.getElementById('modalDeliveryAddress');
    
    pickupAddressText = pickupSelect.options[pickupSelect.selectedIndex]?.text || '';
    deliveryAddressText = deliverySelect.options[deliverySelect.selectedIndex]?.text || '';
    pickupDateValue = document.getElementById('modalPickupDate').value;
    deliveryDateValue = document.getElementById('modalDeliveryDate').value;
    
    // Build services summary
    let servicesHtml = '';
    let subtotal = 0;
    selectedServices.forEach(service => {
        const total = service.price * service.quantity;
        subtotal += total;
        servicesHtml += `
            <div class="confirmation-row">
                <span class="confirmation-label">${service.name} × ${service.quantity}</span>
                <span class="confirmation-value">$${total.toFixed(2)}</span>
            </div>
        `;
    });
    
    document.getElementById('summaryServicesList').innerHTML = servicesHtml;
    document.getElementById('summaryPickupAddress').innerText = pickupAddressText;
    document.getElementById('summaryDeliveryAddress').innerText = deliveryAddressText;
    document.getElementById('summaryPickupDate').innerText = new Date(pickupDateValue).toLocaleString();
    document.getElementById('summaryDeliveryDate').innerText = new Date(deliveryDateValue).toLocaleString();
    
    const paymentMethodText = {
        'card': '💳 Credit/Debit Card',
        'cash': '💵 Cash on Delivery',
        'wallet': '👛 Wallet Balance'
    };
    document.getElementById('summaryPaymentMethod').innerText = paymentMethodText[selectedPaymentMethod] || 'Credit/Debit Card';
    
    // Build price breakdown
    const deliveryFee = 5.00;
    const total = subtotal + deliveryFee;
    
    document.getElementById('summaryPriceBreakdown').innerHTML = `
        <div class="confirmation-row">
            <span class="confirmation-label">Subtotal:</span>
            <span class="confirmation-value">$${subtotal.toFixed(2)}</span>
        </div>
        <div class="confirmation-row">
            <span class="confirmation-label">Delivery Fee:</span>
            <span class="confirmation-value">$${deliveryFee.toFixed(2)}</span>
        </div>
    `;
    document.getElementById('summaryTotal').innerHTML = `Total: $${total.toFixed(2)}`;
    
    // Close checkout modal and open summary modal
    closeCheckoutModal();
    document.getElementById('summaryModal').classList.add('active');
}

function closeSummaryModal() {
    document.getElementById('summaryModal').classList.remove('active');
}

function submitOrder() {
    // Create form data
    const form = document.getElementById('orderForm');
    
    // Clear existing hidden inputs (keep CSRF)
    const existingHidden = form.querySelectorAll('input[type="hidden"]:not([name="_token"])');
    existingHidden.forEach(input => input.remove());
    
    // Add all required fields
    const fields = {
        'pickup_address_id': document.getElementById('modalPickupAddress').value,
        'delivery_address_id': document.getElementById('modalDeliveryAddress').value,
        'pickup_date': pickupDateValue,
        'delivery_date': deliveryDateValue,
        'special_instructions': document.getElementById('modalSpecialInstructions').value,
        'promotion_code': document.getElementById('modalPromotionCode').value,
        'payment_method': selectedPaymentMethod,
        'selected_services': JSON.stringify(selectedServices)
    };
    
    for (const [name, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }
    
    // Close summary modal and submit
    closeSummaryModal();
    form.submit();
}

// Add click handlers to service cards
document.querySelectorAll('.service-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON') return;
        openServiceModal(this);
    });
});

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeServiceModal();
        closeCheckoutModal();
        closeSummaryModal();
    }
});

// Close modals when clicking outside
document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServiceModal();
    }
});

document.getElementById('checkoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCheckoutModal();
    }
});

document.getElementById('summaryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSummaryModal();
    }
});
</script>
@endsection