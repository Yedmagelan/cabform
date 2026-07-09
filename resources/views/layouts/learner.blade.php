<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mon Espace') — CabForm</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Logo-CabForm.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        body { background: var(--cb-dark); color: var(--cb-text-primary); }
        .card-instructor { background: var(--cb-dark-card) !important; border: 1px solid var(--cb-glass-border) !important; border-radius: var(--cb-border-radius-lg); color: var(--cb-text-primary) !important; }
        .main-content-with-sidebar .text-white { color: var(--cb-text-primary) !important; }
        .main-content-with-sidebar .bg-dark { background-color: var(--cb-dark-secondary) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .border-secondary { border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .table-dark { --bs-table-bg: var(--cb-dark-card) !important; --bs-table-color: var(--cb-text-primary) !important; --bs-table-hover-bg: var(--cb-glass-bg-hover) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .table-dark tbody tr:hover, .main-content-with-sidebar .table-dark tbody tr:hover td, .main-content-with-sidebar .table-dark tbody tr:hover th { background: var(--cb-glass-bg-hover) !important; background-color: var(--cb-glass-bg-hover) !important; color: var(--cb-text-primary) !important; }
        .main-content-with-sidebar .list-group-item-dark { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .accordion-button { color: var(--cb-text-primary) !important; }
        .main-content-with-sidebar .accordion-button:not(.collapsed) { background-color: rgba(var(--cb-primary-rgb), 0.05) !important; color: var(--cb-primary-light) !important; }
        .main-content-with-sidebar .accordion-item { background-color: var(--cb-dark-card) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .form-control { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .form-select { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .modal-content { background-color: var(--cb-dark-card) !important; color: var(--cb-text-primary) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .modal-header, .main-content-with-sidebar .modal-footer { border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .dropdown-menu { background-color: var(--cb-dark-card) !important; border-color: var(--cb-glass-border) !important; }
        .main-content-with-sidebar .dropdown-item { color: var(--cb-text-secondary) !important; }
        .main-content-with-sidebar .dropdown-item:hover { color: var(--cb-text-primary) !important; background-color: var(--cb-glass-bg-hover) !important; }
        .btn-premium { background: var(--cb-primary) !important; border: none !important; color: #fff !important; border-radius: var(--cb-border-radius-sm) !important; }
        .btn-premium:hover { background: var(--cb-primary-dark) !important; color: #fff !important; }
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
    <!-- Sidebar Learner -->
    <aside class="sidebar-cabform" id="learner-sidebar">
        <div class="sidebar-brand d-flex justify-content-center align-items-center py-4">
            <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" style="width: 200px; max-height: 80px; object-fit: contain !important; height: auto !important;">
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('learner.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('learner.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i><span>Tableau de bord</span>
            </a>
            <div class="sidebar-section-title">Apprentissage</div>
            <a href="{{ route('learner.dashboard') }}" class="sidebar-nav-item">
                <i class="fas fa-book-open"></i><span>Mes formations</span>
            </a>
            <a href="{{ route('learner.certificates') }}" class="sidebar-nav-item {{ request()->routeIs('learner.certificates') ? 'active' : '' }}">
                <i class="fas fa-award"></i><span>Mes certificats</span>
            </a>
            <a href="{{ route('learner.orders.index') }}" class="sidebar-nav-item {{ request()->routeIs('learner.orders.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i><span>Mes commandes</span>
            </a>
            <div class="sidebar-section-title">Compte</div>
            <a href="{{ route('learner.profile') }}" class="sidebar-nav-item {{ request()->routeIs('learner.profile') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i><span>Mon profil</span>
            </a>
            @php
                $layoutUnreadNotifications = auth()->user()->unreadNotifications ?? collect();
                $layoutUnreadNotificationsCount = auth()->user()->unreadNotifications()->count();
                $layoutUnreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
            @endphp
            <a href="{{ route('learner.messages.index') }}" class="sidebar-nav-item {{ request()->routeIs('learner.messages.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i><span>Messages</span>
                @if($layoutUnreadMessages > 0)
                    <span class="badge bg-danger ms-auto">{{ $layoutUnreadMessages }}</span>
                @endif
            </a>
            <div style="padding: 20px 16px; margin-top: 20px; border-top: 1px solid var(--cb-glass-border);">
                <a href="{{ url('/catalog') }}" class="sidebar-nav-item" style="color: var(--cb-primary-light);">
                    <i class="fas fa-plus-circle"></i><span>Explorer les formations</span>
                </a>
                <a href="{{ url('/') }}" class="sidebar-nav-item" style="color: var(--cb-text-muted);">
                    <i class="fas fa-external-link-alt"></i><span>Retour au site</span>
                </a>
            </div>
        </nav>
    </aside>

    <div class="main-content-with-sidebar">
        <div class="topbar-cabform">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebar-toggle" class="btn btn-cabform-glass btn-cabform-sm d-lg-none"><i class="fas fa-bars"></i></button>
                <h5 class="mb-0 fw-700" style="font-size: 1.1rem;">@yield('page_title', 'Mon Espace')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-cabform-glass btn-cabform-sm position-relative text-white" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @if($layoutUnreadNotificationsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ $layoutUnreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary p-2 text-white" style="min-width: 280px;">
                        <li class="p-2 border-bottom border-secondary d-flex justify-content-between align-items-center">
                            <strong class="text-white">Notifications</strong>
                            <small class="text-muted">{{ $layoutUnreadNotificationsCount }} non lues</small>
                        </li>
                        @forelse($layoutUnreadNotifications->take(5) as $noti)
                            <li class="p-2 border-bottom border-secondary" style="font-size: 0.8rem;">
                                <span class="d-block text-white">{{ $noti->data['message'] ?? 'Notification reçue' }}</span>
                                <small class="text-muted">{{ $noti->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="p-3 text-center text-muted" style="font-size: 0.85rem;">Aucune nouvelle notification.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">{{ auth()->user()->initials ?? 'U' }}</div>
                        <div class="d-none d-md-block">
                            <div class="fw-600" style="font-size: 0.85rem; color: var(--cb-text-primary);">{{ auth()->user()->full_name ?? 'Utilisateur' }}</div>
                            <div style="font-size: 0.75rem; color: var(--cb-text-muted);">Apprenant</div>
                        </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('learner.profile') }}"><i class="fas fa-user me-2"></i>Mon profil</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="dropdown-item text-cb-danger"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-dismissible fade show border-0 rounded-cb mb-4" style="background: rgba(0,217,126,0.1); color: var(--cb-success);">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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
