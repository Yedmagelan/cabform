@extends('layouts.instructor')

@section('title', 'Noter la soumission')
@section('page_title', 'Évaluation de Soumission')

@section('content')
<div class="row g-4">
    <!-- Student Submission display -->
    <div class="col-lg-7">
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0"><i class="fas fa-file-alt text-indigo me-2"></i>Travail remis par l'apprenant</h5>
                @if($submission->file_path)
                    <a href="{{ asset('storage/' . $submission->file_path) }}" download class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-download me-1"></i>Télécharger</a>
                @endif
            </div>

            <!-- Content Area -->
            <div class="p-3 bg-dark border border-secondary rounded text-white mb-4" style="min-height: 240px;">
                @if($submission->content)
                    <div class="rich-text-content">
                        {!! $submission->content !!}
                    </div>
                @endif

                @if($submission->file_path)
                    @php
                        $ext = strtolower(pathinfo($submission->file_name, PATHINFO_EXTENSION));
                    @endphp
                    @if($ext === 'pdf')
                        <div class="ratio ratio-16x9 rounded overflow-hidden mt-3" style="min-height: 480px;">
                            <iframe src="{{ asset('storage/' . $submission->file_path) }}" frameborder="0"></iframe>
                        </div>
                    @elseif(in_array($ext, ['png', 'jpg', 'jpeg', 'webp']))
                        <div class="text-center mt-3">
                            <img src="{{ asset('storage/' . $submission->file_path) }}" class="img-fluid rounded" style="max-height: 450px;">
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-file-archive d-block mb-3" style="font-size: 2.5rem;"></i>
                            Le format de fichier (<strong>.{{ $ext }}</strong>) ne peut pas être prévisualisé directement. Veuillez le télécharger pour correction.
                        </div>
                    @endif
                @endif
            </div>

            <!-- Past attempts history accordion -->
            @if($previousSubmissions->count() > 0)
                <div class="mt-4">
                    <h6 class="fw-bold text-white mb-3">Tentatives antérieures de l'apprenant</h6>
                    <div class="accordion" id="historyAccordion">
                        @foreach($previousSubmissions as $index => $history)
                            <div class="accordion-item bg-dark border-secondary">
                                <h2 class="accordion-header">
                                    <button class="accordion-button bg-dark text-white collapsed border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory-{{ $history->id }}">
                                        Tentative #{{ $index + 1 }} &bull; Soumis {{ $history->submitted_at->format('d/m/Y') }} &bull; Score : {{ $history->score ?? 'Non noté' }}
                                    </button>
                                </h2>
                                <div id="collapseHistory-{{ $history->id }}" class="accordion-collapse collapse" data-bs-parent="#historyAccordion">
                                    <div class="accordion-body text-muted">
                                        <p>{{ $history->content }}</p>
                                        @if($history->feedback)
                                            <div class="p-3 bg-secondary rounded text-white mt-2" style="background: rgba(255,255,255,0.05) !important;">
                                                <strong>Feedback formateur :</strong> {{ $history->feedback }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Grading sidebar -->
    <div class="col-lg-5">
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-gavel text-indigo me-2"></i>Évaluation & Note</h5>

            <!-- Meta info banner -->
            <div class="p-3 bg-dark border border-secondary rounded mb-4 text-white" style="font-size: 0.85rem;">
                <div class="row g-2">
                    <div class="col-6"><strong>Apprenant :</strong> {{ $submission->user->full_name }}</div>
                    <div class="col-6 text-end"><strong>Remis le :</strong> {{ $submission->submitted_at->format('d/m/Y H:i') }}</div>
                    <div class="col-12 mt-2">
                        @if($isLate)
                            <span class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i>Remis en retard de : {{ $lateDuration }}</span>
                        @else
                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Remis dans les temps</span>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('instructor.submissions.grade', [$course->id, $assignment->id, $submission->id]) }}" method="POST">
                @csrf
                
                @if(!empty($assignment->rubric))
                    <h6 class="fw-bold text-white mb-3">Notation par critère</h6>
                    
                    @foreach($assignment->rubric as $criterion)
                        @php
                            $cId = $criterion['id'] ?? $criterion['title'];
                            $currentGrading = $submission->rubric_grades[$cId] ?? [];
                        @endphp
                        <div class="mb-4 p-3 bg-dark border border-secondary rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-bold text-white mb-0">{{ $criterion['title'] }}</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" name="rubric_grades[{{ $cId }}][score]" class="form-control bg-dark border-secondary text-indigo text-center fw-bold py-1 criterion-score-input" style="width: 70px;" min="0" max="{{ $criterion['max_points'] }}" value="{{ $currentGrading['score'] ?? '' }}" required>
                                    <span class="text-muted">/ {{ $criterion['max_points'] }} pts</span>
                                </div>
                            </div>
                            <span class="text-muted d-block mb-2" style="font-size: 0.75rem;">{{ $criterion['description'] }}</span>
                            <textarea name="rubric_grades[{{ $cId }}][comment]" class="form-control bg-dark border-secondary text-white py-1" rows="1" placeholder="Commentaire sur ce critère...">{{ $currentGrading['comment'] ?? '' }}</textarea>
                        </div>
                    @endforeach
                @else
                    <div class="mb-3">
                        <label class="form-label text-white">Note globale (sur {{ $assignment->max_score }})</label>
                        <input type="number" name="score" class="form-control bg-dark border-secondary text-white" value="{{ $submission->score }}" min="0" max="{{ $assignment->max_score }}" required>
                    </div>
                @endif

                <!-- Total Score Summary -->
                <div class="p-3 bg-dark border border-secondary rounded mb-4 d-flex justify-content-between align-items-center text-white">
                    <span class="fw-bold">Note Totale calculée :</span>
                    <div>
                        <span class="fw-bold text-indigo" id="calculated-total-score" style="font-size: 1.25rem;">{{ $submission->score ?? 0 }}</span>
                        <span class="text-muted">/ {{ $assignment->max_score }} pts</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white">Commentaires généraux / Feedback global</label>
                    <textarea name="feedback" class="form-control bg-dark border-secondary text-white" rows="4" placeholder="Félicitations pour ce travail, attention toutefois aux détails du programme..." required>{{ $submission->feedback }}</textarea>
                </div>

                <div class="d-grid gap-2">
                    <input type="hidden" name="status" id="grading-status" value="graded">
                    <button type="submit" onclick="document.getElementById('grading-status').value = 'graded'" class="btn btn-premium py-2"><i class="fas fa-check-circle me-2"></i>Valider & Publier la note</button>
                    <button type="submit" onclick="document.getElementById('grading-status').value = 'returned'" class="btn btn-outline-warning py-2"><i class="fas fa-undo me-2"></i>Renvoyer pour correction (Brouillon)</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const scoreInputs = document.querySelectorAll('.criterion-score-input');
        const calculatedTotal = document.getElementById('calculated-total-score');

        function sumRubricScores() {
            let total = 0;
            scoreInputs.forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                }
            });
            calculatedTotal.textContent = total;
        }

        scoreInputs.forEach(input => {
            input.addEventListener('input', sumRubricScores);
        });
    });
</script>
@endpush
