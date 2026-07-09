@extends('layouts.instructor')

@section('title', 'Modifier la formation')
@section('page_title', 'Édition de Formation')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .nav-tabs-premium { border-bottom: 2px solid var(--cb-glass-border); }
    .nav-tabs-premium .nav-link { color: var(--cb-text-secondary); border: none; border-bottom: 2px solid transparent; font-weight: 500; font-size: 0.95rem; padding: 12px 20px; transition: all 0.2s; }
    .nav-tabs-premium .nav-link:hover { color: var(--cb-primary); background-color: var(--cb-glass-bg-hover); }
    .nav-tabs-premium .nav-link.active { color: var(--cb-primary); border-bottom: 2px solid var(--cb-primary); background: transparent; }
    
    .module-item { background: var(--cb-dark-card); border: 1px solid var(--cb-glass-border); border-radius: 8px; margin-bottom: 12px; transition: all 0.2s; color: var(--cb-text-primary); }
    .module-header { padding: 16px 20px; cursor: move; display: flex; align-items: center; justify-content: space-between; }
    .lesson-item { background: var(--cb-dark-secondary); border: 1px solid var(--cb-glass-border); border-radius: 6px; padding: 12px 16px; margin-top: 8px; cursor: move; display: flex; align-items: center; justify-content: space-between; color: var(--cb-text-primary); }
    .list-group-item-dark { background: var(--cb-dark-card); border-color: var(--cb-glass-border); color: var(--cb-text-primary); }
</style>
@endpush

@section('content')
<!-- Header Status Banner -->
<div class="card card-instructor p-3 mb-4 d-flex flex-row justify-content-between align-items-center flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted">Statut :</span>
        <span class="badge badge-{{ $course->status }} px-3 py-2" style="font-size: 0.85rem;">
            {{ match($course->status) {
                'draft' => 'Brouillon',
                'pending_review' => 'En révision',
                'published' => 'Publié',
                'archived' => 'Archivé',
                default => $course->status
            } }}
        </span>
        @if($course->status === 'draft' && isset($course->meta_data['rejection_reason']))
            <div class="text-danger" style="font-size: 0.85rem;">
                <i class="fas fa-info-circle me-1"></i> Rejeté : {{ $course->meta_data['rejection_reason'] }}
            </div>
        @endif
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('instructor.courses.publish', $course->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-premium"><i class="fas fa-paper-plane me-2"></i>Soumettre / Publier</button>
        </form>
    </div>
</div>

<div class="card card-instructor p-0 overflow-hidden">
    <!-- Tabs Nav -->
    <ul class="nav nav-tabs nav-tabs-premium" id="courseEditTabs" role="tablist">
        <li class="nav-item"><a class="nav-link {{ request()->tab === 'structure' ? '' : 'active' }}" id="details-tab" data-bs-toggle="tab" href="#tab-details" role="tab">Détails généraux</a></li>
        <li class="nav-item"><a class="nav-link" id="objectives-tab" data-bs-toggle="tab" href="#tab-objectives" role="tab">Objectifs & Prérequis</a></li>
        <li class="nav-item"><a class="nav-link" id="pricing-tab" data-bs-toggle="tab" href="#tab-pricing" role="tab">Tarification</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->tab === 'structure' ? 'active' : '' }}" id="structure-tab" data-bs-toggle="tab" href="#tab-structure" role="tab">Structure (Modules)</a></li>
        <li class="nav-item"><a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#tab-settings" role="tab">Paramètres avancés</a></li>
    </ul>

    <div class="tab-content p-4" id="courseEditTabsContent">
        <!-- 1. DETAILS -->
        <div class="tab-pane fade {{ request()->tab === 'structure' ? '' : 'show active' }}" id="tab-details" role="tabpanel">
            <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label text-white">Titre de la formation</label>
                            <input type="text" name="title" class="form-control bg-dark border-secondary text-white py-2" value="{{ $course->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Sous-titre / Accroche</label>
                            <input type="text" name="subtitle" class="form-control bg-dark border-secondary text-white py-2" value="{{ $course->subtitle }}">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Catégorie</label>
                                <select name="parent_category_id" id="edit-parent-category-select" class="form-select bg-dark border-secondary text-white py-2">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $selectedRootId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Sous-catégorie</label>
                                <select name="subcategory_id" id="edit-subcategory-select" class="form-select bg-dark border-secondary text-white py-2">
                                    <option value="">-- Sans sous-catégorie --</option>
                                    @foreach($subcategories as $sub)
                                        <option value="{{ $sub->id }}" {{ $selectedSubId == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Description longue</label>
                            <textarea name="description" id="editor-description" class="form-control bg-dark border-secondary text-white py-2" rows="8">{{ $course->description }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label text-white">Image de couverture</label>
                            <div class="mb-3">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="img-fluid rounded mb-2 w-100" style="max-height: 200px; object-fit: cover;">
                                @endif
                                <input type="file" name="thumbnail" class="form-control bg-dark border-secondary text-white">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Niveau</label>
                            <select name="level" class="form-select bg-dark border-secondary text-white">
                                <option value="debutant" {{ $course->level === 'debutant' ? 'selected' : '' }}>Débutant</option>
                                <option value="intermediaire" {{ $course->level === 'intermediaire' ? 'selected' : '' }}>Intermédiaire</option>
                                <option value="avance" {{ $course->level === 'avance' ? 'selected' : '' }}>Avancé</option>
                                <option value="expert" {{ $course->level === 'expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Langue d'enseignement</label>
                            <select name="language" class="form-select bg-dark border-secondary text-white">
                                <option value="fr" {{ $course->language === 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ $course->language === 'en' ? 'selected' : '' }}>Anglais</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-premium"><i class="fas fa-save me-2"></i>Enregistrer les modifications</button>
                </div>
            </form>
        </div>

        <!-- 2. OBJECTIVES & PREREQUISITES -->
        <div class="tab-pane fade" id="tab-objectives" role="tabpanel">
            <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="form-label text-white fw-bold">Objectifs d'apprentissage</label>
                    <div class="text-muted mb-2" style="font-size: 0.85rem;">Définissez ce que vos apprenants sauront faire à la fin de la formation. Réorganisez la liste par drag & drop.</div>
                    
                    <div id="objectives-list" class="mb-3">
                        @php
                            $objectives = explode("\n", $course->objectives ?? '');
                        @endphp
                        @foreach($objectives as $objective)
                            @if(trim($objective))
                                <div class="d-flex align-items-center gap-2 mb-2 objective-item bg-dark p-2 rounded">
                                    <i class="fas fa-grip-vertical text-muted cursor-move handle"></i>
                                    <input type="text" name="objectives_array[]" class="form-control bg-transparent border-0 text-white" value="{{ trim($objective) }}">
                                    <button type="button" class="btn btn-sm btn-link text-danger remove-objective-btn"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <button type="button" id="add-objective-btn" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-plus me-1"></i>Ajouter un objectif</button>
                </div>

                <hr class="border-secondary my-4">

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-white fw-bold">Prérequis de formation</label>
                        <select name="prerequisites_array[]" class="form-select select2-prereqs bg-dark text-white" multiple style="width: 100%;">
                            @foreach($allCourses as $c)
                                <option value="{{ $c->id }}" {{ Str::contains($course->prerequisites, $c->id) ? 'selected' : '' }}>{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white fw-bold">Public cible</label>
                        <textarea name="target_audience" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="Ex: Développeurs web juniors, designers reconvertis...">{{ $course->target_audience }}</textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-premium"><i class="fas fa-save me-2"></i>Sauvegarder</button>
                </div>
            </form>
        </div>

        <!-- 3. PRICING -->
        <div class="tab-pane fade" id="tab-pricing" role="tabpanel">
            <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label text-white fw-bold">Type de tarification</label>
                            <div class="d-flex gap-4 py-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_free" value="1" id="price-free" {{ $course->is_free ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="price-free">Gratuit</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_free" value="0" id="price-paid" {{ !$course->is_free ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="price-paid">Payant</label>
                                </div>
                            </div>
                        </div>

                        <div id="price-fields-group" style="display: {{ $course->is_free ? 'none' : 'block' }};">
                            <div class="mb-3">
                                <label class="form-label text-white">Prix (FCFA)</label>
                                <input type="number" name="price" class="form-control bg-dark border-secondary text-white" value="{{ $course->price }}" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white">Prix réduit (Optionnel, FCFA)</label>
                                <input type="number" name="sale_price" class="form-control bg-dark border-secondary text-white" value="{{ $course->sale_price }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label text-white fw-bold">Codes promo applicables</label>
                            <select name="coupon_ids[]" class="form-select select2-coupons bg-dark text-white" multiple style="width: 100%;">
                                @php
                                    $selectedCoupons = $course->meta_data['coupon_ids'] ?? [];
                                @endphp
                                @foreach($coupons as $coupon)
                                    <option value="{{ $coupon->id }}" {{ in_array($coupon->id, $selectedCoupons) ? 'selected' : '' }}>{{ $coupon->code }} ({{ $coupon->discount_percentage }}%)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-premium"><i class="fas fa-save me-2"></i>Enregistrer le tarif</button>
                </div>
            </form>
        </div>

        <!-- 4. STRUCTURE -->
        <div class="tab-pane fade {{ request()->tab === 'structure' ? 'show active' : '' }}" id="tab-structure" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">Programme de formation</h5>
                <button type="button" class="btn btn-premium" data-bs-toggle="modal" data-bs-target="#addModuleModal"><i class="fas fa-plus me-2"></i>Ajouter un module</button>
            </div>

            <!-- Modules Tree Drag and Drop -->
            <div id="modules-drag-container">
                @forelse($course->modules as $module)
                    <div class="module-item" data-id="{{ $module->id }}">
                        <div class="module-header bg-dark rounded-top">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-grip-vertical text-muted cursor-move handle"></i>
                                <span class="fw-bold text-white">{{ $module->title }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">({{ $module->lessons->count() }} leçons)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-white border-secondary add-lesson-btn" data-module-id="{{ $module->id }}" data-bs-toggle="modal" data-bs-target="#addLessonModal"><i class="fas fa-plus me-1"></i>Leçon</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary text-white border-secondary add-quiz-btn" data-module-id="{{ $module->id }}" data-bs-toggle="modal" data-bs-target="#addQuizModal"><i class="fas fa-plus me-1"></i>Quiz</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary text-white border-secondary add-assignment-btn" data-module-id="{{ $module->id }}" data-bs-toggle="modal" data-bs-target="#addAssignmentModal"><i class="fas fa-plus me-1"></i>Devoir</button>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-white p-1" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                                        <li>
                                            <form action="{{ route('instructor.modules.duplicate', [$course->id, $module->id]) }}" method="POST">@csrf
                                                <button type="submit" class="dropdown-item text-white"><i class="fas fa-copy me-2"></i>Dupliquer</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('instructor.modules.delete', [$course->id, $module->id]) }}" method="POST" onsubmit="return confirm('Voulez-vous supprimer ce module et tout son contenu ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Supprimer</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Lessons List Inside Module -->
                        <div class="p-3 lessons-drag-container" data-module-id="{{ $module->id }}">
                            @forelse($module->lessons as $lesson)
                                <div class="lesson-item" data-id="{{ $lesson->id }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-grip-vertical text-muted cursor-move handle"></i>
                                        <i class="fas {{ $lesson->type_icon }} text-indigo"></i>
                                        <span class="text-white">{{ $lesson->title }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('instructor.lessons.edit', [$course->id, $module->id, $lesson->id]) }}" class="btn btn-sm btn-cabform-glass text-white"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('instructor.lessons.preview', [$course->id, $module->id, $lesson->id]) }}" target="_blank" class="btn btn-sm btn-cabform-glass text-white"><i class="fas fa-eye"></i></a>
                                        <form action="{{ route('instructor.lessons.delete', [$course->id, $module->id, $lesson->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette leçon ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-cabform-glass text-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-2" style="font-size: 0.85rem;">Aucun contenu dans ce module.</div>
                            @endforelse

                            <!-- Quizzes Inside Module -->
                            @foreach($module->quizzes as $quiz)
                                <div class="lesson-item bg-dark border-secondary">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-question-circle text-warning"></i>
                                        <span class="text-white"><strong>Quiz:</strong> {{ $quiz->title }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" class="btn btn-sm btn-cabform-glass text-white"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('instructor.quiz.results', [$course->id, $quiz->id]) }}" class="btn btn-sm btn-cabform-glass text-warning"><i class="fas fa-chart-bar"></i></a>
                                        <form action="{{ route('instructor.quiz.delete', [$course->id, $quiz->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce quiz ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-cabform-glass text-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Assignments Inside Module -->
                            @foreach($course->assignments->where('module_id', $module->id) as $assignment)
                                <div class="lesson-item bg-dark border-secondary">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="fas fa-file-signature text-success"></i>
                                        <span class="text-white"><strong>Devoir:</strong> {{ $assignment->title }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('instructor.assignments.edit', [$course->id, $assignment->id]) }}" class="btn btn-sm btn-cabform-glass text-white"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('instructor.submissions.index', [$course->id, $assignment->id]) }}" class="btn btn-sm btn-cabform-glass text-success"><i class="fas fa-check-double"></i></a>
                                        <form action="{{ route('instructor.assignments.delete', [$course->id, $assignment->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce devoir ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-cabform-glass text-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="card bg-dark border-secondary p-5 text-center text-muted">
                        <i class="fas fa-layer-group d-block mb-3" style="font-size: 2rem;"></i>
                        Commencez par ajouter un module à votre formation.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 5. ADVANCED SETTINGS -->
        <div class="tab-pane fade" id="tab-settings" role="tabpanel">
            <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-white mb-3">Progression & Déblocage</h6>
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="sequential_unlock" id="seq-unlock" value="1" {{ $course->sequential_unlock ? 'checked' : '' }}>
                                <label class="form-check-label text-white fw-500" for="seq-unlock">Déblocage séquentiel obligatoire</label>
                                <span class="d-block text-muted" style="font-size: 0.75rem;">L'apprenant doit terminer la leçon précédente pour débloquer la suivante.</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-white mb-3">Délivrance de Certificat</h6>
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_certified" id="is-certified" value="1" {{ $course->is_certified ? 'checked' : '' }}>
                                <label class="form-check-label text-white fw-500" for="is-certified">Activer le certificat automatique</label>
                                <span class="d-block text-muted" style="font-size: 0.75rem;">Remet un certificat lorsque les conditions de complétude sont remplies.</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted" style="font-size: 0.85rem;">Progression minimale requise (%)</label>
                                <input type="number" name="certificate_min_progress" class="form-control bg-dark border-secondary text-white" value="{{ $course->meta_data['certificate_min_progress'] ?? 100 }}" min="50" max="100">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-premium"><i class="fas fa-save me-2"></i>Enregistrer les paramètres</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODALS FOR STRUCTURE TAB
     ========================================================================= -->

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1">
    <div class="modal-dialog bg-dark">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Ajouter un module</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('instructor.modules.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre du module</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optionnel)</label>
                        <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Créer le module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Ajouter une leçon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="add-lesson-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre de la leçon</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type de contenu</label>
                        <select name="type" class="form-select bg-dark border-secondary text-white">
                            <option value="text">Texte riche (WYSIWYG)</option>
                            <option value="video">Vidéo locale (Upload)</option>
                            <option value="pdf">Document PDF</option>
                            <option value="audio">Fichier Audio</option>
                            <option value="slide">Diaporama</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durée estimée (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control bg-dark border-secondary text-white" value="15" min="1" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Suivant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Quiz Modal -->
<div class="modal fade" id="addQuizModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Créer un quiz</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('instructor.quiz.store', $course->id) }}" method="POST">
                @csrf
                <input type="hidden" name="module_id" id="quiz-module-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre du Quiz</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select bg-dark border-secondary text-white">
                            <option value="quiz">Quiz standard</option>
                            <option value="exam">Examen final</option>
                            <option value="practice">Entraînement</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Score de passage (%)</label>
                            <input type="number" name="passing_score" class="form-control bg-dark border-secondary text-white" value="70" min="1" max="100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Limite de temps (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control bg-dark border-secondary text-white" placeholder="Illimitée" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Créer le quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Créer un Devoir</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('instructor.assignments.store', $course->id) }}" method="POST">
                @csrf
                <input type="hidden" name="module_id" id="assignment-module-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre du Devoir</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Sujet</label>
                        <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="4" required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Points max</label>
                            <input type="number" name="max_score" class="form-control bg-dark border-secondary text-white" value="100" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Note de passage</label>
                            <input type="number" name="passing_score" class="form-control bg-dark border-secondary text-white" value="50" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Date limite</label>
                        <input type="text" name="due_date" class="form-control bg-dark border-secondary text-white flatpickr-input">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Taille max fichier (MB)</label>
                        <input type="number" name="max_file_size_mb" class="form-control bg-dark border-secondary text-white" value="10" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre max de tentatives</label>
                        <input type="number" name="max_submissions" class="form-control bg-dark border-secondary text-white" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Créer le devoir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        // Init selects & flatpickr
        $('.select2-prereqs').select2({ placeholder: "Sélectionnez les prérequis" });
        $('.select2-coupons').select2({ placeholder: "Sélectionnez les codes promo" });
        flatpickr(".flatpickr-input", { enableTime: true, dateFormat: "Y-m-d H:i" });

        // Auto toggle pricing visibility
        $('input[name="is_free"]').on('change', function() {
            if ($(this).val() == '0') {
                $('#price-fields-group').slideDown();
            } else {
                $('#price-fields-group').slideUp();
            }
        });

        // Cascading Category Dropdown
        $('#edit-parent-category-select').on('change', function() {
            const parentId = $(this).val();
            const $subSelect = $('#edit-subcategory-select');
            
            if (parentId) {
                $subSelect.html('<option value="">Chargement...</option>');
                $.get(`/instructor/categories/${parentId}/subcategories`, function(data) {
                    $subSelect.html('<option value="">-- Sans sous-catégorie --</option>');
                    data.forEach(sub => {
                        $subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                    });
                });
            }
        });

        // Dynamic learning objectives
        $('#add-objective-btn').on('click', function() {
            $('#objectives-list').append(`
                <div class="d-flex align-items-center gap-2 mb-2 objective-item bg-dark p-2 rounded">
                    <i class="fas fa-grip-vertical text-muted cursor-move handle"></i>
                    <input type="text" name="objectives_array[]" class="form-control bg-transparent border-0 text-white" placeholder="Saisir un objectif..." required>
                    <button type="button" class="btn btn-sm btn-link text-danger remove-objective-btn"><i class="fas fa-times"></i></button>
                </div>
            `);
        });

        $(document).on('click', '.remove-objective-btn', function() {
            $(this).closest('.objective-item').remove();
        });

        // Dynamic Modals Context Binding
        $('.add-lesson-btn').on('click', function() {
            const mId = $(this).data('module-id');
            $('#add-lesson-form').attr('action', `/instructor/courses/{{ $course->id }}/modules/${mId}/lessons`);
        });

        $('.add-quiz-btn').on('click', function() {
            const mId = $(this).data('module-id');
            $('#quiz-module-id').val(mId);
        });

        $('.add-assignment-btn').on('click', function() {
            const mId = $(this).data('module-id');
            $('#assignment-module-id').val(mId);
        });

        // Sortable.js implementation
        if (document.getElementById('modules-drag-container')) {
            new Sortable(document.getElementById('modules-drag-container'), {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    const order = [];
                    $('#modules-drag-container .module-item').each(function() {
                        order.push($(this).data('id'));
                    });
                    $.ajax({
                        url: `/instructor/courses/{{ $course->id }}/modules/reorder`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        }
                    });
                }
            });
        }

        $('.lessons-drag-container').each(function() {
            const mId = $(this).data('module-id');
            new Sortable(this, {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    const order = [];
                    $(`.lessons-drag-container[data-module-id="${mId}"] .lesson-item`).each(function() {
                        order.push($(this).data('id'));
                    });
                    $.ajax({
                        url: `/instructor/courses/{{ $course->id }}/modules/${mId}/lessons/reorder`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
