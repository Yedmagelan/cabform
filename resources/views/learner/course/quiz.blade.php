@extends('layouts.learner')

@section('title', 'Quiz : ' . $quiz->title)
@section('page_title', 'Évaluation')

@push('styles')
<style>
    .question-nav-btn { width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem; transition: all 0.2s; }
    .q-not-visited { background: #334155; color: #94a3b8; border: 1px solid rgba(255,255,255,0.05); }
    .q-visited { background: #475569; color: #f8fafc; border: 1px solid #6366f1; }
    .q-answered { background: #6366f1; color: #ffffff; border: 1px solid #6366f1; }
</style>
@endpush

@section('content')
<!-- Start Screen (Instructions) -->
<div id="quiz-start-screen" class="row justify-content-center">
    <div class="col-lg-8 text-center">
        <div class="card card-instructor p-5 mb-4">
            <i class="fas fa-question-circle text-indigo mb-3" style="font-size: 4rem;"></i>
            <h3 class="fw-bold text-white mb-2">{{ $quiz->title }}</h3>
            <p class="text-muted mb-4">{{ $quiz->description ?? 'Veuillez lire attentivement les consignes ci-dessous.' }}</p>

            <div class="row g-3 mb-4 justify-content-center">
                <div class="col-sm-4">
                    <div class="p-3 bg-dark border border-secondary rounded">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Questions</span>
                        <h5 class="fw-bold text-white mb-0">{{ $quiz->questions->count() }}</h5>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-dark border border-secondary rounded">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Durée limite</span>
                        <h5 class="fw-bold text-white mb-0">{{ $quiz->duration_minutes ? $quiz->duration_minutes . ' min' : 'Illimitée' }}</h5>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-dark border border-secondary rounded">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Note de passage</span>
                        <h5 class="fw-bold text-white mb-0">{{ $quiz->passing_score }}%</h5>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 col-md-6 mx-auto">
                <button type="button" id="btn-start-quiz" class="btn btn-premium py-2"><i class="fas fa-play me-2"></i>Démarrer le quiz</button>
                <a href="{{ route('learner.course.player', $course->slug) }}" class="btn btn-outline-secondary border-secondary text-white">Retour au cours</a>
            </div>
        </div>
    </div>
</div>

<!-- Active Quiz Screen (Hidden by default) -->
<div id="quiz-active-screen" class="row g-4 d-none">
    <!-- Sticky Countdown bar -->
    <div class="col-12 sticky-top" style="z-index: 1000; top: 75px;">
        <div class="card bg-dark border-secondary p-3 d-flex flex-row justify-content-between align-items-center text-white shadow-lg">
            <h6 class="fw-bold mb-0 text-white">{{ $quiz->title }}</h6>
            <div class="d-flex align-items-center gap-3">
                <div class="progress" style="width: 150px; height: 6px; background: rgba(255,255,255,0.05);">
                    <div class="progress-bar bg-indigo" id="quiz-progress-bar" style="width: 0%;"></div>
                </div>
                <span class="fw-bold" id="quiz-progress-text">Q 1/{{ $quiz->questions->count() }}</span>
                @if($quiz->duration_minutes)
                    <div class="badge bg-dark border border-secondary px-3 py-2" id="quiz-timer" data-duration="{{ $quiz->duration_minutes * 60 }}">
                        <i class="fas fa-clock me-2 text-indigo"></i><span id="timer-display">--:--</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Question content area -->
    <div class="col-lg-8">
        <form id="quiz-attempt-form" method="POST" action="{{ route('learner.quiz.submit', [$course->slug, $quiz->id]) }}">
            @csrf
            
            @foreach($quiz->questions as $index => $question)
                <div class="card card-instructor p-4 mb-4 question-slide-panel {{ $index > 0 ? 'd-none' : '' }}" data-question-index="{{ $index }}" data-question-id="{{ $question->id }}">
                    <span class="badge bg-indigo-subtle text-indigo mb-3" style="background: rgba(99,102,241,0.15); color: #818cf8;">Question {{ $index + 1 }} sur {{ $quiz->questions->count() }}</span>
                    <h5 class="fw-bold text-white mb-4">{{ $question->question_text }}</h5>

                    <div class="d-flex flex-column gap-3">
                        @if($question->type === 'mcq' || !$question->type)
                            @foreach($question->answers as $ans)
                                <label class="d-flex align-items-center gap-3 p-3 rounded bg-dark border border-secondary cursor-pointer hover-premium" style="transition: all 0.2s;">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $ans->id }}" class="form-check-input question-answer-input" required>
                                    <span class="text-white">{{ $ans->answer_text }}</span>
                                </label>
                            @endforeach

                        @elseif($question->type === 'true_false')
                            @foreach($question->answers as $ans)
                                <label class="d-flex align-items-center gap-3 p-3 rounded bg-dark border border-secondary cursor-pointer hover-premium" style="transition: all 0.2s;">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $ans->id }}" class="form-check-input question-answer-input" required>
                                    <span class="text-white">{{ $ans->answer_text }}</span>
                                </label>
                            @endforeach

                        @elseif($question->type === 'short_answer')
                            <div class="mb-3">
                                <input type="text" name="answers[{{ $question->id }}]" class="form-control bg-dark border-secondary text-white py-2 question-answer-input" placeholder="Saisissez votre réponse ici..." required>
                            </div>

                        @elseif($question->type === 'open_ended')
                            <div class="mb-3">
                                <textarea name="answers[{{ $question->id }}]" class="form-control bg-dark border-secondary text-white question-answer-input" rows="4" placeholder="Rédigez votre réponse ici..." required></textarea>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Navigation bottom buttons -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <button type="button" id="btn-prev-question" class="btn btn-outline-secondary border-secondary text-white" disabled><i class="fas fa-chevron-left me-2"></i>Précédent</button>
                <button type="button" id="btn-next-question" class="btn btn-premium">Suivant <i class="fas fa-chevron-right ms-2"></i></button>
                <button type="button" id="btn-submit-quiz-trigger" class="btn btn-indigo text-white d-none" style="background: #6366f1;"><i class="fas fa-paper-plane me-2"></i>Terminer le quiz</button>
            </div>
        </form>
    </div>

    <!-- Question map sidebar -->
    <div class="col-lg-4">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-3">Plan du Quiz</h5>
            <p class="text-muted" style="font-size: 0.85rem;">Utilisez la grille ci-dessous pour naviguer rapidement entre les questions.</p>
            
            <div class="d-flex flex-wrap gap-2 mb-4" id="quiz-question-map">
                @foreach($quiz->questions as $index => $question)
                    <button type="button" class="question-nav-btn q-not-visited" data-target-index="{{ $index }}">{{ $index + 1 }}</button>
                @endforeach
            </div>

            <div class="d-grid">
                <button type="button" class="btn btn-outline-danger border-danger text-danger btn-sm" id="btn-abandon-quiz"><i class="fas fa-times me-2"></i>Abandonner le quiz</button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitQuizModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Soumettre le Quiz</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="fas fa-paper-plane text-indigo mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold text-white">Êtes-vous sûr de vouloir soumettre ?</h5>
                <p class="text-muted">Vous avez répondu à l'ensemble des questions. Une fois soumis, vos réponses seront enregistrées pour correction.</p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Continuer</button>
                <button type="button" class="btn btn-premium" id="btn-confirm-submit-quiz">Confirmer la soumission</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentQuestionIndex = 0;
        const totalQuestions = {{ $quiz->questions->count() }};
        let timerInterval = null;

        // Démarrer le quiz
        $('#btn-start-quiz').on('click', function() {
            $('#quiz-start-screen').addClass('d-none');
            $('#quiz-active-screen').removeClass('d-none');
            
            // Highlight the first question in the map
            $(`#quiz-question-map button[data-target-index="0"]`).removeClass('q-not-visited').addClass('q-visited');
            
            // Start timer if exists
            const timerContainer = document.getElementById('quiz-timer');
            if (timerContainer) {
                let timeLeft = parseInt(timerContainer.dataset.duration);
                updateTimerDisplay(timeLeft);
                
                timerInterval = setInterval(function() {
                    timeLeft--;
                    updateTimerDisplay(timeLeft);
                    
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        alert('Le temps limite est écoulé ! Le quiz va être soumis automatiquement.');
                        $('#quiz-attempt-form').submit();
                    }
                }, 1000);
            }
        });

        function updateTimerDisplay(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            const display = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            $('#timer-display').text(display);

            // Change colors based on time
            const $timer = $('#quiz-timer');
            if (seconds < 300) {
                $timer.removeClass('border-secondary').addClass('border-danger text-danger');
            } else if (seconds < 600) {
                $timer.removeClass('border-secondary').addClass('border-warning text-warning');
            }
        }

        // Show Slide panel according to index
        function showQuestion(index) {
            currentQuestionIndex = index;
            $('.question-slide-panel').addClass('d-none');
            $(`.question-slide-panel[data-question-index="${index}"]`).removeClass('d-none');

            // Map highlight
            const $btn = $(`#quiz-question-map button[data-target-index="${index}"]`);
            if (!$btn.hasClass('q-answered')) {
                $btn.removeClass('q-not-visited').addClass('q-visited');
            }

            // Progress text and bar
            $('#quiz-progress-text').text(`Q ${index + 1}/${totalQuestions}`);
            $('#quiz-progress-bar').css('width', ((index + 1) / totalQuestions * 100) + '%');

            // Button states
            $('#btn-prev-question').prop('disabled', index === 0);
            
            if (index === totalQuestions - 1) {
                $('#btn-next-question').addClass('d-none');
                $('#btn-submit-quiz-trigger').removeClass('d-none');
            } else {
                $('#btn-next-question').removeClass('d-none');
                $('#btn-submit-quiz-trigger').addClass('d-none');
            }
        }

        $('#btn-next-question').on('click', function() {
            if (currentQuestionIndex < totalQuestions - 1) {
                showQuestion(currentQuestionIndex + 1);
            }
        });

        $('#btn-prev-question').on('click', function() {
            if (currentQuestionIndex > 0) {
                showQuestion(currentQuestionIndex - 1);
            }
        });

        // Question Map Click Navigation
        $(document).on('click', '#quiz-question-map button', function() {
            const idx = $(this).data('target-index');
            showQuestion(idx);
        });

        // Mark as answered when option selected
        $(document).on('change', '.question-answer-input', function() {
            const idx = $(this).closest('.question-slide-panel').data('question-index');
            $(`#quiz-question-map button[data-target-index="${idx}"]`).removeClass('q-visited q-not-visited').addClass('q-answered');
        });

        // Modal triggers
        $('#btn-submit-quiz-trigger').on('click', function() {
            const myModal = new bootstrap.Modal(document.getElementById('submitQuizModal'));
            myModal.show();
        });

        $('#btn-confirm-submit-quiz').on('click', function() {
            $('#quiz-attempt-form').submit();
        });

        // Abandon quiz
        $('#btn-abandon-quiz').on('click', function() {
            if (confirm('Êtes-vous sûr de vouloir abandonner ? Vos réponses ne seront pas enregistrées.')) {
                window.location.href = `{{ route('learner.course.player', $course->slug) }}`;
            }
        });
    });
</script>
@endpush
