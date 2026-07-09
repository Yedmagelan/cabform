@extends('layouts.auth')
@section('title', 'Confirmer le mot de passe')
@section('content')
<div class="auth-card">
    <div class="auth-logo"><a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a></div>
    <h1 class="auth-title" style="font-size: 1.5rem;">Confirmation requise</h1>
    <p class="auth-subtitle">Pour des raisons de sécurité, veuillez confirmer votre mot de passe pour continuer.</p>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <label for="password" class="form-label-cabform">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control form-control-cabform @error('password') is-invalid @enderror" required autofocus>
            @error('password')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg"><i class="fas fa-shield-alt me-2"></i>Confirmer</button>
    </form>
</div>
@endsection
