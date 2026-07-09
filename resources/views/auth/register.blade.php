@extends('layouts.auth')
@section('title', 'Inscription')

@section('content')
<div class="auth-card" style="max-width: 700px;">
    <div class="auth-logo">
        <a href="{{ url('/') }}"><img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm"></a>
    </div>
    <h1 class="auth-title">Créer un compte</h1>
    <p class="auth-subtitle">Rejoignez CabForm et développez vos compétences</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="last_name" class="form-label-cabform">Nom</label>
                <input type="text" id="last_name" name="last_name" class="form-control form-control-cabform @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Votre nom" required>
                @error('last_name')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="first_name" class="form-label-cabform">Prénom</label>
                <input type="text" id="first_name" name="first_name" class="form-control form-control-cabform @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="Votre prénom" required>
                @error('first_name')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label for="email" class="form-label-cabform">Adresse e-mail</label>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-envelope"></i></span>
                <input type="email" id="email" name="email" class="form-control form-control-cabform @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required>
            </div>
            @error('email')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label-cabform">Téléphone <span class="text-cb-muted">(optionnel)</span></label>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-phone"></i></span>
                <input type="text" id="phone" name="phone" class="form-control form-control-cabform" value="{{ old('phone') }}" placeholder="+225 XX XX XX XX">
            </div>
        </div>

        <div class="mb-3">
            <label for="course_id" class="form-label-cabform">Choisir une formation <span class="text-cb-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-graduation-cap"></i></span>
                <select id="course_id" name="course_id" class="form-select form-control-cabform @error('course_id') is-invalid @enderror" required style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-primary);">
                    <option value="" disabled {{ old('course_id') ? '' : 'selected' }}>Sélectionnez une formation...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (old('course_id') == $course->id || request('course') == $course->slug || request('course_id') == $course->id) ? 'selected' : '' }}>
                            {{ $course->title }} ({{ $course->formatted_price }})
                        </option>
                    @endforeach
                </select>
            </div>
            @error('course_id')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label-cabform">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control form-control-cabform @error('password') is-invalid @enderror" placeholder="Minimum 8 caractères" required>
                <button type="button" class="input-group-text password-toggle" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted); cursor: pointer;"><i class="fas fa-eye"></i></button>
            </div>
            @error('password')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label-cabform">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-cabform" placeholder="Confirmer le mot de passe" required>
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="terms" required style="background: var(--cb-glass-bg); border-color: var(--cb-glass-border);">
                <label class="form-check-label text-cb-muted" for="terms" style="font-size: 0.85rem;">
                    J'accepte les <a href="{{ url('/page/cgu') }}">conditions d'utilisation</a> et la <a href="{{ url('/page/confidentialite') }}">politique de confidentialité</a>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-cabform btn-cabform-primary w-100 btn-cabform-lg mb-3">
            <i class="fas fa-user-plus me-2"></i>S'inscrire
        </button>
    </form>

    <div class="text-center mt-3">
        <span class="text-cb-muted" style="font-size: 0.9rem;">Déjà inscrit ?</span>
        <a href="{{ route('login') }}" class="fw-600">Se connecter</a>
    </div>
</div>
@endsection
