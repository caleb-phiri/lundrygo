<div class="list-group shadow-sm">
    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
    </a>
    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users me-2"></i> Users
    </a>
    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i class="fas fa-box me-2"></i> Orders
    </a>
    <a href="{{ route('admin.services.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
        <i class="fas fa-tshirt me-2"></i> Services
    </a>
    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <i class="fas fa-tags me-2"></i> Categories
    </a>
    <a href="{{ route('admin.promotions.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
        <i class="fas fa-gift me-2"></i> Promotions
    </a>
    <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="fas fa-chart-line me-2"></i> Reports
    </a>
    <a href="{{ route('admin.settings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <i class="fas fa-cog me-2"></i> Settings
    </a>
</div>