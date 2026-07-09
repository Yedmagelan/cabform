@extends('layouts.admin')
@section('title', 'Tableau de bord : ' . $course->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Formations</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($course->title, 20) }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-700 mb-1">{{ $course->title }}</h4>
        <span class="text-cb-muted small">Par {{ $course->instructor->full_name ?? '-' }} | Version {{ $course->version }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.report', $course->id) }}" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm">
            <i class="fas fa-file-pdf me-1"></i>Rapport PDF
        </a>
    </div>
</div>

<!-- KPIs Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-cb-muted small fw-600">Inscriptions</span>
                <i class="fas fa-user-graduate text-cb-primary"></i>
            </div>
            <h4 class="fw-700 mb-0">{{ $stats['enrolled_total'] }}</h4>
            <div class="small text-success mt-1"><i class="fas fa-check me-1"></i>{{ $stats['completed'] }} complétés</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-cb-muted small fw-600">Progression moyenne</span>
                <i class="fas fa-spinner text-warning"></i>
            </div>
            <h4 class="fw-700 mb-0">{{ $stats['avg_progress'] }}%</h4>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $stats['avg_progress'] }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-cb-muted small fw-600">Score Quiz Moyen</span>
                <i class="fas fa-award text-success"></i>
            </div>
            <h4 class="fw-700 mb-0">{{ $stats['avg_quiz_score'] }}%</h4>
            <div class="small text-cb-muted mt-1">{{ $stats['attempts_count'] }} tentatives passées</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-cb-muted small fw-600">Revenus</span>
                <i class="fas fa-wallet text-cb-primary"></i>
            </div>
            <h4 class="fw-700 mb-0">{{ number_format($stats['revenue_total'], 0, ',', ' ') }} XOF</h4>
            <div class="small text-cb-muted mt-1">Total des commandes payées</div>
        </div>
    </div>
</div>

<!-- Graphiques analytiques -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-cabform p-4">
            <h6 class="fw-700 text-cb-primary mb-3">Évolution des Inscriptions</h6>
            <canvas id="enrollmentsChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-cabform p-4">
            <h6 class="fw-700 text-cb-primary mb-3">Scores aux Quiz</h6>
            <canvas id="quizDistributionChart" height="220"></canvas>
        </div>
    </div>
</div>

<!-- Onglets Suivi Métier -->
<div class="card-cabform p-0">
    <ul class="nav nav-tabs nav-tabs-cabform p-3 pb-0" id="courseTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Détails</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="curriculum-tab" data-bs-toggle="tab" data-bs-target="#curriculum" type="button" role="tab">Programme</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">Apprenants</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab">Avis</button>
        </li>
    </ul>

    <div class="tab-content p-4" id="courseTabsContent">
        <!-- Détails -->
        <div class="tab-pane fade show active" id="details" role="tabpanel">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-cb-muted mb-1">Catégorie</label>
                    <p class="fw-600">{{ $course->category->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="small text-cb-muted mb-1">Instructeur</label>
                    <p class="fw-600">{{ $course->instructor->full_name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="small text-cb-muted mb-1">Tarif</label>
                    <p class="fw-600">{{ $course->formatted_price }}</p>
                </div>
                <div class="col-md-6">
                    <label class="small text-cb-muted mb-1">Statut</label>
                    <p class="fw-600">
                        <span class="badge-cabform {{ $course->status === 'published' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-12">
                    <label class="small text-cb-muted mb-1">Description</label>
                    <p class="text-cb-muted mb-0">{{ $course->description ?? 'Aucune description.' }}</p>
                </div>
            </div>
        </div>

        <!-- Curriculum -->
        <div class="tab-pane fade" id="curriculum" role="tabpanel">
            @forelse($course->modules as $module)
            <div class="border border-cb-glass-border rounded-cb p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-700 mb-0 text-cb-primary"><i class="fas fa-folder me-2"></i>{{ $module->title }}</h6>
                    <span class="badge bg-secondary badge-sm">{{ $module->lessons->count() }} leçons</span>
                </div>
                <div class="mt-2 ps-3">
                    @forelse($module->lessons as $lesson)
                    <div class="small py-1 text-cb-muted border-bottom border-cb-glass-border d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-file-alt text-cb-primary me-2"></i>{{ $lesson->title }}</span>
                        @if($lesson->video_path)
                            <span class="badge bg-light text-dark"><i class="fas fa-video me-1"></i>Vidéo</span>
                        @endif
                    </div>
                    @empty
                    <p class="text-cb-muted small mb-0">Aucune leçon.</p>
                    @endforelse
                </div>
            </div>
            @empty
            <p class="text-cb-muted py-3">Aucun module n'a été créé.</p>
            @endforelse
        </div>

        <!-- Apprenants -->
        <div class="tab-pane fade" id="students" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-cabform mb-0">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Inscrit le</th>
                            <th>Progression</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td><span class="fw-600">{{ $enrollment->user->full_name }}</span></td>
                            <td>{{ $enrollment->user->email }}</td>
                            <td>{{ $enrollment->created_at?->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress w-100" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="small fw-600">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-cb-muted py-3">Aucun élève inscrit pour le moment.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pt-3">{{ $enrollments->links() }}</div>
        </div>

        <!-- Feedback / Avis -->
        <div class="tab-pane fade" id="feedback" role="tabpanel">
            @forelse($reviews as $review)
            <div class="border-bottom border-cb-glass-border py-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-600 small">{{ $review->user->full_name }}</span>
                    <div>
                        @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-light' }} small"></i>
                        @endfor
                    </div>
                </div>
                <p class="text-cb-muted small mb-0">{{ $review->comment }}</p>
            </div>
            @empty
            <p class="text-center text-cb-muted py-3">Aucun avis laissé pour cette formation.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Line chart for registrations
        var ctxEnrollments = document.getElementById('enrollmentsChart').getContext('2d');
        new Chart(ctxEnrollments, {
            type: 'line',
            data: {
                labels: ['Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4', 'Semaine 5', 'Semaine 6'],
                datasets: [{
                    label: 'Inscriptions',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgba(0, 82, 204, 1)',
                    backgroundColor: 'rgba(0, 82, 204, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Bar chart for quiz scores
        var ctxQuiz = document.getElementById('quizDistributionChart').getContext('2d');
        new Chart(ctxQuiz, {
            type: 'bar',
            data: {
                labels: ['0-40%', '40-60%', '60-80%', '80-100%'],
                datasets: [{
                    label: 'Répartition des notes',
                    data: [4, 11, 25, 45],
                    backgroundColor: [
                        'rgba(230, 55, 87, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(0, 82, 204, 0.7)',
                        'rgba(0, 217, 126, 0.7)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
