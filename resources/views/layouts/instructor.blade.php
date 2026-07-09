<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Formateur') — CabForm</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Logo-CabForm.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <!-- Custom styling extensions for formateur dashboard -->
    <style>
        body { font-family: 'DM Sans', sans-serif; background: var(--cb-dark); color: var(--cb-text-primary); }
        .sidebar-cabform { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background: var(--cb-dark-secondary); border-right: 1px solid var(--cb-glass-border); z-index: 100; transition: all 0.3s ease; }
        .sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 24px; border-bottom: 1px solid var(--cb-glass-border); }
        .sidebar-brand img { width: 36px; height: 36px; }
        .sidebar-nav { padding: 20px 12px; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: var(--cb-border-radius-sm); color: var(--cb-text-secondary); text-decoration: none; font-size: 0.925rem; font-weight: 500; transition: all 0.2s ease; }
        .sidebar-nav-item:hover, .sidebar-nav-item.active { background: rgba(var(--cb-primary-rgb), 0.12); color: var(--cb-primary-light); }
        .sidebar-nav-item i { font-size: 1.1rem; width: 20px; }
        .sidebar-section-title { font-size: 0.725rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--cb-text-muted); font-weight: 700; padding: 16px 16px 8px 16px; }
        .main-content-with-sidebar { margin-left: 280px; min-height: 100vh; transition: all 0.3s ease; }
        .topbar-cabform { height: 70px; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; background: var(--cb-dark-card); border-bottom: 1px solid var(--cb-glass-border); position: sticky; top: 0; z-index: 90; }
        .user-dropdown { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--cb-gradient-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .bell-icon { position: relative; cursor: pointer; color: var(--cb-text-muted); font-size: 1.2rem; transition: color 0.2s; }
        .bell-icon:hover { color: var(--cb-text-primary); }
        .bell-badge { position: absolute; top: -5px; right: -5px; width: 16px; height: 16px; border-radius: 50%; background: var(--cb-danger); color: #fff; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; }
        .card-instructor, .card-cabform, .dashboard-card { background: var(--cb-dark-card) !important; border: 1px solid var(--cb-glass-border) !important; border-radius: var(--cb-border-radius-lg) !important; color: var(--cb-text-primary) !important; }
        .card-kpi { padding: 24px; display: flex; align-items: center; gap: 20px; }
        .kpi-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .btn-premium { background: var(--cb-primary) !important; border: none !important; color: #fff !important; font-weight: 500; border-radius: var(--cb-border-radius-sm) !important; padding: 8px 16px; transition: all 0.2s; }
        .btn-premium:hover { background: var(--cb-primary-dark) !important; color: #fff !important; }
        .badge-draft { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }
        .badge-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .badge-published { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .badge-archived { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

        /* Force Light Theme Overrides on Tables, Cards, and Forms */
        .text-white { color: var(--cb-text-primary) !important; }
        .bg-dark { background-color: var(--cb-dark-secondary) !important; border-color: var(--cb-glass-border) !important; }
        .border-secondary { border-color: var(--cb-glass-border) !important; }
        .table-dark { 
            --bs-table-bg: var(--cb-dark-card) !important; 
            --bs-table-color: var(--cb-text-primary) !important; 
            --bs-table-border-color: var(--cb-glass-border) !important; 
            --bs-table-hover-bg: var(--cb-glass-bg-hover) !important; 
            border-color: var(--cb-glass-border) !important; 
        }
        .table-dark thead th { background: var(--cb-glass-bg) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .table-dark tbody tr:hover, .table-dark tbody tr:hover td, .table-dark tbody tr:hover th { background: var(--cb-glass-bg-hover) !important; background-color: var(--cb-glass-bg-hover) !important; color: var(--cb-text-primary) !important; }
        .list-group-item-dark { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .accordion-button { color: var(--cb-text-primary) !important; }
        .accordion-button:not(.collapsed) { background-color: rgba(var(--cb-primary-rgb), 0.05) !important; color: var(--cb-primary-light) !important; }
        .accordion-item { background-color: var(--cb-dark-card) !important; border-color: var(--cb-glass-border) !important; }
        .form-control { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .form-select { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .modal-content { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .modal-header, .modal-footer { border-color: var(--cb-glass-border) !important; }
        .dropdown-menu { background-color: var(--cb-dark-card) !important; border-color: var(--cb-glass-border) !important; }
        .dropdown-item { color: var(--cb-text-secondary) !important; }
        .dropdown-item:hover { color: var(--cb-text-primary) !important; background-color: var(--cb-glass-bg-hover) !important; }
        
        @media (max-width: 991.98px) {
            .sidebar-cabform { left: -280px; }
            .sidebar-cabform.show { left: 0; }
            .main-content-with-sidebar { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Formateur -->
    <aside class="sidebar-cabform" id="instructor-sidebar">
        <div class="sidebar-brand d-flex justify-content-center align-items-center py-4">
            <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" style="width: 200px; max-height: 80px; object-fit: contain !important; height: auto !important;">
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('instructor.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i><span>Tableau de bord</span>
            </a>
            <a href="{{ route('instructor.courses') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.courses*') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i><span>Mes formations</span>
            </a>
            <a href="{{ route('instructor.resources.library') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.resources*') ? 'active' : '' }}">
                <i class="fas fa-folder-open"></i><span>Médiathèque</span>
            </a>
            <a href="{{ route('instructor.statistics.index') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.statistics*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i><span>Rapports & Stats</span>
            </a>
            <div class="sidebar-section-title">Communication</div>
            <a href="{{ route('instructor.messages.index') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.messages*') ? 'active' : '' }}">
                <i class="fas fa-comment-alt"></i><span>Messagerie</span>
            </a>
            <a href="{{ route('instructor.notifications.index') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.notifications*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i><span>Notifications</span>
            </a>
            <div class="sidebar-section-title">Paramètres</div>
            <a href="{{ route('instructor.profile.show') }}" class="sidebar-nav-item {{ request()->routeIs('instructor.profile*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i><span>Profil & Compte</span>
            </a>
            <div style="padding: 20px 16px; margin-top: 20px; border-top: 1px solid var(--cb-glass-border);">
                <a href="{{ url('/') }}" class="sidebar-nav-item" style="color: var(--cb-text-muted);">
                    <i class="fas fa-globe"></i><span>Retour au site</span>
                </a>
            </div>
        </nav>
    </aside>
 
    <div class="main-content-with-sidebar">
        <!-- Topbar -->
        <header class="topbar-cabform">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebar-toggle" class="btn btn-dark d-lg-none"><i class="fas fa-bars"></i></button>
                <h5 class="mb-0 fw-bold text-white">@yield('page_title', 'Espace Formateur')</h5>
            </div>
            <div class="d-flex align-items-center gap-4">
                <!-- Notifications Bell -->
                @php
                    $unreadNotifications = auth()->user()->unreadNotifications;
                    $unreadCount = $unreadNotifications->count();
                @endphp
                <div class="dropdown">
                    <div class="bell-icon" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @if($unreadCount > 0)
                            <span class="bell-badge">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    <div class="dropdown-menu dropdown-menu-end p-0 bg-dark border-secondary" style="width: 320px; border-radius: 8px;">
                        <div class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-white">Notifications</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('instructor.notifications.mark-all-read') }}" method="POST">@csrf
                                    <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="color: #818cf8; font-size: 0.8rem;">Tout marquer comme lu</button>
                                </form>
                            @endif
                        </div>
                        <div style="max-height: 240px; overflow-y: auto;">
                            @if($unreadCount === 0)
                                <div class="p-4 text-center text-muted" style="font-size: 0.85rem;">
                                    <i class="fas fa-check-circle mb-2 d-block" style="font-size: 1.5rem; color: #10b981;"></i>
                                    Aucune notification non lue.
                                </div>
                            @else
                                @foreach($unreadNotifications->take(4) as $notif)
                                    <div class="p-3 border-bottom border-secondary dropdown-item text-wrap bg-dark text-white" style="font-size: 0.85rem;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <strong class="text-white">{{ $notif->data['title'] ?? 'Notification' }}</strong>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mb-2 text-muted" style="font-size: 0.8rem;">{{ $notif->data['message'] ?? '' }}</p>
                                        <form action="{{ route('instructor.notifications.read', $notif->id) }}" method="POST">@csrf
                                            <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-indigo" style="font-size: 0.75rem; color: #818cf8;">Marquer comme lu</button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="p-2 border-top border-secondary text-center">
                            <a href="{{ route('instructor.notifications.index') }}" class="text-indigo text-decoration-none" style="font-size: 0.8rem; color: #818cf8; font-weight: 500;">Voir toutes les notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">{{ auth()->user()->initials ?? 'F' }}</div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-white" style="font-size: 0.85rem;">{{ auth()->user()->full_name }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">Formateur</div>
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                        <a class="dropdown-item text-white" href="{{ route('instructor.profile.show') }}"><i class="fas fa-user-cog me-2"></i>Mon profil</a>
                        <div class="dropdown-divider border-secondary"></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-4">
            @if(session('success'))
                <div class="alert alert-dismissible fade show border-0 rounded-3 mb-4" style="background: rgba(16,185,129,0.1); color: #10b981;">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-dismissible fade show border-0 rounded-3 mb-4" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebar-toggle').on('click', function() {
                $('#instructor-sidebar').toggleClass('show');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
