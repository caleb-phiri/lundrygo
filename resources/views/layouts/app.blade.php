{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'LaundryGo - Professional Laundry Services at Your Doorstep')">
    <meta name="keywords" content="laundry, dry cleaning, wash and fold, laundry delivery">
    <meta name="author" content="{{ config('app.name', 'LaundryGo') }}">
    
    <title>@yield('title', 'LaundryGo') - {{ config('app.name', 'LaundryGo') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2C8C8C;
            --primary-dark: #1f6e6e;
            --primary-light: #e8f5f5;
            --secondary: #D4AF37;
            --secondary-dark: #b8960f;
            --dark: #1E2A32;
            --dark-light: #2d3a44;
            --light: #F8F9FA;
            --gray: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar Styles */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            padding: 0.75rem 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
        }
        
        .navbar-brand i {
            color: var(--secondary);
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark) !important;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary) !important;
        }
        
        .navbar .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 0.5rem;
        }
        
        .navbar .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .navbar .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        /* Button Styles */
        .btn {
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(44, 140, 140, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: var(--dark);
        }
        
        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            border-color: var(--secondary-dark);
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-footer {
            background: white;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 12px 12px !important;
        }
        
        /* Form Styles */
        .form-control {
            border-radius: 8px;
            border: 1.5px solid #dee2e6;
            padding: 0.625rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(44, 140, 140, 0.15);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        /* Table Styles */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: var(--dark);
            border-top: none;
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.4em 0.8em;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85em;
        }
        
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-processing { background-color: #cce5ff; color: #004085; }
        .badge-completed { background-color: #d4edda; color: #155724; }
        .badge-cancelled { background-color: #f8d7da; color: #721c24; }
        
        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }
        
        /* Footer Styles */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.8);
            padding: 3rem 0 1rem;
            margin-top: auto;
        }
        
        footer h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        footer a:hover {
            color: var(--secondary);
        }
        
        footer .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        footer .social-links a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }
        
        footer .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 2rem;
            padding-top: 1rem;
        }
        
        /* Utility Classes */
        .text-primary { color: var(--primary) !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .text-secondary { color: var(--secondary) !important; }
        .bg-light-primary { background-color: var(--primary-light) !important; }
        
        .section-title {
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        /* Loading Spinner */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .main-content {
                padding: 1rem 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        {{-- Include Navbar --}}
        @include('partials.navbar')
        
        {{-- Main Content --}}
        <main class="main-content">
            <div class="container">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning!</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info!</strong> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                {{-- Page Content --}}
                @yield('content')
            </div>
        </main>
        
        {{-- Include Footer --}}
        @include('partials.footer')
    </div>
    
    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- jQuery (if needed) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Axios for AJAX requests --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    {{-- CSRF Token for AJAX --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').fadeOut('slow');
        }, 5000);
        
        // Enable all tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Enable all popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    </script>
    
    @stack('scripts')
</body>
</html>