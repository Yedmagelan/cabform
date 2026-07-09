<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — CabForm Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/img/Logo-CabForm.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        .topbar-cabform { background: var(--cb-dark-card) !important; border-bottom: 1px solid var(--cb-glass-border) !important; }
        .topbar-cabform h5, 
        .topbar-cabform .text-white, 
        .topbar-cabform .bell-icon, 
        .topbar-cabform .bell-icon i,
        .topbar-cabform .breadcrumb-item, 
        .topbar-cabform .breadcrumb-item a, 
        .topbar-cabform .breadcrumb-item.active {
            color: var(--cb-text-primary) !important;
        }
        .topbar-cabform .btn-dark, .topbar-cabform .btn-cabform-glass {
            background-color: var(--cb-dark-secondary) !important;
            border-color: var(--cb-glass-border) !important;
            color: var(--cb-text-primary) !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('partials.sidebar-admin')

    <!-- Main Content -->
    <div class="main-content-with-sidebar">
        <!-- Top Bar -->
        <div class="topbar-cabform">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebar-toggle" class="btn btn-cabform-glass btn-cabform-sm d-lg-none">
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-cabform-glass btn-cabform-sm position-relative" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;" id="notif-count">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div id="notif-list">
                            <p class="text-center text-cb-muted py-3 mb-0"><small>Aucune notification</small></p>
                        </div>
                    </div>
                </div>
                <!-- User -->
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">{{ auth()->user()->initials ?? 'AD' }}</div>
                        <div class="d-none d-md-block">
                            <div class="fw-600" style="font-size: 0.85rem; color: var(--cb-text-primary);">{{ auth()->user()->full_name ?? 'Admin' }}</div>
                            <div style="font-size: 0.75rem; color: var(--cb-text-muted);">Administrateur</div>
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mon profil</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-cb-danger"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-cb" style="background: rgba(0,217,126,0.1); color: var(--cb-success);">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-cb" style="background: rgba(230,55,87,0.1); color: var(--cb-danger);">
                    <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
