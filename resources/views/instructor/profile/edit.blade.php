@extends('layouts.instructor')

@section('title', 'Modifier le Profil')
@section('page_title', 'Paramètres de compte')

@section('content')
<div class="row g-4">
    <!-- General Profile Edit -->
    <div class="col-lg-7">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-user-edit text-indigo me-2"></i>Informations Générales</h5>
            
            <form action="{{ route('instructor.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white">Prénom</label>
                        <input type="text" name="first_name" class="form-control bg-dark border-secondary text-white py-2" value="{{ $user->first_name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Nom de famille</label>
                        <input type="text" name="last_name" class="form-control bg-dark border-secondary text-white py-2" value="{{ $user->last_name }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white">Téléphone</label>
                        <input type="text" name="phone" class="form-control bg-dark border-secondary text-white py-2" value="{{ $user->phone }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Langue de l'interface</label>
                        <select name="locale" class="form-select bg-dark border-secondary text-white py-2">
                            <option value="fr" {{ $user->locale === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="en" {{ $user->locale === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Image de profil / Avatar</label>
                    <input type="file" name="avatar" class="form-control bg-dark border-secondary text-white">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Lien LinkedIn</label>
                    <input type="url" name="linkedin_url" class="form-control bg-dark border-secondary text-white py-2" value="{{ $profile->linkedin_url }}" placeholder="https://linkedin.com/in/...">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Site internet / Portfolio</label>
                    <input type="url" name="website_url" class="form-control bg-dark border-secondary text-white py-2" value="{{ $profile->website_url }}" placeholder="https://website.com">
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Domaines d'expertise (Séparés par des virgules)</label>
                    @php
                        $exps = implode(', ', $profile->interests['expertises'] ?? []);
                    @endphp
                    <input type="text" name="expertises_raw" id="expertises-input" class="form-control bg-dark border-secondary text-white py-2" value="{{ $exps }}" placeholder="Laravel, Bootstrap, Agile, DevOps">
                    <div id="expertises-container" class="mt-2"></div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Biographie professionnelle</label>
                    <textarea name="bio" class="form-control bg-dark border-secondary text-white" rows="6" placeholder="Partagez votre parcours et votre méthode pédagogique...">{{ $profile->bio }}</textarea>
                </div>

                <button type="submit" class="btn btn-premium px-4"><i class="fas fa-save me-2"></i>Enregistrer le profil</button>
            </form>
        </div>
    </div>

    <!-- Security & Sessions -->
    <div class="col-lg-5">
        <!-- Change Password -->
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-shield-alt text-indigo me-2"></i>Sécurité du compte</h5>
            
            <form action="{{ route('instructor.profile.security') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-white">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control bg-dark border-secondary text-white" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control bg-dark border-secondary text-white" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Confirmer le mot de passe</label>
                    <input type="password" name="new_password_confirmation" class="form-control bg-dark border-secondary text-white" required>
                </div>
                
                <button type="submit" class="btn btn-outline-secondary text-white border-secondary w-100 py-2">Modifier le mot de passe</button>
            </form>
        </div>

        <!-- 2FA Authenticator Switch -->
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-3"><i class="fas fa-key text-indigo me-2"></i>Double authentification (2FA)</h5>
            <p class="text-muted" style="font-size: 0.85rem;">Protégez votre compte des accès non autorisés en activant la vérification 2FA.</p>
            
            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" id="toggle-2fa-btn" {{ $user->two_factor_enabled ? 'checked' : '' }}>
                <label class="form-check-label text-white fw-500" for="toggle-2fa-btn">Double authentification</label>
                <span class="d-block text-muted" style="font-size: 0.75rem;">Requiert un code temporaire lors de votre connexion.</span>
            </div>
        </div>

        <!-- Disconnect Other Sessions -->
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-3"><i class="fas fa-sign-out-alt text-indigo me-2"></i>Sessions actives</h5>
            <p class="text-muted" style="font-size: 0.85rem;">Si vous suspectez un accès suspect, déconnectez toutes vos autres sessions actives sur d'autres appareils.</p>
            
            <form action="{{ route('instructor.profile.logout-others') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-white">Vérification du mot de passe</label>
                    <input type="password" name="password" class="form-control bg-dark border-secondary text-white" placeholder="Saisir votre mot de passe" required>
                </div>
                <button type="submit" class="btn btn-outline-danger border-danger text-danger w-100 py-2">Déconnecter les autres appareils</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle 2FA switch via Ajax
        $('#toggle-2fa-btn').on('change', function() {
            const checked = this.checked;
            $.ajax({
                url: `/instructor/profile/toggle-2fa`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    enabled: checked
                },
                success: function(response) {
                    alert(response.message);
                }
            });
        });

        // Expertises tags generation on edit
        const $input = $('#expertises-input');
        const $container = $('#expertises-container');
        
        function updateTags() {
            $container.empty();
            const val = $input.val();
            if (val) {
                const tags = val.split(',');
                tags.forEach(tag => {
                    if (tag.trim()) {
                        $container.append(`<span class="badge bg-secondary me-1 py-1 text-white">${tag.trim()}</span>`);
                    }
                });
            }
        }
        
        $input.on('input', updateTags);
        updateTags(); // Initial tags
    });
</script>
@endpush
