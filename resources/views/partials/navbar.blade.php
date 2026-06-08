<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
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
                        <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-small">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            @if(auth()->user()->role === 'customer')
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}">
                                    <i class="fas fa-box me-2 text-primary"></i> My Orders
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                                    <i class="fas fa-user me-2 text-primary"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.addresses') }}">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i> Addresses
                                </a></li>
                            @elseif(auth()->user()->role === 'rider')
                                <li><a class="dropdown-item" href="{{ route('rider.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2 text-primary"></i> Rider Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('rider.orders') }}">
                                    <i class="fas fa-box me-2 text-primary"></i> My Deliveries
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('rider.earnings') }}">
                                    <i class="fas fa-dollar-sign me-2 text-primary"></i> Earnings
                                </a></li>
                            @elseif(auth()->user()->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-crown me-2 text-warning"></i> Admin Panel
                                </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2 rounded-pill px-4">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-user-plus me-1"></i> Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
    /* Navbar Corporate Design */
    .navbar {
        padding: 0.8rem 0;
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .navbar.scrolled {
        padding: 0.5rem 0;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }
    
    .navbar-brand {
        font-size: 1.6rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0F2B3D, #2C8C8C);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        transition: all 0.3s ease;
    }
    
    .navbar-brand i {
        background: none;
        -webkit-background-clip: unset;
        color: #2C8C8C;
    }
    
    .navbar-brand:hover {
        transform: scale(1.02);
    }
    
    /* Nav Links */
    .nav-link {
        font-weight: 600;
        color: #1E2A32 !important;
        margin: 0 4px;
        padding: 8px 16px !important;
        border-radius: 50px;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .nav-link i {
        transition: transform 0.2s ease;
    }
    
    .nav-link:hover i {
        transform: translateY(-2px);
    }
    
    .nav-link:hover {
        color: #2C8C8C !important;
        background: rgba(44, 140, 140, 0.08);
    }
    
    .nav-link.active {
        color: #2C8C8C !important;
        background: rgba(44, 140, 140, 0.12);
        font-weight: 700;
    }
    
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background: linear-gradient(90deg, #2C8C8C, #D4AF37);
        border-radius: 3px;
    }
    
    /* User Avatar */
    .user-avatar-small {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #2C8C8C, #D4AF37);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
    }
    
    /* Dropdown Styling */
    .dropdown-toggle {
        border-radius: 50px !important;
        padding: 6px 16px 6px 8px !important;
        transition: all 0.3s ease;
    }
    
    .dropdown-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 140, 140, 0.2);
    }
    
    .dropdown-menu {
        border-radius: 16px;
        margin-top: 12px;
        padding: 8px 0;
        animation: fadeInDown 0.3s ease;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dropdown-item {
        padding: 10px 24px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .dropdown-item i {
        width: 20px;
        transition: transform 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: rgba(44, 140, 140, 0.08);
        transform: translateX(4px);
    }
    
    .dropdown-item:hover i {
        transform: translateX(2px);
    }
    
    /* Buttons */
    .btn-outline-primary {
        border: 2px solid #2C8C8C;
        color: #2C8C8C;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: #2C8C8C;
        border-color: #2C8C8C;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 140, 140, 0.3);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2C8C8C, #206e6e);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 140, 140, 0.4);
        background: linear-gradient(135deg, #206e6e, #1a5a5a);
    }
    
    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .navbar-nav {
            margin: 20px 0;
        }
        
        .nav-link {
            display: inline-block;
            width: auto;
            margin: 4px 0;
        }
        
        .nav-link.active::after {
            display: none;
        }
        
        .d-flex {
            margin-top: 10px;
            justify-content: center;
        }
        
        .dropdown-toggle {
            width: 100%;
            justify-content: center;
        }
        
        .dropdown-menu {
            width: 100%;
            margin-top: 8px;
        }
    }
    
    /* Scroll effect */
    @media (min-width: 992px) {
        .navbar.scrolled .nav-link {
            padding: 6px 14px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Navbar scroll effect
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar');
        
        function handleScroll() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
        
        window.addEventListener('scroll', handleScroll);
        handleScroll();
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                const menu = dropdown.nextElementSibling;
                if (menu && menu.classList.contains('dropdown-menu')) {
                    if (!dropdown.contains(event.target) && !menu.contains(event.target)) {
                        menu.classList.remove('show');
                        dropdown.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    });
</script>
@endpush