@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-2">
            @include('admin.partials.sidebar')
        </div>
        
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">System Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ config('app.name') }}">
                        </div>
                        <div class="mb-3">
                            <label>Site Email</label>
                            <input type="email" name="site_email" class="form-control" value="{{ config('mail.from.address') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection