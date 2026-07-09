@extends('layouts.learner')
@section('title', 'Mes certificats')
@section('page_title', 'Mes Certificats')

@section('content')
<div class="card-cabform p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-700 mb-0"><i class="fas fa-award text-cb-warning me-2"></i>Mes Certificats</h5>
        <a href="{{ route('certificate.verify') }}" class="btn btn-cabform btn-cabform-glass btn-cabform-sm">
            <i class="fas fa-qrcode me-1"></i>Vérifier un certificat
        </a>
    </div>

    @if($certificates->count() > 0)
        <div class="row g-4">
            @foreach($certificates as $cert)
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="d-flex gap-3">
                            <div style="width: 64px; height: 64px; border-radius: var(--cb-border-radius); background: linear-gradient(135deg, rgba(245,166,35,0.15), rgba(245,166,35,0.05)); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-certificate text-cb-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-700 mb-1">{{ $cert->course->title ?? 'Formation' }}</h6>
                                <div class="text-cb-muted mb-1" style="font-size: 0.8rem;">
                                    N° {{ $cert->certificate_number }}
                                </div>
                                <div class="d-flex align-items-center gap-3" style="font-size: 0.78rem;">
                                    <span class="text-cb-muted"><i class="fas fa-calendar me-1"></i>{{ $cert->issued_at?->format('d/m/Y') }}</span>
                                    @if($cert->is_valid)
                                        <span class="badge-cabform badge-success">Valide</span>
                                    @else
                                        <span class="badge-cabform badge-danger">Expiré</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="#" class="btn btn-cabform btn-cabform-primary btn-cabform-sm"><i class="fas fa-download me-1"></i>PDF</a>
                                    <a href="#" class="btn btn-cabform btn-cabform-glass btn-cabform-sm"><i class="fas fa-share-alt me-1"></i>Partager</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-award text-cb-muted" style="font-size: 4rem; opacity: 0.2;"></i>
            <h5 class="text-cb-muted mt-3">Aucun certificat pour le moment</h5>
            <p class="text-cb-muted">Terminez une formation certifiante pour obtenir votre premier certificat.</p>
            <a href="{{ url('/catalog') }}" class="btn btn-cabform btn-cabform-primary">
                <i class="fas fa-search me-1"></i>Trouver une formation certifiante
            </a>
        </div>
    @endif
</div>
@endsection
