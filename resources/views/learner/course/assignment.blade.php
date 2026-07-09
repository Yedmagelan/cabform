@extends('layouts.learner')

@section('title', $assignment->title)
@section('page_title', 'Devoir')

@section('content')
<div class="row g-4">
    <!-- Assignment Info -->
    <div class="col-lg-8">
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <span class="badge bg-indigo-subtle text-indigo mb-2" style="background: rgba(99,102,241,0.15); color: #818cf8;">Devoir</span>
                    <h4 class="fw-bold text-white mb-1">{{ $assignment->title }}</h4>
                    <span class="text-muted" style="font-size: 0.85rem;">Formation : {{ $course->title }}</span>
                </div>
                <a href="{{ route('learner.course.player', $course->slug) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left me-1"></i>Retour</a>
            </div>

            <!-- Tabs Nav -->
            <ul class="nav nav-tabs nav-tabs-premium mb-4" id="assignmentTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="instructions-tab" data-bs-toggle="tab" href="#tab-instructions" role="tab">Énoncé & Grille</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="submission-tab" data-bs-toggle="tab" href="#tab-submission" role="tab">Ma Soumission</a>
                </li>
                @if($submission && $submission->status === 'graded')
                    <li class="nav-item">
                        <a class="nav-link text-success" id="feedback-tab" data-bs-toggle="tab" href="#tab-feedback" role="tab">Feedback ({{ $submission->score }}/{{ $assignment->max_score }})</a>
                    </li>
                @endif
            </ul>

            <div class="tab-content" id="assignmentTabsContent">
                <!-- 1. INSTRUCTIONS & RUBRIC -->
                <div class="tab-pane fade show active text-white" id="tab-instructions" role="tabpanel">
                    <div class="mb-4">
                        <h6 class="fw-bold text-indigo mb-2">Consignes et Sujet :</h6>
                        <p class="text-muted" style="line-height: 1.6; white-space: pre-wrap;">{{ $assignment->description }}</p>
                    </div>

                    @if($assignment->instructions)
                        <div class="mb-4 p-3 bg-dark border border-secondary rounded">
                            <h6 class="fw-bold text-white mb-2"><i class="fas fa-info-circle me-2"></i>Livrables attendus :</h6>
                            <p class="text-muted mb-0">{{ $assignment->instructions }}</p>
                        </div>
                    @endif

                    <!-- Evaluation Rubrics -->
                    @if(!empty($assignment->rubric))
                        <div class="mb-4">
                            <h6 class="fw-bold text-white mb-3"><i class="fas fa-table text-indigo me-2"></i>Critères d'évaluation (Grille de notation) :</h6>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Critère</th>
                                            <th>Description</th>
                                            <th>Points Max.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignment->rubric as $criterion)
                                            <tr>
                                                <td class="fw-bold text-white">{{ $criterion['title'] }}</td>
                                                <td class="text-muted">{{ $criterion['description'] }}</td>
                                                <td class="text-indigo fw-bold">{{ $criterion['max_points'] }} pts</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 2. MY SUBMISSION FORM -->
                <div class="tab-pane fade" id="tab-submission" role="tabpanel">
                    @if($submission)
                        <div class="alert alert-secondary bg-dark border-secondary text-white p-4">
                            <h6 class="fw-bold mb-2">Vous avez déjà soumis ce devoir.</h6>
                            <p class="text-muted mb-3" style="font-size: 0.85rem;">Date de remise : {{ $submission->submitted_at->format('d/m/Y H:i') }} &bull; Statut : {{ strtoupper($submission->status) }}</p>

                            @if($submission->content)
                                <div class="p-3 bg-secondary rounded mb-3" style="background: rgba(255,255,255,0.02) !important;">
                                    <strong>Votre réponse en ligne :</strong>
                                    <p class="mb-0 text-muted mt-2">{{ $submission->content }}</p>
                                </div>
                            @endif

                            @if($submission->file_path)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fas fa-file-download text-indigo"></i>
                                    <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-white hover-indigo">{{ $submission->file_name }}</a>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Submission Form -->
                        <form action="{{ route('learner.assignment.submit', [$course->slug, $assignment->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-white">Réponse rédigée (Optionnel)</label>
                                <textarea name="content" class="form-control bg-dark border-secondary text-white" rows="5" placeholder="Décrivez votre démarche ou votre réponse ici..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-white">Fichier ou Livrable (PDF, ZIP, Max 20MB)</label>
                                <input type="file" name="file" class="form-control bg-dark border-secondary text-white" accept=".pdf,.zip,.rar,.docx,.pptx">
                            </div>

                            <button type="submit" class="btn btn-premium w-100 py-2">Soumettre mon travail</button>
                        </form>
                    @endif
                </div>

                <!-- 3. FEEDBACK -->
                @if($submission && $submission->status === 'graded')
                    <div class="tab-pane fade" id="tab-feedback" role="tabpanel">
                        <div class="p-4 bg-dark border border-secondary rounded text-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Commentaire du formateur :</h6>
                                <span class="badge bg-success-subtle text-success fs-6">{{ $submission->score }} / {{ $assignment->max_score }} pts</span>
                            </div>
                            <p class="text-muted" style="white-space: pre-wrap;">{{ $submission->feedback ?? 'Aucun commentaire rédigé.' }}</p>
                        </div>

                        <!-- Graded Rubrics -->
                        @if($submission->rubric_grades)
                            <h6 class="fw-bold text-white mb-3">Détails des points :</h6>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Critère</th>
                                            <th>Points</th>
                                            <th>Commentaire</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submission->rubric_grades as $cId => $grade)
                                            @php
                                                $criterion = collect($assignment->rubric)->firstWhere('id', $cId) 
                                                          ?? collect($assignment->rubric)->firstWhere('title', $cId);
                                            @endphp
                                            <tr>
                                                <td class="fw-bold text-white">{{ $criterion['title'] ?? $cId }}</td>
                                                <td><span class="text-indigo fw-bold">{{ $grade['score'] }}</span> / {{ $criterion['max_points'] ?? '' }}</td>
                                                <td class="text-muted">{{ $grade['comment'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Deadlines Widget sidebar -->
    <div class="col-lg-4">
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-clock text-indigo me-2"></i>Date limite</h5>
            
            <div class="p-3 bg-dark border border-secondary rounded text-center mb-3 text-white">
                @if($assignment->due_date)
                    <h5 class="fw-bold text-indigo mb-2">{{ $assignment->due_date->format('d/m/Y') }}</h5>
                    <span class="text-muted" style="font-size: 0.85rem;">Heure limite : {{ $assignment->due_date->format('H:i') }}</span>
                    
                    @php
                        $daysLeft = now()->diffInDays($assignment->due_date, false);
                    @endphp
                    <div class="mt-2 text-{{ $daysLeft < 3 ? 'danger' : ($daysLeft < 7 ? 'warning' : 'success') }} fw-bold" style="font-size: 0.85rem;">
                        @if($daysLeft < 0)
                            Date limite dépassée !
                        @else
                            {{ round($daysLeft) }} jours restants
                        @endif
                    </div>
                @else
                    <span class="text-muted">Aucune date limite</span>
                @endif
            </div>

            <div class="p-3 bg-dark border border-secondary rounded text-white" style="font-size: 0.85rem;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tentatives max :</span>
                    <strong>{{ $assignment->max_submissions }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Taille max :</span>
                    <strong>{{ $assignment->max_file_size_mb }} MB</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
