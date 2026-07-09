@extends('layouts.app')
@section('title', 'Page introuvable - 404')
@section('content')
<div class="container text-center" style="padding: 120px 0;">
    <h1 style="font-size: 8rem; color: var(--cb-primary); font-weight: 900;">404</h1>
    <h2 class="mb-4">Oups ! Page introuvable</h2>
    <p class="text-muted mb-5" style="max-width: 500px; margin: 0 auto;">
        La page que vous recherchez semble avoir été déplacée, supprimée ou n'a peut-être jamais existé.
    </p>
    <a href="{{ url('/') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-lg">
        <i class="fas fa-home me-2"></i>Retour à l'accueil
    </a>
</div>
@endsection
