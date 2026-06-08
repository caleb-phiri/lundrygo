@extends('layouts.app')

@section('title', 'My Earnings')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3">
            @include('rider.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <!-- Earnings Summary -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="mb-0">Total Earnings</h6>
                            <h2 class="mb-0">${{ number_format($totalEarnings, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="mb-0">Completed Deliveries</h6>
                            <h2 class="mb-0">{{ $earnings->total() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Earnings Chart -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Monthly Earnings</h6>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart" height="200"></canvas>
                </div>
            </div>
            
            <!-- Daily Earnings Table -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Daily Earnings</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Deliveries</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($earnings as $earning)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($earning->date)->format('M d, Y') }}</td>
                                    <td>{{ $earning->deliveries_count ?? 0 }}</td>
                                    <td class="fw-bold text-success">${{ number_format($earning->total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No earnings yet.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $earnings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyEarnings->pluck('month')),
            datasets: [{
                label: 'Monthly Earnings ($)',
                data: @json($monthlyEarnings->pluck('total')),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>
@endpush
@endsection