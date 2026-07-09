@extends('layouts.app')

@section('title', 'Compte en attente d\'activation - CabForm')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <!-- Main Glass Card -->
            <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-radius: 16px;">
                <div class="card-body p-5 text-center">
                    
                    <!-- Decorative Pending Icon -->
                    <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px; background: rgba(5, 0, 216, 0.08); border-radius: 50%;">
                        <i class="fas fa-hourglass-half text-cb-primary fa-2x animate-pulse"></i>
                    </div>

                    <h2 class="fw-bold mb-3" style="color: var(--cb-text-primary);">Inscription enregistrée !</h2>
                    
                    <p class="text-cb-muted mb-4" style="font-size: 1.05rem; line-height: 1.6;">
                        Bonjour <strong>{{ $user->full_name }}</strong>, votre compte a été créé avec succès. 
                        Pour accéder à votre espace de formation et commencer vos cours, veuillez finaliser le paiement de votre formation.
                    </p>

                    @if($course)
                        <!-- Selected Course Card -->
                        <div class="text-start p-4 mb-4" style="background: rgba(5, 0, 216, 0.03); border: 1px solid rgba(5, 0, 216, 0.06); border-radius: 12px;">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <span class="badge bg-cb-primary-light text-cb-primary mb-2" style="font-weight: 600; font-size: 0.75rem;">Formation sélectionnée</span>
                                    <h5 class="fw-bold mb-1" style="color: var(--cb-text-primary);">{{ $course->title }}</h5>
                                    <p class="text-cb-muted mb-0 small"><i class="fas fa-chalkboard-teacher me-1"></i> Formateur : {{ $course->instructor->full_name }}</p>
                                </div>
                                <div class="col-4 text-end">
                                    <span class="fs-4 fw-bold text-cb-primary">{{ number_format($course->price, 0, ',', ' ') }} F CFA</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row g-3 justify-content-center mb-4">
                        @if($course)
                            <!-- Online Payment Option -->
                            <div class="col-sm-6">
                                <a href="{{ route('checkout', $course->slug) }}" class="btn btn-cabform btn-cabform-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px !important;">
                                    <i class="fas fa-credit-card"></i> Payer en ligne (CinetPay)
                                </a>
                            </div>
                        @endif
                        
                        <!-- Logout Option -->
                        <div class="col-sm-6">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-cabform btn-cabform-outline w-100 py-3 d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px !important;">
                                    <i class="fas fa-sign-out-alt"></i> Se déconnecter
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr style="border-color: rgba(5, 0, 216, 0.1);">

                    <!-- Manual/Offline Payment Instructions -->
                    <div class="text-start mt-4">
                        <h6 class="fw-bold mb-3" style="color: var(--cb-text-primary);"><i class="fas fa-info-circle text-cb-primary me-2"></i>Instructions de paiement manuel</h6>
                        <p class="text-cb-muted small mb-3">Si vous préférez payer par transfert d'argent mobile (Wave, Orange, MTN, Moov) :</p>
                        
                        <div class="p-3 bg-light rounded" style="font-size: 0.9rem; border-left: 4px solid var(--cb-primary);">
                            <ul class="list-unstyled mb-0 lh-lg">
                                <li><strong class="text-cb-primary">Wave / Orange Money :</strong> +225 07 00 00 00 00</li>
                                <li><strong class="text-cb-primary">MTN MoMo :</strong> +225 05 00 00 00 00</li>
                                <li><strong class="text-cb-primary">Moov Money :</strong> +225 01 00 00 00 00</li>
                                <li><strong>Référence à indiquer :</strong> Votre email ({{ $user->email }})</li>
                            </ul>
                        </div>
                        
                        <p class="text-cb-muted small mt-3 mb-0 text-center">
                            <em>Une fois le transfert effectué, l'administrateur validera votre inscription sous 2 à 24 heures et vous recevrez un email de confirmation.</em>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
