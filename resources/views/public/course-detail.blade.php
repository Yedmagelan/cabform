@extends('layouts.app')
@section('title', $course->title)
@section('meta_description', Str::limit(strip_tags($course->description), 160))

@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="row g-5">
            <!-- Main -->
            <div class="col-lg-8 fade-in">
                <nav class="mb-3"><a href="{{ route('catalog') }}" class="text-cb-muted"><i class="fas fa-arrow-left me-1"></i>Retour au catalogue</a></nav>
                <span class="badge-cabform badge-primary mb-2">{{ $course->category->name ?? 'Général' }}</span>
                <span class="badge-cabform" style="background:rgba(var(--cb-accent-rgb),0.1);color:var(--cb-accent);">{{ $course->level_label }}</span>

                <h1 style="font-size: clamp(1.6rem, 4vw, 2.2rem); font-weight: 800; margin: 1rem 0;">{{ $course->title }}</h1>

                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width:36px;height:36px;font-size:0.7rem;">{{ $course->instructor->initials ?? 'CF' }}</div>
                        <span class="text-cb-muted">{{ $course->instructor->full_name ?? 'CabForm' }}</span>
                    </div>
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($course->rating) ? '' : 'empty' }}"></i>
                        @endfor
                        <span class="text-cb-muted ms-1">{{ number_format($course->rating, 1) }} ({{ $course->rating_count }} avis)</span>
                    </div>
                    <span class="text-cb-muted"><i class="fas fa-users me-1"></i>{{ $course->enrollment_count }} inscrits</span>
                    <span class="text-cb-muted"><i class="fas fa-clock me-1"></i>{{ $course->duration_hours }}h</span>
                </div>

                <!-- Thumbnail -->
                <div class="card-cabform mb-4 overflow-hidden" style="border-radius: var(--cb-border-radius-xl);">
                    <div style="height: 350px; background: linear-gradient(135deg, rgba(5,0,216,0.15), rgba(0,0,15,0.8)); display: flex; align-items: center; justify-content: center;">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $course->title }}">
                        @else
                            <i class="fas fa-graduation-cap" style="font-size: 5rem; opacity: 0.3; color: var(--cb-primary-light);"></i>
                        @endif
                    </div>
                </div>

                <!-- Tabs -->
                <div class="card-cabform p-4 mb-4">
                    <ul class="nav nav-tabs-cabform mb-4" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#program">Programme</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#instructor-tab">Formateur</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews-tab">Avis</button></li>
                    </ul>
                    <div class="tab-content">
                        <!-- Description -->
                        <div class="tab-pane fade show active" id="description">
                            <div class="text-cb-secondary" style="line-height: 1.9;">{{ $course->description }}</div>
                            @if($course->objectives)
                                <h5 class="fw-700 mt-4 mb-3"><i class="fas fa-bullseye text-cb-primary me-2"></i>Objectifs</h5>
                                <ul class="list-unstyled">
                                    @foreach(explode("\n", $course->objectives) as $obj)
                                        @if(trim($obj))
                                            <li class="d-flex align-items-start gap-2 mb-2"><i class="fas fa-check-circle text-cb-success mt-1" style="font-size:0.8rem;"></i><span class="text-cb-secondary">{{ trim($obj) }}</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            @if($course->prerequisites)
                                <h5 class="fw-700 mt-4 mb-3"><i class="fas fa-list-check text-cb-warning me-2"></i>Prérequis</h5>
                                <p class="text-cb-secondary">{{ $course->prerequisites }}</p>
                            @endif
                        </div>

                        <!-- Programme -->
                        <div class="tab-pane fade" id="program">
                            <div class="accordion accordion-cabform">
                                @foreach($course->modules as $i => $module)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" data-bs-toggle="collapse" data-bs-target="#mod-{{ $module->id }}">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <span class="badge-cabform badge-primary" style="min-width:30px;text-align:center;">{{ $i + 1 }}</span>
                                                    <div class="flex-grow-1"><strong>{{ $module->title }}</strong><br><span class="text-cb-muted" style="font-size:0.8rem;">{{ $module->lessons->count() }} leçons — {{ $module->duration_minutes ?? 0 }} min</span></div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="mod-{{ $module->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}">
                                            <div class="accordion-body p-0">
                                                @foreach($module->lessons as $lesson)
                                                    <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--cb-glass-border) !important;">
                                                        <i class="fas fa-{{ $lesson->type === 'video' ? 'play-circle text-cb-primary' : ($lesson->type === 'pdf' ? 'file-pdf text-cb-danger' : 'file-alt text-cb-muted') }}"></i>
                                                        <div class="flex-grow-1">
                                                            <span class="text-cb-secondary" style="font-size:0.9rem;">{{ $lesson->title }}</span>
                                                            @if($lesson->is_free_preview) <span class="badge-cabform badge-success" style="font-size:0.65rem;">Aperçu gratuit</span> @endif
                                                        </div>
                                                        <span class="text-cb-muted" style="font-size:0.8rem;">{{ $lesson->duration_minutes }} min</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Formateur -->
                        <div class="tab-pane fade" id="instructor-tab">
                            <div class="d-flex gap-4">
                                <div class="user-avatar" style="width:80px;height:80px;font-size:1.8rem;">{{ $course->instructor->initials ?? 'CF' }}</div>
                                <div>
                                    <h5 class="fw-700 mb-1">{{ $course->instructor->full_name ?? 'CabForm' }}</h5>
                                    <p class="text-cb-muted">{{ $course->instructor->profile->bio ?? 'Formateur professionnel certifié CabForm.' }}</p>
                                    <div class="text-cb-muted" style="font-size:0.85rem;"><i class="fas fa-book me-1"></i>{{ $course->instructor->courses->count() ?? 0 }} formations</div>
                                </div>
                            </div>
                        </div>

                        <!-- Avis -->
                        <div class="tab-pane fade" id="reviews-tab">
                            @forelse($course->reviews as $review)
                                <div class="dashboard-card mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2"><div class="user-avatar" style="width:32px;height:32px;font-size:0.7rem;">{{ $review->user->initials ?? 'U' }}</div><span class="fw-600">{{ $review->user->full_name ?? 'Utilisateur' }}</span></div>
                                        <div class="rating-stars" style="font-size:0.7rem;">@for($j=1;$j<=5;$j++)<i class="fas fa-star {{ $j <= $review->rating ? '' : 'empty' }}"></i>@endfor</div>
                                    </div>
                                    <p class="text-cb-muted mb-0" style="font-size:0.9rem;">{{ $review->comment }}</p>
                                </div>
                            @empty
                                <p class="text-cb-muted text-center py-3">Aucun avis pour le moment.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card-cabform p-4 sticky-top fade-in" style="top: 100px;">
                    <div class="text-center mb-4">
                        @if($course->is_free)
                            <div class="fw-900 text-gradient" style="font-size: 2.5rem;">Gratuit</div>
                        @else
                            <div class="fw-900 text-gradient" style="font-size: 2.5rem;">{{ number_format($course->price, 0, ',', ' ') }} <span style="font-size: 1rem;">FCFA</span></div>
                        @endif
                    </div>

                    @auth
                        @if(auth()->user()->enrolledIn($course))
                            <a href="{{ route('learner.course.player', $course->slug) }}" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
                                <i class="fas fa-play me-2"></i>Continuer la formation
                            </a>
                        @elseif($course->is_free)
                            <form method="POST" action="{{ route('learner.course.enroll-free', $course->slug) }}">
                                @csrf
                                <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
                                    <i class="fas fa-unlock me-2"></i>S'inscrire gratuitement
                                </button>
                            </form>
                        @else
                            <a href="{{ route('checkout', $course->slug) }}" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
                                <i class="fas fa-shopping-cart me-2"></i>Acheter cette formation
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire pour commencer
                        </a>
                    @endauth

                    <hr style="border-color: var(--cb-glass-border);">

                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--cb-glass-border) !important;"><span class="text-cb-muted"><i class="fas fa-layer-group me-2"></i>Modules</span><span class="fw-600">{{ $course->modules->count() }}</span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--cb-glass-border) !important;"><span class="text-cb-muted"><i class="fas fa-book me-2"></i>Leçons</span><span class="fw-600">{{ $course->modules->sum(fn($m) => $m->lessons->count()) }}</span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--cb-glass-border) !important;"><span class="text-cb-muted"><i class="fas fa-clock me-2"></i>Durée</span><span class="fw-600">{{ $course->duration_hours }}h</span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--cb-glass-border) !important;"><span class="text-cb-muted"><i class="fas fa-signal me-2"></i>Niveau</span><span class="fw-600">{{ $course->level_label }}</span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--cb-glass-border) !important;"><span class="text-cb-muted"><i class="fas fa-award me-2"></i>Certificat</span><span class="fw-600">{{ $course->is_certified ? 'Oui' : 'Non' }}</span></li>
                        <li class="d-flex justify-content-between py-2"><span class="text-cb-muted"><i class="fas fa-infinity me-2"></i>Accès</span><span class="fw-600">1 an</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
