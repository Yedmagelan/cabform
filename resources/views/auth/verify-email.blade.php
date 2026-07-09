@extends('layouts.auth')
@section('title', 'Vérification e-mail')
@section('content')
<div class="auth-card text-center">
    <div class="auth-logo"><a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a></div>
    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(var(--cb-primary-rgb), 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
        <i class="fas fa-envelope-open text-cb-primary" style="font-size: 2rem;"></i>
    </div>
    <h1 class="auth-title" style="font-size: 1.5rem;">Vérifiez votre e-mail</h1>
    <p class="auth-subtitle">Nous avons envoyé un lien de vérification à votre adresse e-mail. Cliquez sur le lien pour activer votre compte.</p>
    @if(session('status') == 'verification-link-sent')
        <div class="alert border-0 rounded-cb mb-3" style="background: rgba(0,217,126,0.1); color: var(--cb-success); font-size: 0.9rem;">
            <i class="fas fa-check-circle me-2"></i>Un nouveau lien de vérification a été envoyé !
        </div>
    @endif
    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100"><i class="fas fa-paper-plane me-2"></i>Renvoyer le lien</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-cabform btn-cabform-glass w-100"><i class="fas fa-sign-out-alt me-2"></i>Se déconnecter</button>
    </form>
</div>
@endsection
