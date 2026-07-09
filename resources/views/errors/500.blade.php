@extends('layouts.app')
@section('title', 'Erreur serveur - 500')
@section('content')
<div class="container text-center" style="padding: 120px 0;">
    <h1 style="font-size: 8rem; color: var(--cb-warning); font-weight: 900;">500</h1>
    <h2 class="mb-4">Erreur interne du serveur</h2>
    <p class="text-muted mb-5" style="max-width: 500px; margin: 0 auto;">
        Désolé, une erreur inattendue s'est produite de notre côté. Nos équipes ont été alertées. Veuillez réessayer plus tard.
    </p>
    <a href="{{ url('/') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-lg">
        <i class="fas fa-home me-2"></i>Retour à l'accueil
    </a>
</div>
@endsection
