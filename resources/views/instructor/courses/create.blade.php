@extends('layouts.instructor')

@section('title', 'Créer une formation')
@section('page_title', 'Créer une formation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <!-- Progress Steps -->
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between text-center position-relative">
                <div class="position-absolute start-0 end-0 top-50 translate-middle-y bg-secondary" style="height: 2px; z-index: 1;"></div>
                <div class="position-absolute start-0 bg-primary" id="wizard-progress-bar" style="height: 2px; z-index: 2; width: 0%; transition: width 0.3s ease;"></div>
                
                <div class="wizard-step active" style="z-index: 3;">
                    <div class="rounded-circle bg-indigo text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 32px; height: 32px; font-weight: bold;">1</div>
                    <span class="text-white" style="font-size: 0.85rem;">Infos Générales</span>
                </div>
                <div class="wizard-step text-muted" style="z-index: 3;">
                    <div class="rounded-circle bg-dark border border-secondary text-muted d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 32px; height: 32px;">2</div>
                    <span style="font-size: 0.85rem;">Objectifs & Prérequis</span>
                </div>
                <div class="wizard-step text-muted" style="z-index: 3;">
                    <div class="rounded-circle bg-dark border border-secondary text-muted d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 32px; height: 32px;">3</div>
                    <span style="font-size: 0.85rem;">Tarification</span>
                </div>
                <div class="wizard-step text-muted" style="z-index: 3;">
                    <div class="rounded-circle bg-dark border border-secondary text-muted d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 32px; height: 32px;">4</div>
                    <span style="font-size: 0.85rem;">Structure & Publication</span>
                </div>
            </div>
        </div>

        <!-- Wizard form step 1 -->
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-info-circle me-2 text-indigo"></i>Étape 1 : Informations Générales</h5>
            
            <form id="create-course-form" action="{{ route('instructor.courses.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-white fw-500">Titre de la formation</label>
                    <input type="text" name="title" class="form-control bg-dark border-secondary text-white py-2" placeholder="Ex: Apprendre Laravel en 10 jours" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white fw-500">Sous-titre / Description courte</label>
                    <input type="text" name="subtitle" class="form-control bg-dark border-secondary text-white py-2" placeholder="Ex: Maîtrisez le framework PHP le plus populaire">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white fw-500">Catégorie parente</label>
                        <select name="parent_category_id" id="parent-category-select" class="form-select bg-dark border-secondary text-white py-2" required>
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white fw-500">Sous-catégorie</label>
                        <select name="subcategory_id" id="subcategory-select" class="form-select bg-dark border-secondary text-white py-2" disabled>
                            <option value="">Choisissez d'abord une catégorie parente</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white fw-500">Niveau d'expertise</label>
                        <div class="d-flex gap-3 py-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="level" value="debutant" id="lvl-deb" checked>
                                <label class="form-check-label text-muted" for="lvl-deb">Débutant</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="level" value="intermediaire" id="lvl-int">
                                <label class="form-check-label text-muted" for="lvl-int">Intermédiaire</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="level" value="avance" id="lvl-av">
                                <label class="form-check-label text-muted" for="lvl-av">Avancé</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white fw-500">Langue d'enseignement</label>
                        <select name="language" class="form-select bg-dark border-secondary text-white py-2" required>
                            <option value="fr" selected>Français</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-500">Description longue</label>
                    <textarea name="description" class="form-control bg-dark border-secondary text-white py-2" rows="6" placeholder="Présentez en détail les enjeux et le programme de votre formation..." required></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 0.85rem;"><i class="fas fa-save me-1"></i> Sauvegarde automatique activée</span>
                    <button type="submit" class="btn btn-premium px-4 py-2">Continuer vers l'édition détaillée <i class="fas fa-arrow-right ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Cascading Category Dropdown
        $('#parent-category-select').on('change', function() {
            const parentId = $(this).val();
            const $subSelect = $('#subcategory-select');
            
            if (parentId) {
                $subSelect.prop('disabled', true).html('<option value="">Chargement...</option>');
                
                $.get(`/instructor/categories/${parentId}/subcategories`, function(data) {
                    $subSelect.prop('disabled', false).html('<option value="">Sélectionnez une sous-catégorie</option>');
                    if (data && data.length > 0) {
                        data.forEach(sub => {
                            $subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                        });
                    } else {
                        $subSelect.html('<option value="">Aucune sous-catégorie disponible</option>');
                    }
                });
            } else {
                $subSelect.prop('disabled', true).html('<option value="">Choisissez d\'abord une catégorie parente</option>');
            }
        });

        // Submit form via Ajax
        $('#create-course-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Création du brouillon...');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success && response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('Continuer vers l\'édition détaillée <i class="fas fa-arrow-right ms-2"></i>');
                    alert('Une erreur est survenue lors de la création de la formation. Veuillez vérifier les champs requis.');
                }
            });
        });
    });
</script>
@endpush
