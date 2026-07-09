@extends('layouts.app')
@section('title', 'Tarification')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="text-center mb-5 fade-in">
            <span class="section-subtitle"><i class="fas fa-tags me-2"></i>Tarification</span>
            <h1 class="section-title">Des tarifs <span class="text-gradient">accessibles</span></h1>
            <p class="section-description">Choisissez la formule qui vous convient. Paiement sécurisé via Mobile Money et carte bancaire.</p>
        </div>
        <div class="row g-4 justify-content-center">
            @php $plans = [
                ['name' => 'Découverte', 'price' => 'Gratuit', 'desc' => 'Commencez gratuitement', 'features' => ['Accès aux formations gratuites', 'Quiz de base', 'Support communautaire', 'Certificat non inclus'], 'btn' => 'outline', 'badge' => ''],
                ['name' => 'Professionnel', 'price' => 'Sur mesure', 'desc' => 'Formation par formation', 'features' => ['Accès à la formation choisie', 'Tous les modules & quiz', 'Certificat vérifiable (QR)', 'Support e-mail prioritaire', 'Téléchargement des ressources'], 'btn' => 'primary', 'badge' => 'Populaire'],
                ['name' => 'Entreprise B2B', 'price' => 'Sur devis', 'desc' => 'Pour vos équipes', 'features' => ['Formations personnalisées', 'Gestion des apprenants', 'Rapports détaillés', 'Support dédié 24/7', 'Facturation entreprise', 'API d\'intégration'], 'btn' => 'outline', 'badge' => ''],
            ]; @endphp
            @foreach($plans as $i => $plan)
            <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: {{ $i * 0.1 }}s;">
                <div class="card-cabform p-4 h-100 text-center {{ $i == 1 ? 'border' : '' }}" style="{{ $i == 1 ? 'border-color: rgba(5,0,216,0.4) !important;' : '' }}">
                    @if($plan['badge'])<div class="mb-3"><span class="badge-cabform badge-primary">{{ $plan['badge'] }}</span></div>@endif
                    <h4 class="fw-800">{{ $plan['name'] }}</h4>
                    <div class="my-3"><span class="fw-900 text-gradient" style="font-size: 2rem;">{{ $plan['price'] }}</span></div>
                    <p class="text-cb-muted mb-4">{{ $plan['desc'] }}</p>
                    <ul class="list-unstyled text-start mb-4">
                        @foreach($plan['features'] as $f)
                        <li class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-check-circle text-cb-success" style="font-size:0.8rem;"></i><span class="text-cb-muted" style="font-size:0.9rem;">{{ $f }}</span></li>
                        @endforeach
                    </ul>
                    <a href="{{ url($i == 2 ? '/contact' : '/catalog') }}" class="btn btn-cabform btn-cabform-{{ $plan['btn'] }} w-100 mt-auto">{{ $i == 2 ? 'Nous contacter' : 'Commencer' }}</a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-5 fade-in">
            <h5 class="fw-700 mb-3"><i class="fas fa-mobile-alt text-cb-primary me-2"></i>Moyens de paiement acceptés</h5>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                @foreach(['Orange Money', 'MTN Money', 'Moov Money', 'Wave', 'Visa', 'Mastercard'] as $m)
                <span class="badge-cabform badge-primary" style="font-size: 0.85rem; padding: 8px 16px;">{{ $m }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
