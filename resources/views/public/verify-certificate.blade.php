@extends('layouts.app')
@section('title', 'Vérifier un certificat')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-5 fade-in">
                    <span class="section-subtitle"><i class="fas fa-shield-alt me-2"></i>Vérification</span>
                    <h1 class="section-title">Vérifier un <span class="text-gradient">Certificat</span></h1>
                    <p class="section-description">Entrez le numéro du certificat ou scannez le QR code pour vérifier son authenticité.</p>
                </div>
                <div class="card-cabform p-4 mb-4 fade-in">
                    <form method="GET" action="{{ route('certificate.verify') }}">
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--cb-glass-bg);border:1px solid var(--cb-glass-border);color:var(--cb-text-muted);"><i class="fas fa-search"></i></span>
                            <input type="text" name="code" class="form-control form-control-cabform" placeholder="Numéro du certificat (ex: CERT-20260704-XXXXXX)" value="{{ request('code') }}" required>
                            <button type="submit" class="btn btn-cabform btn-cabform-primary"><i class="fas fa-check-circle me-1"></i>Vérifier</button>
                        </div>
                    </form>
                </div>
                @if(request('code'))
                    @if(isset($certificate) && $certificate)
                        <div class="card-cabform p-4 fade-in" style="border-left: 4px solid var(--cb-success);">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div style="width:56px;height:56px;border-radius:50%;background:rgba(0,217,126,0.1);display:flex;align-items:center;justify-content:center;"><i class="fas fa-check-circle text-cb-success" style="font-size:1.5rem;"></i></div>
                                <div><h5 class="fw-700 mb-0 text-cb-success">Certificat vérifié ✓</h5><p class="text-cb-muted mb-0" style="font-size:0.85rem;">Ce certificat est authentique et valide.</p></div>
                            </div>
                            <hr style="border-color:var(--cb-glass-border);">
                            <div class="row g-3">
                                <div class="col-md-6"><strong class="text-cb-muted" style="font-size:0.8rem;">Titulaire :</strong><div class="fw-600">{{ $certificate->user->full_name }}</div></div>
                                <div class="col-md-6"><strong class="text-cb-muted" style="font-size:0.8rem;">Formation :</strong><div class="fw-600">{{ $certificate->course->title }}</div></div>
                                <div class="col-md-6"><strong class="text-cb-muted" style="font-size:0.8rem;">Numéro :</strong><div class="fw-600">{{ $certificate->certificate_number }}</div></div>
                                <div class="col-md-6"><strong class="text-cb-muted" style="font-size:0.8rem;">Délivré le :</strong><div class="fw-600">{{ $certificate->issued_at?->format('d/m/Y') }}</div></div>
                                @if($certificate->final_score)<div class="col-md-6"><strong class="text-cb-muted" style="font-size:0.8rem;">Score :</strong><div class="fw-600">{{ $certificate->final_score }}%</div></div>@endif
                            </div>
                        </div>
                    @else
                        <div class="card-cabform p-4 fade-in" style="border-left: 4px solid var(--cb-danger);">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:56px;height:56px;border-radius:50%;background:rgba(230,55,87,0.1);display:flex;align-items:center;justify-content:center;"><i class="fas fa-times-circle text-cb-danger" style="font-size:1.5rem;"></i></div>
                                <div><h5 class="fw-700 mb-0 text-cb-danger">Certificat non trouvé</h5><p class="text-cb-muted mb-0" style="font-size:0.85rem;">Aucun certificat ne correspond au numéro « {{ request('code') }} ».</p></div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
