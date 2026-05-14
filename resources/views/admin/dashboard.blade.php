@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-0">Admin Dashboard</h1>
            <p class="text-muted">Manage your laundry business efficiently</p>
        </div>
        <div class="d-flex gap-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary rounded-3" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i> Export Report
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i> PDF Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i> Excel Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i> Print</a></li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-primary rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus me-2"></i> Quick Action
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fas fa-box me-2"></i> View Orders</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.users.create') }}"><i class="fas fa-user-plus me-2"></i> Add User</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.services.index') }}"><i class="fas fa-tshirt me-2"></i> Manage Services</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.promotions.create') }}"><i class="fas fa-tag me-2"></i> Add Promotion</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                        <div class="dropdown">
                            <i class="fas fa-ellipsis-h text-muted" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Last 7 days</a></li>
                                <li><a class="dropdown-item" href="#">Last 30 days</a></li>
                                <li><a class="dropdown-item" href="#">This year</a></li>
                            </ul>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_orders']) }}</h3>
                    <p class="text-muted mb-0">Total Orders</p>
                    <div class="mt-2">
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="fas fa-arrow-up me-1"></i> +12.5%
                        </span>
                        <span class="text-muted ms-2 small">vs last month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p class="text-muted mb-0">Total Revenue</p>
                    <div class="mt-2">
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="fas fa-arrow-up me-1"></i> +18.2%
                        </span>
                        <span class="text-muted ms-2 small">vs last month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_customers']) }}</h3>
                    <p class="text-muted mb-0">Total Customers</p>
                    <div class="mt-2">
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="fas fa-arrow-up me-1"></i> +8.3%
                        </span>
                        <span class="text-muted ms-2 small">new this month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="fas fa-motorcycle fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">{{ number_format($stats['total_riders']) }}</h3>
                    <p class="text-muted mb-0">Active Riders</p>
                    <div class="mt-2">
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="fas fa-user-plus me-1"></i> +5
                        </span>
                        <span class="text-muted ms-2 small">this month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">Revenue & Orders Overview</h5>
                            <p class="text-muted small mb-0">Last 30 days performance</p>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary active">Weekly</button>
                            <button class="btn btn-outline-secondary">Monthly</button>
                            <button class="btn btn-outline-secondary">Yearly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-1">Order Status</h5>
                    <p class="text-muted small mb-0">Current distribution</p>
                </div>
                <div class="card-body p-4">
                    <canvas id="statusChart" height="250"></canvas>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-circle text-warning me-2"></i> Pending</span>
                            <span class="fw-bold">{{ $stats['pending_orders'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-circle text-info me-2"></i> Processing</span>
                            <span class="fw-bold">{{ $stats['processing_orders'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-circle text-success me-2"></i> Delivered</span>
                            <span class="fw-bold">{{ $stats['delivered_orders'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-circle text-danger me-2"></i> Cancelled</span>
                            <span class="fw-bold">{{ $stats['cancelled_orders'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                                <div class="action-card text-center p-3 rounded-4 border hover-scale">
                                    <div class="action-icon bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-box fa-xl text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Manage Orders</h6>
                                    <small class="text-muted">View & update orders</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                                <div class="action-card text-center p-3 rounded-4 border hover-scale">
                                    <div class="action-icon bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-users fa-xl text-success"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">User Management</h6>
                                    <small class="text-muted">Add/edit users & roles</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.services.index') }}" class="text-decoration-none">
                                <div class="action-card text-center p-3 rounded-4 border hover-scale">
                                    <div class="action-icon bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-tshirt fa-xl text-info"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Services</h6>
                                    <small class="text-muted">Manage laundry services</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('admin.promotions.index') }}" class="text-decoration-none">
                                <div class="action-card text-center p-3 rounded-4 border hover-scale">
                                    <div class="action-icon bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-tag fa-xl text-warning"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Promotions</h6>
                                    <small class="text-muted">Create discount offers</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Recent Orders</h5>
                        <p class="text-muted small mb-0">Latest 10 customer orders</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary rounded-3">
                        View All <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Order #</th>
                                    <th class="py-3">Customer</th>
                                    <th class="py-3">Amount</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Date</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td class="px-4">
                                        <span class="fw-bold">{{ $order->order_number ?? '#' . $order->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                {{ substr($order->user->name ?? 'N/A', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $order->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($order->status) {
                                                'delivered' => 'success',
                                                'processing' => 'info',
                                                'cancelled' => 'danger',
                                                default => 'warning'
                                            };
                                            $statusIcon = match($order->status) {
                                                'delivered' => 'fa-check-circle',
                                                'processing' => 'fa-sync-alt',
                                                'cancelled' => 'fa-times-circle',
                                                default => 'fa-clock'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-2 rounded-pill">
                                            <i class="fas {{ $statusIcon }} me-1"></i> {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">No orders found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }
    
    .hover-scale {
        transition: transform 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.05);
    }
    
    .action-card {
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
    }
    
    .action-card:hover {
        background: #f8f9fa;
        border-color: #0ea5e9 !important;
    }
    
    .avatar-circle {
        width: 35px;
        height: 35px;
        font-weight: 600;
    }
    
    .stat-icon {
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .rounded-4 {
        border-radius: 1rem !important;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue & Orders Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels'] ?? []) !!},
            datasets: [
                {
                    label: 'Orders',
                    data: {!! json_encode($chartData['orders'] ?? []) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0284c7',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue ($)',
                    data: {!! json_encode($chartData['revenue'] ?? []) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#059669',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#94a3b8',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e2e8f0' },
                    title: {
                        display: true,
                        text: 'Number of Orders',
                        color: '#64748b'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: { display: false },
                    title: {
                        display: true,
                        text: 'Revenue ($)',
                        color: '#64748b'
                    }
                },
                x: {
                    grid: { display: false },
                    title: {
                        display: true,
                        text: 'Date',
                        color: '#64748b'
                    }
                }
            }
        }
    });
    
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Delivered', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $stats['pending_orders'] ?? 0 }},
                    {{ $stats['processing_orders'] ?? 0 }},
                    {{ $stats['delivered_orders'] ?? 0 }},
                    {{ $stats['cancelled_orders'] ?? 0 }}
                ],
                backgroundColor: [
                    '#f59e0b',
                    '#0ea5e9',
                    '#10b981',
                    '#ef4444'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
    
    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.6s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
});
</script>
@endpush