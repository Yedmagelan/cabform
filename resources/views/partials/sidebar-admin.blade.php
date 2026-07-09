<!-- Admin Sidebar -->
<aside class="sidebar-cabform" id="admin-sidebar">
    <!-- Brand -->
    <div class="sidebar-brand d-flex justify-content-center align-items-center py-4">
        <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" style="width: 200px; max-height: 80px; object-fit: contain !important; height: auto !important;">
    </div>

    <nav class="sidebar-nav">
        <!-- Tableau de bord -->
        <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>Tableau de bord</span>
        </a>

        <!-- Gestion des cours -->
        <div class="sidebar-section-title">Formations</div>
        <a href="{{ route('admin.courses.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            <span>Formations</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Catégories</span>
        </a>
        <a href="{{ route('admin.quizzes.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
            <i class="fas fa-question-circle"></i>
            <span>Quiz & Examens</span>
        </a>

        <!-- Utilisateurs -->
        <div class="sidebar-section-title">Utilisateurs</div>
        <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Utilisateurs</span>
        </a>
        <a href="{{ route('admin.enrollments.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span>Mes apprenants</span>
        </a>
        <a href="{{ route('admin.sessions.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Sessions / Cohortes</span>
        </a>
        <a href="{{ route('admin.partners.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}">
            <i class="fas fa-handshake"></i>
            <span>Partenaires B2B</span>
        </a>

        <!-- Finance -->
        <div class="sidebar-section-title">Finance</div>
        <a href="{{ route('admin.payments.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i>
            <span>Paiements</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Commandes</span>
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>Coupons</span>
        </a>

        <!-- Certificats -->
        <div class="sidebar-section-title">Certifications</div>
        <a href="{{ route('admin.certificates.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
            <i class="fas fa-award"></i>
            <span>Certificats</span>
        </a>

        <!-- CMS -->
        <div class="sidebar-section-title">Contenu</div>
        <a href="{{ route('admin.pages.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span>Pages</span>
        </a>
        <a href="{{ route('admin.blog.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i>
            <span>Blog</span>
        </a>
        <a href="{{ route('admin.banners.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <i class="fas fa-images"></i>
            <span>Bannières</span>
        </a>
        <a href="{{ route('admin.faqs.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
            <i class="fas fa-question"></i>
            <span>FAQ</span>
        </a>

        <!-- Système -->
        <div class="sidebar-section-title">Système</div>
        <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Journal d'audit</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Paramètres</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Rapports</span>
        </a>

        <!-- Retour au site -->
        <div style="padding: 20px 16px; margin-top: 20px; border-top: 1px solid var(--cb-glass-border);">
            <a href="{{ url('/') }}" class="sidebar-nav-item" style="color: var(--cb-text-muted);">
                <i class="fas fa-external-link-alt"></i>
                <span>Voir le site</span>
            </a>
        </div>
    </nav>
</aside>
