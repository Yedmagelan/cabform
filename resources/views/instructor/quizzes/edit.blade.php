@extends('layouts.instructor')

@section('title', 'Édition du Quiz')
@section('page_title', 'Configuration du Quiz')

@section('content')
<div class="row g-4">
    <!-- Quiz Settings -->
    <div class="col-lg-4">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-cog text-indigo me-2"></i>Paramètres du Quiz</h5>
            
            <form action="{{ route('instructor.quiz.update', [$course->id, $quiz->id]) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label text-white">Titre du Quiz</label>
                    <input type="text" name="title" class="form-control bg-dark border-secondary text-white py-2" value="{{ $quiz->title }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Module</label>
                    <select name="module_id" class="form-select bg-dark border-secondary text-white">
                        <option value="">-- Aucun --</option>
                        @foreach($modules as $m)
                            <option value="{{ $m->id }}" {{ $quiz->module_id == $m->id ? 'selected' : '' }}>{{ $m->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Type</label>
                    <select name="type" class="form-select bg-dark border-secondary text-white">
                        <option value="quiz" {{ $quiz->type === 'quiz' ? 'selected' : '' }}>Quiz standard</option>
                        <option value="exam" {{ $quiz->type === 'exam' ? 'selected' : '' }}>Examen final</option>
                        <option value="practice" {{ $quiz->type === 'practice' ? 'selected' : '' }}>Entraînement</option>
                        <option value="survey" {{ $quiz->type === 'survey' ? 'selected' : '' }}>Sondage</option>
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label text-white">Seuil de réussite (%)</label>
                        <input type="number" name="passing_score" class="form-control bg-dark border-secondary text-white" value="{{ $quiz->passing_score }}" min="1" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-white">Limite (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control bg-dark border-secondary text-white" value="{{ $quiz->duration_minutes }}" placeholder="Illimitée" min="1">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Tentatives max.</label>
                    <input type="number" name="max_attempts" class="form-control bg-dark border-secondary text-white" value="{{ $quiz->max_attempts }}" min="1">
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_questions" id="sh-q" value="1" {{ $quiz->shuffle_questions ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="sh-q">Mélanger les questions</label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_answers" id="sh-a" value="1" {{ $quiz->shuffle_answers ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="sh-a">Mélanger les réponses</label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_published" id="pub-q" value="1" {{ $quiz->is_published ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="pub-q">Publier le Quiz</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-2"><i class="fas fa-save me-2"></i>Enregistrer</button>
            </form>
        </div>
    </div>

    <!-- Questions Bank -->
    <div class="col-lg-8">
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-white mb-0">Banque de Questions</h5>
                    <span class="text-indigo" style="font-size: 0.85rem; font-weight: 500;">Total : {{ $quiz->total_points }} points</span>
                </div>
                <button type="button" class="btn btn-premium btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal"><i class="fas fa-plus me-2"></i>Ajouter une question</button>
            </div>

            <!-- Questions list -->
            <div id="questions-list-container">
                @forelse($quiz->questions as $question)
                    <div class="p-3 mb-3 bg-dark border border-secondary rounded d-flex justify-content-between align-items-start question-item" data-id="{{ $question->id }}">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-grip-vertical text-muted cursor-move handle mt-1"></i>
                            <div>
                                <span class="badge bg-indigo-subtle text-indigo mb-2" style="background: rgba(99,102,241,0.15); color: #818cf8;">{{ strtoupper($question->type) }} &bull; {{ $question->points }} pts</span>
                                <div class="text-white fw-bold">{{ $question->question_text }}</div>
                                @if($question->explanation)
                                    <small class="text-muted d-block mt-1">Explication : {{ $question->explanation }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <form action="{{ route('instructor.questions.duplicate', [$quiz->id, $question->id]) }}" method="POST">@csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-copy"></i></button>
                            </form>
                            <form action="{{ route('instructor.questions.delete', [$quiz->id, $question->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette question ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-danger text-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-question-circle d-block mb-3" style="font-size: 2.5rem;"></i>
                        Aucune question n'a été ajoutée pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL AJOUT QUESTION (DYNAMIQUE)
     ========================================================================= -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Ajouter une question</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('instructor.questions.store', $quiz->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Type de Question</label>
                            <select name="type" id="question-type-select" class="form-select bg-dark border-secondary text-white" required>
                                <option value="mcq">Choix Multiple (QCM)</option>
                                <option value="true_false">Vrai / Faux</option>
                                <option value="short_answer">Réponse courte</option>
                                <option value="matching">Appariement (Matching)</option>
                                <option value="open_ended">Question ouverte (Correction manuelle)</option>
                                <option value="fill_blank">Remplissage de trous</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Points</label>
                            <input type="number" name="points" class="form-control bg-dark border-secondary text-white" value="1" min="0.5" step="0.5" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Intitulé de la question</label>
                        <textarea name="question_text" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="Ex: Quelle est la capitale de la France ?" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Explication de la réponse (Optionnel)</label>
                        <input type="text" name="explanation" class="form-control bg-dark border-secondary text-white" placeholder="Sera affiché à l'apprenant après soumission">
                    </div>

                    <hr class="border-secondary my-4">

                    <!-- Section Réponses Dynamiques -->
                    <div id="dynamic-answers-area">
                        <h6 class="fw-bold mb-3">Réponses possibles</h6>
                        
                        <!-- Conteneur pour QCM par défaut -->
                        <div id="answers-container">
                            <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                                <input type="text" name="answers[0][text]" class="form-control bg-dark border-secondary text-white" placeholder="Option de réponse 1" required>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answers[0][is_correct]" value="1" id="correct-0">
                                    <label class="form-check-label text-muted" for="correct-0">Correct</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                                <input type="text" name="answers[1][text]" class="form-control bg-dark border-secondary text-white" placeholder="Option de réponse 2" required>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answers[1][is_correct]" value="1" id="correct-1">
                                    <label class="form-check-label text-muted" for="correct-1">Correct</label>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btn-add-answer" class="btn btn-sm btn-outline-secondary text-white border-secondary mt-2"><i class="fas fa-plus me-1"></i>Ajouter une option</button>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Enregistrer la question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Init Sortable list of questions
        if (document.getElementById('questions-list-container')) {
            new Sortable(document.getElementById('questions-list-container'), {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    const order = [];
                    $('#questions-list-container .question-item').each(function() {
                        order.push($(this).data('id'));
                    });
                    $.ajax({
                        url: `/instructor/quizzes/{{ $quiz->id }}/questions/reorder`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        }
                    });
                }
            });
        }

        // Handle Dynamic answers according to selected question type
        $('#question-type-select').on('change', function() {
            const val = $(this).val();
            const $area = $('#dynamic-answers-area');
            
            if (val === 'mcq') {
                $area.show().html(`
                    <h6 class="fw-bold mb-3">Réponses possibles</h6>
                    <div id="answers-container">
                        <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                            <input type="text" name="answers[0][text]" class="form-control bg-dark border-secondary text-white" placeholder="Option de réponse 1" required>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="answers[0][is_correct]" value="1" id="correct-0">
                                <label class="form-check-label text-muted" for="correct-0">Correct</label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                            <input type="text" name="answers[1][text]" class="form-control bg-dark border-secondary text-white" placeholder="Option de réponse 2" required>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="answers[1][is_correct]" value="1" id="correct-1">
                                <label class="form-check-label text-muted" for="correct-1">Correct</label>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="btn-add-answer" class="btn btn-sm btn-outline-secondary text-white border-secondary mt-2"><i class="fas fa-plus me-1"></i>Ajouter une option</button>
                `);
            } else if (val === 'true_false') {
                $area.show().html(`
                    <h6 class="fw-bold mb-3">Sélectionnez la réponse correcte</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="answers[0][is_correct]" value="1" id="tf-true" checked>
                        <input type="hidden" name="answers[0][text]" value="Vrai">
                        <label class="form-check-label text-white" for="tf-true">Vrai</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[0][is_correct]" value="0" id="tf-false">
                        <input type="hidden" name="answers[1][text]" value="Faux">
                        <input type="hidden" name="answers[1][is_correct]" value="1"> <!-- S'il n'est pas Vrai, c'est Faux -->
                        <label class="form-check-label text-white" for="tf-false">Faux</label>
                    </div>
                `);
            } else if (val === 'short_answer') {
                $area.show().html(`
                    <h6 class="fw-bold mb-3">Mots-clés acceptés (insensible à la casse)</h6>
                    <div id="answers-container">
                        <div class="mb-2 answer-row">
                            <input type="text" name="answers[0][text]" class="form-control bg-dark border-secondary text-white" placeholder="Ex: Paris" required>
                            <input type="hidden" name="answers[0][is_correct]" value="1">
                        </div>
                    </div>
                    <button type="button" id="btn-add-keyword" class="btn btn-sm btn-outline-secondary text-white border-secondary mt-2"><i class="fas fa-plus me-1"></i>Ajouter un synonyme</button>
                `);
            } else if (val === 'matching') {
                $area.show().html(`
                    <h6 class="fw-bold mb-3">Paires d'appariement (Élément gauche -> Élément droit)</h6>
                    <div id="answers-container">
                        <div class="row g-2 mb-2 answer-row">
                            <div class="col-6">
                                <input type="text" name="answers[0][left]" class="form-control bg-dark border-secondary text-white" placeholder="Élément gauche (ex: PHP)" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="answers[0][right]" class="form-control bg-dark border-secondary text-white" placeholder="Élément droit (ex: Laravel)" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="btn-add-pair" class="btn btn-sm btn-outline-secondary text-white border-secondary mt-2"><i class="fas fa-plus me-1"></i>Ajouter une paire</button>
                `);
            } else if (val === 'open_ended') {
                $area.hide(); // Pas de choix de réponse requis pour correction manuelle
            } else if (val === 'fill_blank') {
                $area.show().html(`
                    <h6 class="fw-bold mb-3">Réponses des trous (dans l'ordre d'apparition)</h6>
                    <div class="alert alert-secondary bg-dark border-secondary mb-2" style="font-size: 0.8rem;">
                        Note : Utilisez les crochets dans l'intitulé ci-dessus pour marquer les trous, ex: "Le ciel est [bleu]". Saisissez ci-dessous les mots attendus.
                    </div>
                    <div id="answers-container">
                        <div class="mb-2 answer-row">
                            <input type="text" name="answers[0][text]" class="form-control bg-dark border-secondary text-white" placeholder="Ex: bleu" required>
                            <input type="hidden" name="answers[0][is_correct]" value="1">
                        </div>
                    </div>
                    <button type="button" id="btn-add-blank" class="btn btn-sm btn-outline-secondary text-white border-secondary mt-2"><i class="fas fa-plus me-1"></i>Ajouter un trou</button>
                `);
            }
        });

        // Add answer triggers
        $(document).on('click', '#btn-add-answer', function() {
            const count = $('#answers-container .answer-row').length;
            $('#answers-container').append(`
                <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                    <input type="text" name="answers[${count}][text]" class="form-control bg-dark border-secondary text-white" placeholder="Option de réponse ${count + 1}" required>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="answers[${count}][is_correct]" value="1" id="correct-${count}">
                        <label class="form-check-label text-muted" for="correct-${count}">Correct</label>
                    </div>
                    <button type="button" class="btn btn-sm text-danger remove-answer-btn"><i class="fas fa-times"></i></button>
                </div>
            `);
        });

        $(document).on('click', '#btn-add-keyword, #btn-add-blank', function() {
            const count = $('#answers-container .answer-row').length;
            $('#answers-container').append(`
                <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                    <input type="text" name="answers[${count}][text]" class="form-control bg-dark border-secondary text-white" placeholder="Valeur attendue" required>
                    <input type="hidden" name="answers[${count}][is_correct]" value="1">
                    <button type="button" class="btn btn-sm text-danger remove-answer-btn"><i class="fas fa-times"></i></button>
                </div>
            `);
        });

        $(document).on('click', '#btn-add-pair', function() {
            const count = $('#answers-container .answer-row').length;
            $('#answers-container').append(`
                <div class="row g-2 mb-2 answer-row">
                    <div class="col-5">
                        <input type="text" name="answers[${count}][left]" class="form-control bg-dark border-secondary text-white" placeholder="Élément gauche" required>
                    </div>
                    <div class="col-5">
                        <input type="text" name="answers[${count}][right]" class="form-control bg-dark border-secondary text-white" placeholder="Élément droit" required>
                    </div>
                    <div class="col-2 d-flex align-items-center justify-content-center">
                        <button type="button" class="btn btn-sm text-danger remove-answer-btn"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `);
        });

        $(document).on('click', '.remove-answer-btn', function() {
            $(this).closest('.answer-row').remove();
        });
    });
</script>
@endpush
