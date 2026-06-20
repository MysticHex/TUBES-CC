<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Cloud Computing Project</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    {{-- Sidebar (offcanvas on mobile, fixed on desktop) --}}
    <nav class="sidebar offcanvas-md offcanvas-start text-white p-3" tabindex="-1" id="sidebar">
        <div class="offcanvas-header px-1 d-md-none">
            <span class="brand h5 mb-0">☁ CloudComputing</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
        </div>
        <div class="d-none d-md-flex align-items-center gap-2 px-2 py-3 mb-2">
            <span class="fs-3">☁</span>
            <span class="brand h5 mb-0">CloudComputing</span>
        </div>

        <ul class="nav nav-pills flex-column position-relative" style="z-index:1">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}" href="{{ route('members.index') }}">
                    <i class="bi bi-people-fill"></i> Group Members
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
                    <i class="bi bi-mortarboard-fill"></i> Profile
                </a>
            </li>
        </ul>

        <div class="mt-auto small text-white-50 px-2 position-relative" style="z-index:1">
            <i class="bi bi-hdd-network"></i> {{ $serverName }}
        </div>
    </nav>

    <div class="content d-flex flex-column">
        {{-- Topbar --}}
        <header class="topbar d-flex align-items-center justify-content-between px-3 px-md-4 py-3 sticky-top">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm d-md-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h5 mb-0 fw-bold">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill text-bg-success d-none d-sm-inline-flex align-items-center gap-2 px-3 py-2">
                    <span class="status-dot"></span> {{ $serverName }}
                </span>
                <div class="dropdown">
                    <button class="btn btn-light border d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()?->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()?->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="p-3 p-md-4 flex-grow-1">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="text-center text-muted small py-3">
            Cloud Computing Project &middot; Served by <strong>{{ $serverName }}</strong>
            <div class="text-success fw-semibold">Running on AWS EC2 - Instance 2</div>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
