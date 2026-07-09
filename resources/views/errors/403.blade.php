@extends('layouts.app')
@section('title', 'Accès refusé - 403')
@section('content')
<div class="container text-center" style="padding: 120px 0;">
    <h1 style="font-size: 8rem; color: var(--cb-danger); font-weight: 900;">403</h1>
    <h2 class="mb-4">Accès refusé</h2>
    <p class="text-muted mb-5" style="max-width: 500px; margin: 0 auto;">
        Vous n'avez pas les permissions nécessaires pour accéder à cette page.
    </p>
    <a href="{{ url('/') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-lg">
        <i class="fas fa-home me-2"></i>Retour à l'accueil
    </a>
</div>
@endsection
