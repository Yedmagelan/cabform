@extends('layouts.instructor')

@section('title', 'Détails de l\'Apprenant')
@section('page_title', 'Suivi Individuel')

@section('content')
<div class="row g-4 mb-4">
    <!-- Student Profile Header Card -->
    <div class="col-12">
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="d-flex align-items-center gap-4">
                    <div class="user-avatar" style="width: 64px; height: 64px; font-size: 1.5rem;">{{ $student->initials }}</div>
                    <div>
                        <h4 class="fw-bold text-white mb-1">{{ $student->full_name }}</h4>
                        <span class="text-muted d-block" style="font-size: 0.9rem;"><i class="fas fa-envelope me-2"></i>{{ $student->email }}</span>
                        <span class="text-muted d-block" style="font-size: 0.9rem;"><i class="fas fa-calendar-alt me-2"></i>Inscrit le : {{ $enrollment->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('instructor.students.export-pdf', [$course->id, $student->id]) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-file-pdf me-2"></i>Rapport PDF</a>
                    <a href="{{ route('instructor.students', $course->id) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Navigation tabs sidebar -->
    <div class="col-lg-3">
        <div class="card card-instructor p-3">
            <div class="nav flex-column nav-pills nav-tabs-premium" id="studentTabs" role="tablist" style="border-bottom: none;">
                <button class="nav-link active text-start mb-2" id="prog-tab" data-bs-toggle="pill" data-bs-target="#tab-prog" type="button"><i class="fas fa-tasks me-2"></i>Progression</button>
                <button class="nav-link text-start mb-2" id="quiz-tab" data-bs-toggle="pill" data-bs-target="#tab-quiz" type="button"><i class="fas fa-question-circle me-2"></i>Quiz & Évals</button>
                <button class="nav-link text-start mb-2" id="assign-tab" data-bs-toggle="pill" data-bs-target="#tab-assign" type="button"><i class="fas fa-file-signature me-2"></i>Devoirs</button>
                <button class="nav-link text-start mb-2" id="cert-tab" data-bs-toggle="pill" data-bs-target="#tab-cert" type="button"><i class="fas fa-award me-2"></i>Certificat</button>
                <button class="nav-link text-start mb-2" id="stats-tab" data-bs-toggle="pill" data-bs-target="#tab-stats" type="button"><i class="fas fa-chart-line me-2"></i>Statistiques</button>
            </div>
        </div>
    </div>

    <!-- Tabs Content workspace -->
    <div class="col-lg-9">
        <div class="card card-instructor p-4 min-vh-50">
            <div class="tab-content" id="studentTabsContent">
                
                <!-- 1. PROGRESSION -->
                <div class="tab-pane fade show active" id="tab-prog" role="tabpanel">
                    <h5 class="fw-bold text-white mb-4">Progression globale ({{ round($progress_percentage) }}%)</h5>
                    <div class="progress mb-4" style="height: 10px; background: rgba(255,255,255,0.05);">
                        <div class="progress-bar bg-indigo" style="width: {{ $progress_percentage }}%; background: #6366f1;"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Leçon</th>
                                    <th>Type</th>
                                    <th>Durée</th>
                                    <th>Complétude</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->lessons as $lesson)
                                    @php
                                        // Vérifier le statut de progression de cette leçon
                                        $lessonProgress = $student->submissions()->where('id', $lesson->id)->first(); // Simple check or relation
                                        $status = \App\Models\Progress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $lesson->title }}</td>
                                        <td><i class="fas {{ $lesson->type_icon }} text-muted me-1"></i> {{ strtoupper($lesson->type) }}</td>
                                        <td>{{ $lesson->duration_minutes }} min</td>
                                        <td>
                                            @if($status && $status->status === 'completed')
                                                <span class="badge bg-success-subtle text-success"><i class="fas fa-check-circle me-1"></i> Terminé</span>
                                            @elseif($status && $status->status === 'in_progress')
                                                <span class="badge bg-warning-subtle text-warning"><i class="fas fa-spinner fa-spin me-1"></i> En cours</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-muted">Non démarré</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. QUIZZES -->
                <div class="tab-pane fade" id="tab-quiz" role="tabpanel">
                    <h5 class="fw-bold text-white mb-4">Tentatives de Quiz</h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Quiz / Examen</th>
                                    <th>Score obtenu</th>
                                    <th>Note de passage</th>
                                    <th>Statut</th>
                                    <th>Date de soumission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quiz_attempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->quiz->title }}</td>
                                        <td class="fw-bold text-white">{{ $attempt->score }}%</td>
                                        <td>{{ $attempt->quiz->passing_score }}%</td>
                                        <td>
                                            <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">
                                                {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                                            </span>
                                        </td>
                                        <td>{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Aucune tentative de quiz enregistrée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. ASSIGNMENTS -->
                <div class="tab-pane fade" id="tab-assign" role="tabpanel">
                    <h5 class="fw-bold text-white mb-4">Devoirs remis</h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Devoir</th>
                                    <th>Date de remise</th>
                                    <th>Statut</th>
                                    <th>Note</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $sub)
                                    <tr>
                                        <td>{{ $sub->assignment->title }}</td>
                                        <td>{{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $sub->status === 'graded' ? 'success' : 'warning' }}">
                                                {{ match($sub->status) {
                                                    'submitted' => 'À corriger',
                                                    'graded' => 'Corrigé',
                                                    'returned' => 'Renvoyé',
                                                    default => $sub->status
                                                } }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($sub->score !== null)
                                                <strong>{{ $sub->score }}</strong> / {{ $sub->assignment->max_score }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $sub->feedback ? Str::limit($sub->feedback, 30) : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Aucun devoir remis pour le moment.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. CERTIFICATS -->
                <div class="tab-pane fade" id="tab-cert" role="tabpanel">
                    <h5 class="fw-bold text-white mb-4">Certificat de formation</h5>
                    @if($certificate)
                        <div class="card bg-dark border-secondary p-4 text-center">
                            <i class="fas fa-award text-warning mb-3" style="font-size: 3rem;"></i>
                            <h4 class="fw-bold text-white">Certificat délivré</h4>
                            <p class="text-muted mb-3">Numéro unique : {{ $certificate->certificate_number }}</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ asset('storage/' . $certificate->pdf_path) }}" target="_blank" class="btn btn-premium"><i class="fas fa-eye me-2"></i>Voir le PDF</a>
                                <form action="{{ route('instructor.certificates.revoke', [$course->id, $certificate->id]) }}" method="POST" onsubmit="return confirm('Révoquer ce certificat ?');">
                                    @csrf
                                    <input type="hidden" name="reason" value="Révocation administrative formateur.">
                                    <button type="submit" class="btn btn-outline-danger border-danger text-danger"><i class="fas fa-ban me-2"></i>Révoquer le certificat</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card bg-dark border-secondary p-4 text-center text-muted">
                            <i class="fas fa-award mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-white fw-bold mb-2">Aucun certificat délivré</h5>
                            <p class="mb-4">L'apprenant n'a pas encore rempli toutes les conditions d'obtention automatique (progression, quiz réussis).</p>
                            
                            <form action="{{ route('instructor.certificates.generate', [$course->id, $student->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-premium"><i class="fas fa-plus me-2"></i>Générer manuellement le certificat</button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- 5. STATISTICS -->
                <div class="tab-pane fade" id="tab-stats" role="tabpanel">
                    <h5 class="fw-bold text-white mb-4">Statistiques d'activité</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-dark border border-secondary rounded">
                                <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Temps total passé</span>
                                <h4 class="fw-bold text-white mb-0">{{ $time_spent_minutes }} minutes</h4>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-dark border border-secondary rounded">
                                <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Leçons terminées</span>
                                <h4 class="fw-bold text-white mb-0">{{ $lessons_completed }} / {{ $lessons_total }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
