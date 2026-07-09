@extends('layouts.learner')
@section('title', 'Mon profil')
@section('page_title', 'Mon Profil')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-cabform p-4 text-center">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->full_name }}" class="rounded-circle mx-auto mb-3" style="width: 90px; height: 90px; object-fit: cover; border: 3px solid var(--cb-primary);">
            @else
                <div class="user-avatar mx-auto mb-3" style="width: 90px; height: 90px; font-size: 2rem;">{{ $user->initials }}</div>
            @endif
            <h5 class="fw-700">{{ $user->full_name }}</h5>
            <p class="text-cb-muted mb-2">{{ $user->email }}</p>
            @if($user->phone)
                <p class="text-cb-muted mb-3" style="font-size: 0.9rem;"><i class="fas fa-phone me-1"></i>{{ $user->phone }}</p>
            @endif
            <span class="badge-cabform badge-primary">Apprenant</span>
            <div class="mt-3 text-cb-muted" style="font-size: 0.8rem;">
                <p class="mb-1"><i class="fas fa-calendar me-1"></i>Inscrit le {{ $user->created_at?->format('d/m/Y') }}</p>
                @if($user->last_login_at)
                    <p class="mb-0"><i class="fas fa-clock me-1"></i>Dernière connexion : {{ $user->last_login_at?->diffForHumans() }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-cb mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-user-edit text-cb-primary me-2"></i>Modifier mon profil</h5>
            <form method="POST" action="{{ route('learner.profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-cabform">Nom</label>
                        <input type="text" name="last_name" class="form-control form-control-cabform @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}">
                        @error('last_name')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-cabform">Prénom</label>
                        <input type="text" name="first_name" class="form-control form-control-cabform @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}">
                        @error('first_name')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-cabform">E-mail</label>
                        <input type="email" class="form-control form-control-cabform" value="{{ $user->email }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-cabform">Téléphone</label>
                        <input type="text" name="phone" class="form-control form-control-cabform @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-cabform">Photo de profil (Avatar)</label>
                        <input type="file" name="avatar" class="form-control form-control-cabform @error('avatar') is-invalid @enderror">
                        @error('avatar')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label-cabform">Biographie</label>
                        <textarea name="bio" class="form-control form-control-cabform" rows="3" placeholder="Décrivez-vous en quelques mots...">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-cabform btn-cabform-primary mt-4">
                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                </button>
            </form>
        </div>

        <div class="card-cabform p-4 mt-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-shield-alt text-cb-success me-2"></i>Double Authentification (2FA)</h5>
            <form method="POST" action="{{ route('learner.profile.2fa') }}">
                @csrf
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="two_factor_enabled" id="twoFactorSwitch" value="1" {{ $user->two_factor_enabled ? 'checked' : '' }}>
                    <label class="form-check-label text-cb-muted" for="twoFactorSwitch" style="font-size: 0.9rem;">
                        Activer la double authentification par code de sécurité e-mail
                    </label>
                </div>
                <button type="submit" class="btn btn-cabform btn-cabform-outline">
                    <i class="fas fa-shield-alt me-2"></i>Mettre à jour la sécurité
                </button>
            </form>
        </div>

        <div class="card-cabform p-4 mt-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-lock text-cb-warning me-2"></i>Changer le mot de passe</h5>
            <form method="POST" action="{{ route('learner.profile.password') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label-cabform">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-control form-control-cabform @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-cabform">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control form-control-cabform @error('password') is-invalid @enderror" required>
                        @error('password')<div class="text-cb-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-cabform">Confirmer</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-cabform" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-cabform btn-cabform-outline mt-4">
                    <i class="fas fa-key me-2"></i>Mettre à jour
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
