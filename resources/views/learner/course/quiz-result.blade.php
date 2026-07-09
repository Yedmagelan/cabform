@extends('layouts.learner')

@section('title', 'Résultat du Quiz')
@section('page_title', 'Score de l\'évaluation')

@section('content')
<div class="row g-4">
    <!-- Quiz score card -->
    <div class="col-lg-5">
        <div class="card card-instructor p-4 text-center">
            @if($attempt->passed)
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 80px; height: 80px; background: rgba(16,185,129,0.15) !important;">
                    <i class="fas fa-check-circle fs-1"></i>
                </div>
                <h3 class="fw-bold text-success mb-1">Félicitations ! 🎉</h3>
                <p class="text-muted mb-3">Vous avez validé cette évaluation avec succès.</p>
            @else
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 80px; height: 80px; background: rgba(239,68,68,0.15) !important;">
                    <i class="fas fa-times-circle fs-1"></i>
                </div>
                <h3 class="fw-bold text-danger mb-1">Pas encore réussi</h3>
                <p class="text-muted mb-3">Vous n'avez pas atteint le score de passage minimum.</p>
            @endif

            <div class="my-4" style="font-size: 3.5rem; font-weight: 900;">
                <span class="{{ $attempt->passed ? 'text-indigo' : 'text-danger' }}" style="color: {{ $attempt->passed ? '#818cf8' : '#ef4444' }};">{{ round($attempt->score) }}%</span>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-4">
                    <div class="p-2 bg-dark border border-secondary rounded">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Correctes</span>
                        <span class="fw-bold text-success">{{ $attempt->correct_answers }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 bg-dark border border-secondary rounded">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Incorrectes</span>
                        <span class="fw-bold text-danger">{{ $attempt->total_questions - $attempt->correct_answers }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 bg-dark border border-secondary rounded">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Questions</span>
                        <span class="fw-bold text-white">{{ $attempt->total_questions }}</span>
                    </div>
                </div>
            </div>

            <span class="text-muted d-block mb-4" style="font-size: 0.85rem;">Seuil de réussite : {{ $quiz->passing_score }}%</span>

            <div class="d-grid gap-2">
                <a href="{{ route('learner.course.player', $course->slug) }}" class="btn btn-premium py-2"><i class="fas fa-arrow-left me-2"></i>Retourner au cours</a>
                @if(!$attempt->passed)
                    <a href="{{ route('learner.quiz.show', [$course->slug, $quiz->id]) }}" class="btn btn-outline-secondary border-secondary text-white"><i class="fas fa-redo me-2"></i>Tenter à nouveau</a>
                @endif
            </div>
        </div>

        <!-- Attempts History chart -->
        @php
            $attemptsHistory = auth()->user()->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->orderBy('created_at', 'asc')
                ->get();
        @endphp
        @if($attemptsHistory->count() > 1)
            <div class="card card-instructor p-4 mt-4">
                <h6 class="fw-bold text-white mb-3">Progression des tentatives</h6>
                <div style="height: 180px;">
                    <canvas id="quizProgressChart"></canvas>
                </div>
            </div>
        @endif
    </div>

    <!-- Question review details -->
    <div class="col-lg-7">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4">Correction de vos réponses</h5>

            <div class="accordion accordion-dark" id="quizReviewAccordion">
                @foreach($quiz->questions as $index => $question)
                    @php
                        $selectedAnsId = $attempt->answers_data[$question->id] ?? null;
                        $correctAnswer = $question->answers->firstWhere('is_correct', true);
                        $isCorrect = $selectedAnsId && $correctAnswer && (int)$selectedAnsId === $correctAnswer->id;
                    @endphp
                    <div class="accordion-item bg-dark border-secondary">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQuest-{{ $question->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas {{ $isCorrect ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} fs-5"></i>
                                    <span class="text-white text-start" style="font-size: 0.9rem;">Question {{ $index + 1 }} : {{ Str::limit($question->question_text, 40) }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseQuest-{{ $question->id }}" class="accordion-collapse collapse" data-bs-parent="#quizReviewAccordion">
                            <div class="accordion-body text-white">
                                <p class="fw-bold mb-3">{{ $question->question_text }}</p>

                                <div class="list-group mb-3">
                                    @foreach($question->answers as $ans)
                                        @php
                                            $badgeClass = '';
                                            $borderClass = 'border-secondary';
                                            if ($ans->is_correct) {
                                                $badgeClass = 'bg-success text-white';
                                                $borderClass = 'border-success';
                                            } elseif ($ans->id == $selectedAnsId) {
                                                $badgeClass = 'bg-danger text-white';
                                                $borderClass = 'border-danger';
                                            }
                                        @endphp
                                        <div class="list-group-item list-group-item-dark border {{ $borderClass }} d-flex justify-content-between align-items-center mb-1 rounded">
                                            <span>{{ $ans->answer_text }}</span>
                                            @if($badgeClass)
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $ans->is_correct ? 'Correcte' : 'Sélectionnée' }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if($question->explanation)
                                    <div class="p-3 bg-secondary rounded text-white" style="background: rgba(255,255,255,0.02) !important; font-size: 0.85rem;">
                                        <strong>Explication :</strong> {{ $question->explanation }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('quizProgressChart');
        if (ctx) {
            const data = @json($attemptsHistory->pluck('score'));
            const labels = data.map((_, idx) => 'Essai ' + (idx + 1));

            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Score (%)',
                        data: data,
                        borderColor: '#818cf8',
                        backgroundColor: 'rgba(129, 140, 248, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                        y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' }, min: 0, max: 100 }
                    }
                }
            });
        }
    });
</script>
@endpush
