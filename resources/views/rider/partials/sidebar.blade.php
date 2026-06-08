<div class="card shadow-sm mb-4">
    <div class="card-body text-center">
        <div class="mb-3">
            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-motorcycle fa-3x"></i>
            </div>
        </div>
        <h5>{{ auth()->user()->name }}</h5>
        <p class="text-muted small">{{ auth()->user()->email }}</p>
        <hr>
        <div class="row text-start">
            <div class="col-6">
                <small class="text-muted">Online Status</small>
                <h6 class="mb-0">
                    <span class="badge bg-success">Online</span>
                </h6>
            </div>
            <div class="col-6">
                <small class="text-muted">Rating</small>
                <h6 class="mb-0">
                    <i class="fas fa-star text-warning"></i> 4.8
                </h6>
            </div>
        </div>
    </div>
</div>

<div class="list-group">
    <a href="{{ route('rider.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('rider.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
    </a>
    <a href="{{ route('rider.orders') }}" class="list-group-item list-group-item-action {{ request()->routeIs('rider.orders*') ? 'active' : '' }}">
        <i class="fas fa-box me-2"></i> My Deliveries
    </a>
    <a href="{{ route('rider.tracking') }}" class="list-group-item list-group-item-action {{ request()->routeIs('rider.tracking*') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt me-2"></i> Live Tracking
    </a>
    <a href="{{ route('rider.earnings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('rider.earnings*') ? 'active' : '' }}">
        <i class="fas fa-dollar-sign me-2"></i> Earnings
    </a>
    <a href="{{ route('rider.profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('rider.profile*') ? 'active' : '' }}">
        <i class="fas fa-user me-2"></i> Profile
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
</div>