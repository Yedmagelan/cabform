<!-- Navbar CabForm — Edutek Inspired Layout -->
<nav class="navbar navbar-expand-lg navbar-cabform fixed-top" id="main-navbar">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" class="navbar-logo">
        </a>

        <!-- Mobile Toggle (hamburger icon) -->
        <button class="navbar-toggler border-0 p-2 ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas" aria-controls="navbarOffcanvas">
            <span class="toggler-icon-cabform"><i class="fas fa-bars"></i></span>
        </button>

        <!-- Nav Content (Offcanvas on mobile, slides from LEFT) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel">
            <div class="offcanvas-header d-flex justify-content-end p-4 d-lg-none">
                <button type="button" class="btn-close-circle" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="offcanvas-body">
                <!-- Nav Links (Centered) -->
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-lg-3 gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('catalog*') ? 'active' : '' }}" href="{{ url('/catalog') }}">Formations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ url('/about') }}">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('blog*') ? 'active' : '' }}" href="{{ url('/blog') }}">Blogs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="{{ url('/contact') }}">Contact</a>
                    </li>
                </ul>

                <!-- Auth Section (Custom Desktop/Mobile layouts) -->
                <div class="auth-section-cabform d-flex align-items-center mt-4 mt-lg-0">
                    @guest
                        <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 w-100">
                            <a href="{{ route('login') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-sm pulse-glow text-center">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-cabform btn-cabform-outline btn-cabform-sm text-center">
                                <i class="fas fa-user-plus me-2"></i>Inscrivez-vous
                            </a>
                        </div>
                    @else
                        <div class="dropdown w-100">
                            <button class="user-dropdown-trigger-cabform d-flex align-items-center justify-content-between justify-content-lg-start gap-2 w-100 border-0" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar-navbar">{{ auth()->user()->initials ?? substr(auth()->user()->first_name, 0, 1) }}</div>
                                    <span class="user-name-navbar">{{ auth()->user()->first_name }}</span>
                                </div>
                                <i class="fas fa-chevron-down arrow-icon" style="font-size: 0.75rem;"></i>
                            </button>
                            
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-cabform w-100 mt-2">
                                <div class="px-3 py-2 border-bottom border-light mb-1 d-lg-none">
                                    <div class="fw-bold" style="font-size: 0.9rem; color: var(--cb-text-primary);">{{ auth()->user()->full_name ?? (auth()->user()->first_name . ' ' . auth()->user()->last_name) }}</div>
                                    <div style="font-size: 0.75rem; color: var(--cb-text-muted);">{{ auth()->user()->email }}</div>
                                </div>
                                
                                @if(method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-cb-primary"></i>Administration</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                @if(method_exists(auth()->user(), 'isInstructor') && auth()->user()->isInstructor())
                                    <li><a class="dropdown-item" href="{{ route('instructor.dashboard') }}"><i class="fas fa-chalkboard-teacher me-2 text-cb-primary"></i>Espace Formateur</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('learner.dashboard') }}"><i class="fas fa-graduation-cap me-2"></i>Mes formations</a></li>
                                <li><a class="dropdown-item" href="{{ route('learner.certificates') }}"><i class="fas fa-certificate me-2"></i>Mes certificats</a></li>
                                <li><a class="dropdown-item" href="{{ route('learner.profile') }}"><i class="fas fa-user-circle me-2"></i>Mon profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-cb-danger w-100 text-start border-0 bg-transparent">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
                
            </div>
        </div>
    </div>
</nav>