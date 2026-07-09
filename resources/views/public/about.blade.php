@extends('layouts.app')
@section('title', 'À propos')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="text-center mb-5 fade-in">
            <span class="section-subtitle"><i class="fas fa-info-circle me-2"></i>Notre histoire</span>
            <h1 class="section-title">À propos de <span class="text-gradient">CabForm</span></h1>
            <p class="section-description">Nous croyons que la formation de qualité doit être accessible à tous.</p>
        </div>
        <div class="row g-5 align-items-center mb-5">
            <div class="col-lg-6 fade-in-left">
                <h2 class="fw-800 mb-3">Notre <span class="text-gradient">Mission</span></h2>
                <p class="text-cb-muted">CabForm est née de la conviction que l'éducation et la formation professionnelle doivent être accessibles, flexibles et certifiantes. Notre plateforme propose des formations de qualité dispensées par des experts reconnus dans leurs domaines.</p>
                <p class="text-cb-muted">Nous accompagnons les apprenants, les entreprises et les institutions dans le développement des compétences à travers des parcours structurés et des certifications vérifiables.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <div class="dashboard-card text-center" style="min-width: 130px;">
                        <div class="fw-800 text-gradient" style="font-size: 1.5rem;">2020</div>
                        <div class="text-cb-muted" style="font-size: 0.8rem;">Année de création</div>
                    </div>
                    <div class="dashboard-card text-center" style="min-width: 130px;">
                        <div class="fw-800 text-gradient" style="font-size: 1.5rem;">5000+</div>
                        <div class="text-cb-muted" style="font-size: 0.8rem;">Apprenants formés</div>
                    </div>
                    <div class="dashboard-card text-center" style="min-width: 130px;">
                        <div class="fw-800 text-gradient" style="font-size: 1.5rem;">98%</div>
                        <div class="text-cb-muted" style="font-size: 0.8rem;">Taux de satisfaction</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 fade-in-right">
                <div class="card-cabform p-5 text-center" style="border: 1px solid rgba(5,0,216,0.2);">
                    <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" height="80" class="mb-4 float-animation">
                    <h4 class="fw-800">Excellence en Formation</h4>
                    <p class="text-cb-muted">Votre partenaire de confiance pour la formation professionnelle et la certification.</p>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-5 fade-in">
            @php $values = [['icon' => 'fa-gem', 'title' => 'Excellence', 'desc' => 'Nous visons l\'excellence dans chaque formation que nous proposons.'], ['icon' => 'fa-handshake', 'title' => 'Accessibilité', 'desc' => 'Nous rendons la formation de qualité accessible à tous.'], ['icon' => 'fa-lightbulb', 'title' => 'Innovation', 'desc' => 'Nous innovons constamment pour offrir la meilleure expérience.']]; @endphp
            @foreach($values as $v)
            <div class="col-md-4">
                <div class="dashboard-card text-center h-100">
                    <div class="card-icon primary mx-auto" style="width:64px;height:64px;font-size:1.5rem;"><i class="fas {{ $v['icon'] }}"></i></div>
                    <h5 class="mt-3">{{ $v['title'] }}</h5>
                    <p class="card-text">{{ $v['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
