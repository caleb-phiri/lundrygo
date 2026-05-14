@extends('layouts.app')

@section('title', 'Manage Promotions')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Promotions Management</h5>
                    <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Promotion
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Discount</th>
                                    <th>Valid From</th>
                                    <th>Valid To</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($promotions as $promotion)
                                <tr>
                                    <td>{{ $promotion->id }}</td>
                                    <td>{{ $promotion->code }}</td>
                                    <td>{{ $promotion->discount_percent }}%</td>
                                    <td>{{ $promotion->valid_from ?? 'N/A' }}</td>
                                    <td>{{ $promotion->valid_to ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ now()->between($promotion->valid_from, $promotion->valid_to) ? 'success' : 'secondary' }}">
                                            {{ now()->between($promotion->valid_from, $promotion->valid_to) ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.promotions.edit', $promotion->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" 
                                              method="POST" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Delete this promotion?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No promotions found.</td>
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
@endsection