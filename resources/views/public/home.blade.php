@extends('layouts.app')

@section('title', 'Accueil')
@section('meta_description', 'CabForm - Plateforme de formation et certification en ligne. Développez vos compétences avec des formations certifiantes de qualité professionnelle.')

@section('content')

    <!-- ══════════════════════════ HERO SECTION ══════════════════════════ -->
    <section class="hero-section">
        <!-- Decorative Elements -->
        <div style="position: absolute; top: 20%; right: 10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(5,0,216,0.08) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>
        <div style="position: absolute; bottom: 20%; left: 5%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(77,107,254,0.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb- mb-lg-0">
                    <div class="fade-in">
                        <span class="section-subtitle">
                            <i class="fas fa-graduation-cap me-2"></i>Plateforme de Formation Certifiante
                        </span>
                        <h1 class="hero-title">
                            Formez-vous.<br>
                            <span class="text-gradient">Certifiez-vous.</span><br>
                            Évoluez.
                        </h1>
                        <p class="hero-description">
                            Accédez à des formations professionnelles certifiantes, suivez votre progression en temps réel et obtenez des certificats reconnus avec vérification QR code.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ url('/catalog') }}" class="btn btn-cabform btn-cabform-primary pulse-glow">
                                <i class="fas fa-rocket me-2"></i>Découvrir les formations
                            </a>
                            <a href="{{ url('/verify-certificate') }}" class="btn btn-cabform btn-cabform-outline">
                                <i class="fas fa-qrcode me-2"></i>Vérifier un certificat
                            </a>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="fade-in-right text-center d-flex justify-content-center align-items-center h-100">
                        <img src="{{ asset('assets/img/banner1.png') }}" alt="CabForm Banner" class="img-fluid rounded-cb" style="max-height: 550px; width: auto; max-width: 100%; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════ REJOINS LA FORMATION ═════════════════════════════════ -->
    <section class="section-darker py-5" id="home-categories-explore">
        <div class="container text-center">
            <div class="mx-auto mb-5 fade-in" style="max-width: 800px;">
                <h2 class="fw-bold mb-3" style="font-size: 2.25rem; color: var(--cb-text-primary);">
                    Rejoins la formation qui correspond à tes besoins
                </h2>
                <p class="text-cb-muted mb-4" style="font-size: 1.1rem; line-height: 1.6;">
                    Que tu souhaites t'initier à un domaine, accélérer ta carrière avec une certification internationale, ou changer de carrière, nous avons la formation faite pour toi.
                </p>
                
                <!-- Checkmarks list -->
                <div class="d-flex flex-wrap justify-content-center gap-4 mt-3" style="font-size: 1rem; font-weight: 500;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check text-cb-success" style="font-size: 1.1rem;"></i>
                        <span style="color: var(--cb-text-primary);">40 à 360 heures</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check text-cb-success" style="font-size: 1.1rem;"></i>
                        <span style="color: var(--cb-text-primary);">Cours en direct</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check text-cb-success" style="font-size: 1.1rem;"></i>
                        <span style="color: var(--cb-text-primary);">En présentiel ou en ligne</span>
                    </div>
                </div>
            </div>

            <!-- Categories Container -->
            <div class="categories-container-explore bg-glass border-glass rounded-cb p-4 mx-auto fade-in shadow-sm mt-4" style="max-width: 900px;">
                <div class="d-flex flex-wrap justify-content-center gap-3 align-items-center">
                    @foreach($categories as $index => $category)
                        <a href="{{ route('catalog', ['category' => $category->slug]) }}" 
                           class="category-pill-explore d-flex align-items-center gap-2 px-4 py-2 text-decoration-none transition"
                           style="
                             background: transparent;
                             border: 1px solid rgba(5, 0, 216, 0.1);
                             border-radius: 10px;
                             color: var(--cb-text-primary); 
                             font-size: 0.95rem; 
                             font-weight: 600;
                           "
                           onmouseover="this.style.background='rgba(5, 0, 216, 0.05)!'; this.style.color='var(--cb-primary)'; this.querySelector('i').style.color='var(--cb-primary)';"
                           onmouseout="this.style.background='transparent'; this.style.color='var(--cb-text-primary)'; this.querySelector('i').style.color='var(--cb-primary)';"
                           >
                            <i class="fas {{ $category->icon ?? 'fa-graduation-cap' }} transition" style="font-size: 1rem; color: var(--cb-primary);"></i>
                            <span>{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════ FORMATIONS EN VEDETTE ════════════════ -->
    <section class="section-dark">
        <div class="container">
            <div class="text-center mb-5 fade-in">
                <span class="section-subtitle">Notre catalogue</span>
                <h2 class="section-title">Formations <span class="text-gradient">certifiantes</span></h2>
                <p class="section-description">Découvrez nos formations professionnelles conçues par des experts pour vous permettre d'acquérir des compétences reconnues.</p>
            </div>

            <div class="row g-4">
                @forelse($featuredCourses ?? [] as $course)
                    <div class="col-lg-4 col-md-6 fade-in">
                        <div class="card-cabform h-100">
                            <div class="card-img-wrapper">
                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('assets/img/Logo-CabForm.png') }}" class="card-img-top" alt="{{ $course->title }}">
                                <span class="badge-price {{ $course->is_free ? 'badge-free' : '' }}">
                                    {{ $course->formatted_price }}
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge-cabform badge-primary">{{ $course->category->name ?? 'Général' }}</span>
                                    <span class="badge-cabform" style="background: rgba(var(--cb-accent-rgb), 0.1); color: var(--cb-accent);">{{ $course->level_label }}</span>
                                </div>
                                <h5 class="card-title">{{ $course->title }}</h5>
                                <p class="card-text flex-grow-1">{{ Str::limit($course->description, 150) }}</p>
                                <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid var(--cb-glass-border);">
                                    <!-- <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width:30px;height:30px;font-size:0.65rem;">{{ $course->instructor->initials ?? 'CF' }}</div>
                                        <span class="text-cb-muted" style="font-size: 0.8rem;">{{ $course->instructor->full_name ?? 'CabForm' }}</span>
                                    </div>
                                    <div class="text-cb-muted" style="font-size: 0.8rem;">
                                        <i class="fas fa-clock me-1"></i>{{ $course->duration_hours }}h
                                    </div> -->
                                </div>
                                <a href="{{ url('/course/' . ($course->slug ?? '#')) }}" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100 mt-3">
                                    <i class="fas fa-arrow-right me-1"></i>Voir la formation
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Demo cards when no courses exist -->
                    @for($i = 0; $i < 3; $i++)
                    <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: {{ $i * 0.1 }}s;">
                        <div class="card-cabform h-100">
                            <div class="card-img-wrapper">
                                <div style="height: 200px; background: var(--cb-gradient-primary); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-graduation-cap" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                                <span class="badge-price">{{ ['25 000 FCFA', 'Gratuit', '50 000 FCFA'][$i] }}</span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge-cabform badge-primary">{{ ['Informatique', 'Management', 'Finance'][$i] }}</span>
                                    <span class="badge-cabform" style="background: rgba(var(--cb-accent-rgb),0.1); color: var(--cb-accent);">{{ ['Débutant', 'Intermédiaire', 'Avancé'][$i] }}</span>
                                </div>
                                <h5 class="card-title">{{ ['Développement Web Full-Stack', 'Leadership & Management', 'Analyse Financière Avancée'][$i] }}</h5>
                                <p class="card-text flex-grow-1">{{ ['Maîtrisez HTML, CSS, JavaScript et les frameworks modernes pour créer des applications web complètes.', 'Développez vos compétences en leadership et apprenez à gérer des équipes performantes.', 'Approfondissez vos connaissances en analyse financière et prise de décision stratégique.'][$i] }}</p>
                                <div class="d-flex align-items-center justify-content-between mt-3 pt-3" style="border-top: 1px solid var(--cb-glass-border);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width:30px;height:30px;font-size:0.65rem;">{{ ['AK', 'MD', 'FK'][$i] }}</div>
                                        <span class="text-cb-muted" style="font-size: 0.8rem;">{{ ['Dr. Konan A.', 'M. Diallo', 'F. Kouamé'][$i] }}</span>
                                    </div>
                                    <div class="text-cb-muted" style="font-size: 0.8rem;">
                                        <i class="fas fa-clock me-1"></i>{{ [40, 24, 32][$i] }}h
                                    </div>
                                </div>
                                <a href="{{ url('/catalog') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100 mt-3">
                                    <i class="fas fa-arrow-right me-1"></i>Voir la formation
                                </a>
                            </div>
                        </div>
                    </div>
                    @endfor
                @endforelse
            </div>

            <div class="text-center mt-5 fade-in">
                <a href="{{ url('/catalog') }}" class="btn btn-cabform btn-cabform-outline btn-cabform-lg">
                    Voir toutes les formations <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════ POURQUOI NOUS ════════════════════════ -->
    <section class="section-darker">
        <div class="container">
            <div class="text-center mb-5 fade-in">
                <span class="section-subtitle">Nos avantages</span>
                <h2 class="section-title">Pourquoi choisir <span class="text-gradient">CabForm</span> ?</h2>
            </div>

            <div class="row g-4">
                @php
                    $features = [
                        ['icon' => 'fa-certificate', 'title' => 'Certifications Reconnues', 'desc' => 'Obtenez des certificats vérifiables par QR code, reconnus par les entreprises et institutions.', 'color' => 'primary'],
                        ['icon' => 'fa-chalkboard-teacher', 'title' => 'Formateurs Experts', 'desc' => 'Apprenez auprès de professionnels expérimentés dans leurs domaines respectifs.', 'color' => 'success'],
                        ['icon' => 'fa-laptop-code', 'title' => 'Apprentissage Flexible', 'desc' => 'Suivez vos formations à votre rythme, où que vous soyez, sur tous vos appareils.', 'color' => 'warning'],
                        ['icon' => 'fa-mobile-alt', 'title' => 'Paiement Mobile', 'desc' => 'Payez facilement via Orange Money, MTN Money, Moov Money, Wave ou carte bancaire.', 'color' => 'danger'],
                        ['icon' => 'fa-chart-line', 'title' => 'Suivi de Progression', 'desc' => 'Visualisez votre avancement en temps réel avec des tableaux de bord détaillés.', 'color' => 'primary'],
                        ['icon' => 'fa-headset', 'title' => 'Support Dédié', 'desc' => 'Bénéficiez d\'un accompagnement personnalisé tout au long de votre parcours.', 'color' => 'success'],
                    ];
                @endphp

                @foreach($features as $i => $feature)
                    <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: {{ $i * 0.08 }}s;">
                        <div class="dashboard-card h-100 text-center">
                            <div class="card-icon {{ $feature['color'] }} mx-auto" style="width: 64px; height: 64px; font-size: 1.5rem;">
                                <i class="fas {{ $feature['icon'] }}"></i>
                            </div>
                            <h5 class="mt-3">{{ $feature['title'] }}</h5>
                            <p class="card-text">{{ $feature['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════════════ PROCESSUS ════════════════════════════ -->
    <section class="section-dark">
        <div class="container">
            <div class="text-center mb-5 fade-in">
                <span class="section-subtitle">Comment ça marche</span>
                <h2 class="section-title">Votre parcours en <span class="text-gradient">4 étapes</span></h2>
            </div>

            <div class="row g-4">
                @php
                    $steps = [
                        ['num' => '01', 'icon' => 'fa-search', 'title' => 'Explorez', 'desc' => 'Parcourez notre catalogue de formations et trouvez celle qui correspond à vos objectifs.'],
                        ['num' => '02', 'icon' => 'fa-user-plus', 'title' => 'Inscrivez-vous', 'desc' => 'Créez votre compte et inscrivez-vous à la formation de votre choix.'],
                        ['num' => '03', 'icon' => 'fa-play-circle', 'title' => 'Apprenez', 'desc' => 'Suivez les modules, réalisez les exercices et passez les évaluations.'],
                        ['num' => '04', 'icon' => 'fa-award', 'title' => 'Certifiez-vous', 'desc' => 'Obtenez votre certificat vérifiable et ajoutez-le à votre CV.'],
                    ];
                @endphp

                @foreach($steps as $i => $step)
                    <div class="col-lg-3 col-md-6 fade-in" style="transition-delay: {{ $i * 0.12 }}s;">
                        <div class="text-center position-relative">
                            <div class="mb-3">
                                <span class="fw-900 text-gradient" style="font-size: 3rem; opacity: 0.3;">{{ $step['num'] }}</span>
                            </div>
                            <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(var(--cb-primary-rgb), 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; border: 2px solid rgba(var(--cb-primary-rgb), 0.2);">
                                <i class="fas {{ $step['icon'] }} text-cb-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>{{ $step['title'] }}</h5>
                            <p class="card-text">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════════════ CTA ══════════════════════════════════ -->
    <section style="padding: 100px 0; position: relative; overflow: hidden;">
        <div style="position: absolute; inset: 0; background: var(--cb-gradient-primary); opacity: 0.08;"></div>
        <div style="position: absolute; top: -50%; right: -20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(77,107,254,0.15), transparent 70%); border-radius: 50%;"></div>

        <div class="container text-center position-relative">
            <div class="fade-in">
                <h2 class="section-title mb-3">Prêt à développer vos <span class="text-gradient">compétences</span> ?</h2>
                <p class="section-description mx-auto">Rejoignez des milliers d'apprenants et commencez votre parcours de formation certifiante dès aujourd'hui.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                    <a href="{{ url('/register') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-lg pulse-glow">
                        <i class="fas fa-rocket me-2"></i>Commencer gratuitement
                    </a>
                    <a href="{{ url('/contact') }}" class="btn btn-cabform btn-cabform-glass btn-cabform-lg">
                        <i class="fas fa-envelope me-2"></i>Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
