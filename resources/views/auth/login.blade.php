@extends('layouts.auth')
@section('title', 'Connexion')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a>
    </div>
    <h1 class="auth-title">Connexion</h1>
    <p class="auth-subtitle">Accédez à votre espace de formation</p>

    @if(session('status'))
        <div class="alert border-0 rounded-cb mb-3" style="background: rgba(0,217,126,0.1); color: var(--cb-success); font-size: 0.9rem;">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label-cabform">Adresse e-mail</label>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control form-control-cabform @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
            </div>
            @error('email')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label-cabform">Mot de passe</label>
                <a href="{{ route('password.request') }}" style="font-size: 0.8rem;">Mot de passe oublié ?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control form-control-cabform @error('password') is-invalid @enderror" placeholder="••••••••" required>
                <button type="button" class="input-group-text password-toggle" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted); cursor: pointer;"><i class="fas fa-eye"></i></button>
            </div>
            @error('password')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" style="background: var(--cb-glass-bg); border-color: var(--cb-glass-border);">
                <label class="form-check-label text-cb-muted" for="remember" style="font-size: 0.9rem;">Se souvenir de moi</label>
            </div>
        </div>

        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
        </button>
    </form>

    <div class="text-center mt-3">
        <span class="text-cb-muted" style="font-size: 0.9rem;">Pas encore de compte ?</span>
        <a href="{{ route('register') }}" class="fw-600">S'inscrire</a>
    </div>
</div>
@endsection
