@extends('layouts.auth')
@section('title', 'Réinitialiser le mot de passe')
@section('content')
<div class="auth-card">
    <div class="auth-logo"><a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a></div>
    <h1 class="auth-title" style="font-size: 1.5rem;">Nouveau mot de passe</h1>
    <p class="auth-subtitle">Choisissez un nouveau mot de passe sécurisé.</p>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-3">
            <label for="email" class="form-label-cabform">Adresse e-mail</label>
            <input type="email" id="email" name="email" class="form-control form-control-cabform @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" required>
            @error('email')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label-cabform">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" class="form-control form-control-cabform @error('password') is-invalid @enderror" required>
            @error('password')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label-cabform">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-cabform" required>
        </div>
        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg"><i class="fas fa-key me-2"></i>Réinitialiser</button>
    </form>
</div>
@endsection
