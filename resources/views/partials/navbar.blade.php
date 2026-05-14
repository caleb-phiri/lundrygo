<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('home') }}">
            <i class="fas fa-tshirt me-2"></i>{{ config('app.name', 'LaundryGo') }}
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">
                        <i class="fas fa-washing-machine me-1"></i> Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}">
                        <i class="fas fa-tag me-1"></i> Pricing
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('how-it-works') ? 'active' : '' }}" href="{{ route('how-it-works') }}">
                        <i class="fas fa-question-circle me-1"></i> How It Works
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        <i class="fas fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>
            
            <div class="d-flex">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->role === 'customer')
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}">
                                    <i class="fas fa-box me-2"></i> My Orders
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.addresses') }}">
                                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                                </a></li>
                            @elseif(auth()->user()->role === 'rider')
                                <li><a class="dropdown-item" href="{{ route('rider.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Rider Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('rider.orders') }}">
                                    <i class="fas fa-box me-2"></i> My Deliveries
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('rider.earnings') }}">
                                    <i class="fas fa-dollar-sign me-2"></i> Earnings
                                </a></li>
                            @elseif(auth()->user()->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-crown me-2"></i> Admin Panel
                                </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
    .navbar-brand {
        font-size: 1.5rem;
    }
    .nav-link.active {
        color: #4e73df !important;
        font-weight: 600;
    }
    .dropdown-item:active {
        background-color: #4e73df;
    }
</style>
@endpush