@extends('layouts.auth')
@section('title', 'Double Authentification')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a>
    </div>
    <h1 class="auth-title">Double Authentification</h1>
    <p class="auth-subtitle">Un code de vérification temporaire a été généré pour sécuriser votre connexion.</p>

    @if(session('error'))
        <div class="alert border-0 rounded-cb mb-3" style="background: rgba(230,55,87,0.1); color: var(--cb-danger); font-size: 0.9rem;">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="alert border-0 rounded-cb mb-4" style="background: rgba(77,107,254,0.1); color: var(--cb-accent); font-size: 0.85rem;">
        <i class="fas fa-info-circle me-2"></i>Pour des raisons de simulation de sécurité, veuillez consulter les journaux de l'application (<code>storage/logs/laravel.log</code>) pour récupérer le code de vérification.
    </div>

    <form method="POST" action="{{ route('two-factor.store') }}">
        @csrf
        <div class="mb-4">
            <label for="code" class="form-label-cabform">Code de vérification (6 chiffres)</label>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-key"></i></span>
                <input type="text" id="code" name="code" class="form-control form-control-cabform @error('code') is-invalid @enderror" placeholder="123456" required autofocus autocomplete="off">
            </div>
            @error('code')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
            <i class="fas fa-check-circle me-2"></i>Valider le code
        </button>
    </form>

    <div class="text-center mt-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-cb-muted p-0" style="font-size: 0.9rem; text-decoration: none;">
                Annuler et retourner à la connexion
            </button>
        </form>
    </div>
</div>
@endsection
