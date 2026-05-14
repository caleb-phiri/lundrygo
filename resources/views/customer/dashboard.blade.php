<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LaundryPro | Customer Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root{
            --primary:#0ea5e9;
            --primary-dark:#0284c7;
            --secondary:#0f172a;
            --muted:#64748b;
            --border:#e2e8f0;
            --bg:#f8fafc;
            --white:#ffffff;
            --success:#10b981;
            --warning:#f59e0b;
            --danger:#ef4444;
            --shadow:0 10px 30px rgba(15,23,42,0.06);
            --radius:20px;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Inter',sans-serif;
            background:var(--bg);
            color:var(--secondary);
        }

        a{
            text-decoration:none;
        }

        /* =========================
           HEADER
        ==========================*/

        .topbar{
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(12px);
            border-bottom:1px solid var(--border);
            padding:1rem 0;
            position:sticky;
            top:0;
            z-index:1000;
        }

        .brand{
            display:flex;
            align-items:center;
            gap:.8rem;
        }

        .brand-icon{
            width:48px;
            height:48px;
            border-radius:16px;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-size:1.2rem;
            box-shadow:0 10px 20px rgba(14,165,233,.25);
        }

        .brand h4{
            margin:0;
            font-weight:800;
            font-size:1.2rem;
        }

        .brand small{
            color:var(--muted);
            font-size:.75rem;
        }

        .header-right{
            display:flex;
            align-items:center;
            gap:1rem;
        }

        .notification-btn{
            width:42px;
            height:42px;
            border-radius:14px;
            border:1px solid var(--border);
            display:flex;
            align-items:center;
            justify-content:center;
            background:white;
            color:var(--secondary);
            transition:.3s;
            position:relative;
        }

        .notification-btn:hover{
            background:var(--primary);
            color:white;
            border-color:var(--primary);
        }

        .notification-badge{
            position:absolute;
            top:-5px;
            right:-5px;
            background:var(--danger);
            color:white;
            font-size:0.65rem;
            padding:0.2rem 0.45rem;
            border-radius:50%;
            font-weight:700;
        }

        .profile-box{
            display:flex;
            align-items:center;
            gap:.8rem;
            padding:.45rem .7rem;
            border:1px solid var(--border);
            border-radius:16px;
            background:white;
            cursor:pointer;
            transition:.3s;
        }

        .profile-box:hover{
            border-color:var(--primary);
            box-shadow:var(--shadow);
        }

        .profile-avatar{
            width:42px;
            height:42px;
            border-radius:14px;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:white;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
        }

        .profile-box h6{
            margin:0;
            font-size:.9rem;
            font-weight:700;
        }

        .profile-box small{
            color:var(--muted);
        }

        /* =========================
           HERO
        ==========================*/

        .hero{
            background:linear-gradient(135deg,#0ea5e9,#0284c7);
            border-radius:32px;
            padding:2rem;
            color:white;
            position:relative;
            overflow:hidden;
            margin-top:2rem;
            box-shadow:0 20px 40px rgba(14,165,233,.18);
        }

        .hero::before{
            content:'';
            position:absolute;
            width:250px;
            height:250px;
            background:rgba(255,255,255,.08);
            border-radius:50%;
            right:-80px;
            top:-80px;
        }

        .hero::after{
            content:'';
            position:absolute;
            width:180px;
            height:180px;
            background:rgba(255,255,255,.05);
            border-radius:50%;
            bottom:-60px;
            right:120px;
        }

        .hero h2{
            font-weight:800;
            margin-bottom:.5rem;
        }

        .hero p{
            opacity:.9;
            max-width:550px;
        }

        .hero-stats{
            display:flex;
            gap:2rem;
            margin-top:1.5rem;
            flex-wrap:wrap;
        }

        .hero-stat{
            background:rgba(255,255,255,0.15);
            backdrop-filter:blur(8px);
            border-radius:20px;
            padding:0.6rem 1.2rem;
            text-align:center;
            min-width:100px;
        }

        .hero-stat .number{
            font-size:1.4rem;
            font-weight:800;
        }

        .hero-stat .label{
            font-size:0.7rem;
            opacity:0.85;
        }

        .hero-actions{
            display:flex;
            gap:1rem;
            flex-wrap:wrap;
            margin-top:1.5rem;
        }

        .btn-modern{
            padding:.85rem 1.4rem;
            border-radius:16px;
            font-weight:600;
            transition:.3s;
            display:inline-flex;
            align-items:center;
            gap:.6rem;
        }

        .btn-light-modern{
            background:white;
            color:var(--secondary);
        }

        .btn-light-modern:hover{
            transform:translateY(-2px);
        }

        .btn-outline-modern{
            border:1px solid rgba(255,255,255,.3);
            color:white;
        }

        .btn-outline-modern:hover{
            background:white;
            color:var(--secondary);
        }

        /* =========================
           STATS
        ==========================*/

        .stats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
            gap:1.2rem;
            margin-top:2rem;
        }

        .stat-card{
            background:white;
            border-radius:24px;
            padding:1.5rem;
            border:1px solid var(--border);
            box-shadow:var(--shadow);
            transition:.3s;
        }

        .stat-card:hover{
            transform:translateY(-5px);
        }

        .stat-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:1rem;
        }

        .stat-icon{
            width:52px;
            height:52px;
            border-radius:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.2rem;
        }

        .icon-blue{
            background:#e0f2fe;
            color:var(--primary);
        }

        .icon-green{
            background:#dcfce7;
            color:var(--success);
        }

        .icon-yellow{
            background:#fef3c7;
            color:var(--warning);
        }

        .icon-red{
            background:#fee2e2;
            color:var(--danger);
        }

        .stat-card h3{
            font-size:2rem;
            font-weight:800;
            margin:0;
        }

        .stat-card p{
            margin:0;
            color:var(--muted);
            font-size:.85rem;
        }

        .stat-trend{
            font-size:0.7rem;
            margin-top:0.5rem;
            display:flex;
            align-items:center;
            gap:0.3rem;
        }

        .trend-up{
            color:var(--success);
        }

        .trend-down{
            color:var(--danger);
        }

        /* =========================
           QUICK ACTIONS
        ==========================*/

        .section-title{
            font-weight:800;
            margin-bottom:1.2rem;
        }

        .actions-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:1rem;
        }

        .action-card{
            background:white;
            border-radius:24px;
            padding:1.5rem;
            border:1px solid var(--border);
            transition:.3s;
            box-shadow:var(--shadow);
            text-align:center;
            text-decoration:none;
            display:block;
        }

        .action-card:hover{
            transform:translateY(-5px);
            border-color:var(--primary);
        }

        .action-icon{
            width:60px;
            height:60px;
            border-radius:20px;
            margin:0 auto 1rem;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:white;
            font-size:1.3rem;
        }

        .action-card h6{
            font-weight:700;
            margin-bottom:.3rem;
            color:var(--secondary);
        }

        .action-card p{
            color:var(--muted);
            font-size:.8rem;
            margin:0;
        }

        /* =========================
           CHART
        ==========================*/

        .chart-container{
            background:white;
            border-radius:28px;
            border:1px solid var(--border);
            padding:1rem;
            margin-top:1.5rem;
        }

        /* =========================
           TABLE
        ==========================*/

        .card-modern{
            background:white;
            border-radius:28px;
            border:1px solid var(--border);
            box-shadow:var(--shadow);
            overflow:hidden;
            margin-top:1.5rem;
        }

        .card-header-modern{
            padding:1.4rem 1.6rem;
            border-bottom:1px solid var(--border);
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:1rem;
        }

        .card-header-modern h5{
            margin:0;
            font-weight:800;
        }

        .table-modern{
            margin:0;
        }

        .table-modern thead{
            background:#f8fafc;
        }

        .table-modern th{
            padding:1rem 1.5rem;
            color:var(--muted);
            font-size:.75rem;
            text-transform:uppercase;
            letter-spacing:.5px;
            border:none;
        }

        .table-modern td{
            padding:1rem 1.5rem;
            vertical-align:middle;
            border-color:#f1f5f9;
        }

        .status-badge{
            padding:.5rem .9rem;
            border-radius:50px;
            font-size:.75rem;
            font-weight:700;
            display:inline-flex;
            align-items:center;
            gap:0.3rem;
        }

        .status-pending{
            background:#fef3c7;
            color:#92400e;
        }

        .status-processing{
            background:#dbeafe;
            color:#1d4ed8;
        }

        .status-delivered{
            background:#dcfce7;
            color:#166534;
        }

        .status-cancelled{
            background:#fee2e2;
            color:#b91c1c;
        }

        .btn-view{
            padding:.55rem 1rem;
            border-radius:12px;
            border:1px solid var(--border);
            color:var(--secondary);
            font-size:.8rem;
            font-weight:600;
            transition:.3s;
            display:inline-flex;
            align-items:center;
            gap:0.3rem;
        }

        .btn-view:hover{
            background:var(--primary);
            border-color:var(--primary);
            color:white;
        }

        .empty-state{
            text-align:center;
            padding:3rem;
        }

        .empty-state i{
            font-size:3rem;
            color:#cbd5e1;
            margin-bottom:1rem;
        }

        /* =========================
           RESPONSIVE
        ==========================*/

        @media(max-width:768px){
            .hero{
                padding:1.5rem;
            }
            .hero h2{
                font-size:1.5rem;
            }
            .hero-stats{
                gap:0.8rem;
            }
            .hero-stat{
                padding:0.4rem 0.8rem;
            }
            .hero-stat .number{
                font-size:1rem;
            }
            .profile-info{
                display:none;
            }
            .card-header-modern{
                flex-direction:column;
                align-items:flex-start;
            }
            .table-modern th,
            .table-modern td{
                padding:0.8rem;
            }
        }

        @media(max-width:576px){
            .hero-actions{
                flex-direction:column;
            }
            .btn-modern{
                width:100%;
                justify-content:center;
            }
            .stats-grid{
                grid-template-columns:repeat(2,1fr);
            }
            .actions-grid{
                grid-template-columns:repeat(2,1fr);
            }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="topbar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fas fa-soap"></i>
                </div>
                <div>
                    <h4>LaundryPro</h4>
                    <small>Customer Dashboard</small>
                </div>
            </div>

            <div class="header-right">
                <a href="#" class="notification-btn" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationCount">{{ $pendingOrders ?? 0 }}</span>
                </a>

                <div class="profile-box" id="profileBox">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                    </div>
                    <div class="profile-info">
                        <h6>{{ auth()->user()->name ?? 'Customer' }}</h6>
                        <small>⭐ Premium Customer</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<div class="container pb-5">

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="position-relative" style="z-index:2;">
            <h2>Welcome back, {{ auth()->user()->name ?? 'Customer' }}! 👋</h2>
            <p>Manage your laundry orders, track deliveries, update your profile, and enjoy a seamless modern laundry experience.</p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="number" id="lifetimeOrders">{{ $totalOrders ?? 0 }}</div>
                    <div class="label">Lifetime Orders</div>
                </div>
                <div class="hero-stat">
                    <div class="number" id="lifetimeSpent">${{ number_format($totalSpent ?? 0, 2) }}</div>
                    <div class="label">Lifetime Spent</div>
                </div>
                <div class="hero-stat">
                    <div class="number">{{ $memberSince ?? 'N/A' }}</div>
                    <div class="label">Member Since</div>
                </div>
            </div>

            <div class="hero-actions">
                <a href="{{ route('customer.orders.create') }}" class="btn-modern btn-light-modern">
                    <i class="fas fa-plus-circle"></i> New Order
                </a>
                <a href="{{ route('customer.orders') }}" class="btn-modern btn-outline-modern">
                    <i class="fas fa-box"></i> Track Orders
                </a>
                <a href="{{ route('customer.promotions') }}" class="btn-modern btn-outline-modern">
                    <i class="fas fa-tag"></i> Offers
                </a>
            </div>
        </div>
    </section>

    <!-- STATS CARDS -->
    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <h3>{{ $activeOrders ?? 0 }}</h3>
                    <p>Active Orders</p>
                </div>
                <div class="stat-icon icon-blue">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
            <div class="stat-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>+{{ $orderGrowth ?? 0 }}% this month</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <h3>{{ $completedOrders ?? 0 }}</h3>
                    <p>Completed Orders</p>
                </div>
                <div class="stat-icon icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>+8% this week</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <h3>${{ number_format($savings ?? 0, 2) }}</h3>
                    <p>Total Savings</p>
                </div>
                <div class="stat-icon icon-yellow">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="stat-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>Saved with discounts</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <h3>{{ $addresses->count() ?? 0 }}</h3>
                    <p>Saved Addresses</p>
                </div>
                <div class="stat-icon icon-red">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
            <div class="stat-trend">
                <i class="fas fa-plus-circle"></i>
                <span>Manage locations</span>
            </div>
        </div>
    </section>

    <!-- QUICK ACTIONS -->
    <section class="mt-5">
        <h4 class="section-title">Quick Actions</h4>
        <div class="actions-grid">
            <a href="{{ route('customer.orders.create') }}" class="action-card">
                <div class="action-icon"><i class="fas fa-plus"></i></div>
                <h6>Place Order</h6>
                <p>Create a new laundry request</p>
            </a>

            <a href="{{ route('customer.orders') }}" class="action-card">
                <div class="action-icon"><i class="fas fa-box"></i></div>
                <h6>My Orders</h6>
                <p>Track all your laundry orders</p>
            </a>

            <a href="{{ route('customer.addresses') }}" class="action-card">
                <div class="action-icon"><i class="fas fa-location-dot"></i></div>
                <h6>Addresses</h6>
                <p>Manage pickup & delivery spots</p>
            </a>

            <a href="{{ route('customer.profile') }}" class="action-card">
                <div class="action-icon"><i class="fas fa-user"></i></div>
                <h6>My Profile</h6>
                <p>Update personal information</p>
            </a>

            <a href="{{ route('customer.reviews') }}" class="action-card">
                <div class="action-icon"><i class="fas fa-star"></i></div>
                <h6>My Reviews</h6>
                <p>Write & manage reviews</p>
            </a>

            <a href="{{ route('customer.promotions') }}" class="action-card">
                <div class="action-icon"><i class="fas fa-tag"></i></div>
                <h6>Promotions</h6>
                <p>Available discounts & offers</p>
            </a>
        </div>
    </section>

    <!-- ORDER TRENDS CHART -->
    <section class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Order Analytics</h4>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary btn-sm chart-period active" data-period="12">12 Months</button>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="orderTrendsChart" height="80"></canvas>
        </div>
    </section>

    <!-- RECENT ORDERS TABLE -->
    <section class="mt-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-clock-rotate-left me-2 text-info"></i> Recent Orders</h5>
                <a href="{{ route('customer.orders') }}" class="btn-view">View All →</a>
            </div>

            @if(($recentOrders ?? collect())->count())
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        @php
                            $statusClass = match($order->status){
                                'delivered' => 'status-delivered',
                                'processing' => 'status-processing',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending'
                            };
                            $statusIcon = match($order->status){
                                'delivered' => 'fa-check-circle',
                                'processing' => 'fa-sync-alt',
                                'cancelled' => 'fa-times-circle',
                                default => 'fa-clock'
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>${{ number_format($order->total, 2) }}</strong></td>
                            <td><span class="status-badge {{ $statusClass }}"><i class="fas {{ $statusIcon }}"></i> {{ ucfirst($order->status) }}</span></td>
                            <td><a href="{{ route('customer.orders.show', $order) }}" class="btn-view"><i class="fas fa-eye"></i> View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h5>No Orders Yet</h5>
                <p class="text-muted mb-3">Start by placing your first laundry order.</p>
                <a href="{{ route('customer.orders.create') }}" class="btn-modern btn-light-modern" style="background:var(--primary); color:white;">
                    <i class="fas fa-plus-circle"></i> Place Order
                </a>
            </div>
            @endif
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Order Trends Chart
        const ctx = document.getElementById('orderTrendsChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels ?? []),
                    datasets: [{
                        label: 'Orders',
                        data: @json($chartData ?? []),
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0284c7',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { 
                            backgroundColor: '#0f172a',
                            titleColor: '#fff',
                            bodyColor: '#94a3b8'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Smooth fade animation for elements
        const animated = document.querySelectorAll('.hero, .stat-card, .action-card, .card-modern, .chart-container');
        animated.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            setTimeout(() => {
                el.style.transition = 'all 0.5s ease';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 80);
        });

        // Notification click handler
        document.getElementById('notificationBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            alert('📬 You have {{ $pendingOrders ?? 0 }} pending notifications:\n• Your orders are being processed\n• Check your delivery status\n• New promotions available!');
        });

        // Profile click handler
        document.getElementById('profileBox')?.addEventListener('click', function() {
            window.location.href = '{{ route("customer.profile") }}';
        });
    });
</script>
</body>
</html>