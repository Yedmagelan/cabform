@extends('layouts.auth')
@section('title', 'Mot de passe oublié')
@section('content')
<div class="auth-card">
    <div class="auth-logo"><a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a></div>
    <h1 class="auth-title" style="font-size: 1.5rem;">Mot de passe oublié ?</h1>
    <p class="auth-subtitle">Entrez votre adresse e-mail et nous vous enverrons un lien de réinitialisation.</p>
    @if(session('status'))<div class="alert border-0 rounded-cb mb-3" style="background: rgba(0,217,126,0.1); color: var(--cb-success); font-size: 0.9rem;"><i class="fas fa-check-circle me-2"></i>{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label-cabform">Adresse e-mail</label>
            <input type="email" id="email" name="email" class="form-control form-control-cabform @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
            @error('email')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg"><i class="fas fa-paper-plane me-2"></i>Envoyer le lien</button>
    </form>
    <div class="text-center mt-3"><a href="{{ route('login') }}" class="text-cb-muted" style="font-size: 0.9rem;"><i class="fas fa-arrow-left me-1"></i>Retour à la connexion</a></div>
</div>
@endsection
