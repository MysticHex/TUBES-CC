@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Load-balancer instance banner --}}
    <div class="alert alert-success border-0 shadow-sm text-center fw-bold fs-5 text-uppercase mb-4 reveal"
         style="letter-spacing:2px">
        <i class="bi bi-hdd-network-fill me-2"></i> WEB INSTANCE 2
    </div>

    <div class="row g-3 g-md-4">
        {{-- Total members --}}
        <div class="col-12 col-sm-6 col-xl-3 reveal">
            <div class="card-3d stat-card bg-grad-1 tilt h-100 p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-semibold">Total Members</div>
                        <div class="stat-value mt-2">{{ $totalMembers }}</div>
                    </div>
                    <i class="bi bi-people-fill stat-icon"></i>
                </div>
                <a href="{{ route('members.index') }}" class="stretched-link text-white-50 small mt-3 text-decoration-none">
                    Manage members <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Logged in user --}}
        <div class="col-12 col-sm-6 col-xl-3 reveal">
            <div class="card-3d stat-card bg-grad-2 tilt h-100 p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-semibold">Logged In As</div>
                        <div class="h4 fw-bold mt-2 mb-0 text-truncate">{{ auth()->user()->name }}</div>
                        <div class="small text-white-50 text-truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <i class="bi bi-person-badge-fill stat-icon"></i>
                </div>
            </div>
        </div>

        {{-- Database status --}}
        <div class="col-12 col-sm-6 col-xl-3 reveal">
            <div class="card-3d stat-card {{ $databaseStatus ? 'bg-grad-3' : 'bg-grad-4' }} tilt h-100 p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-semibold">Database (MySQL)</div>
                        <div class="h4 fw-bold mt-2 mb-0">{{ $databaseStatus ? 'Connected' : 'Unreachable' }}</div>
                        <div class="small text-white-50">Connection: {{ config('database.default') }}</div>
                    </div>
                    <i class="bi bi-database-fill-check stat-icon"></i>
                </div>
            </div>
        </div>

        {{-- Current server label --}}
        <div class="col-12 col-sm-6 col-xl-3 reveal">
            <div class="card-3d stat-card bg-grad-3 tilt h-100 p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-semibold">Current Server</div>
                        <div class="h4 fw-bold mt-2 mb-0">{{ $serverName }}</div>
                        <div class="small text-white-50">Behind Load Balancer</div>
                    </div>
                    <i class="bi bi-hdd-network-fill stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Server status widget + quick actions --}}
    <div class="row g-3 g-md-4 mt-1">
        <div class="col-12 col-lg-7 reveal">
            @include('partials.server-status')
        </div>

        <div class="col-12 col-lg-5 reveal">
            <div class="card-3d h-100 p-4">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Quick Actions</h2>
                <div class="d-grid gap-2">
                    <a href="{{ route('members.create') }}" class="btn btn-primary text-start">
                        <i class="bi bi-person-plus me-2"></i> Add Group Member
                    </a>
                    <a href="{{ route('members.index') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-people me-2"></i> View Member List
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-mortarboard me-2"></i> Group Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
