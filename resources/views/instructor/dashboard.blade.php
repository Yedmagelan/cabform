@extends('layouts.instructor')

@section('title', 'Tableau de bord Formateur')
@section('page_title', 'Tableau de bord')

@section('content')
<div class="row g-4 mb-4">
    <!-- KPI 1 -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-instructor card-kpi">
            <div class="kpi-icon bg-indigo text-white" style="background: rgba(99, 102, 241, 0.15); color: #818cf8 !important;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-white">{{ $stats['total_courses'] }}</h3>
                <span class="text-muted" style="font-size: 0.85rem;">Formations créées</span>
            </div>
        </div>
    </div>
    <!-- KPI 2 -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-instructor card-kpi">
            <div class="kpi-icon bg-success text-white" style="background: rgba(16, 185, 129, 0.15); color: #10b981 !important;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-white">{{ $stats['total_students'] }}</h3>
                <span class="text-muted" style="font-size: 0.85rem;">Inscriptions actives</span>
            </div>
        </div>
    </div>
    <!-- KPI 3 -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-instructor card-kpi">
            <div class="kpi-icon bg-warning text-white" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b !important;">
                <i class="fas fa-award"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-white">{{ $stats['total_certificates'] }}</h3>
                <span class="text-muted" style="font-size: 0.85rem;">Certificats générés</span>
            </div>
        </div>
    </div>
    <!-- KPI 4 -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-instructor card-kpi">
            <div class="kpi-icon bg-danger text-white" style="background: rgba(239, 68, 68, 0.15); color: #ef4444 !important;">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 text-white">{{ $stats['satisfaction_rate'] }} / 5</h3>
                <span class="text-muted" style="font-size: 0.85rem;">Satisfaction apprenant</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4">Inscriptions des apprenants (12 derniers mois)</h5>
            <div style="height: 320px;">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        <!-- Recent Courses -->
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">Mes formations récentes</h5>
                <a href="{{ route('instructor.courses') }}" class="text-indigo text-decoration-none" style="color: #818cf8; font-size: 0.875rem;">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Formation</th>
                            <th>Modules</th>
                            <th>Inscrits</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-white">{{ $course->title }}</div>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ $course->level_label }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $course->modules_count }} modules</td>
                                <td>{{ $course->enrollments_count }} apprenants</td>
                                <td>
                                    <span class="badge badge-{{ $course->status }}">
                                        {{ match($course->status) {
                                            'draft' => 'Brouillon',
                                            'pending_review' => 'En révision',
                                            'published' => 'Publié',
                                            'archived' => 'Archivé',
                                            default => $course->status
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('instructor.courses.edit', $course->id) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-edit"></i> Écrire</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Aucune formation créée pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments & Fast actions -->
    <div class="col-lg-4">
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4">Actions Rapides</h5>
            <div class="d-grid gap-3">
                <a href="{{ route('instructor.courses.create') }}" class="btn btn-premium py-2 w-100 text-center"><i class="fas fa-plus me-2"></i>Créer une formation</a>
                <a href="{{ route('instructor.resources.library') }}" class="btn btn-outline-secondary text-white border-secondary py-2 w-100 text-center"><i class="fas fa-images me-2"></i>Médiathèque</a>
                <a href="{{ route('instructor.messages.index') }}" class="btn btn-outline-secondary text-white border-secondary py-2 w-100 text-center"><i class="fas fa-comments me-2"></i>Messagerie direct</a>
            </div>
        </div>

        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4">Inscriptions récentes</h5>
            <div class="d-flex flex-column gap-3">
                @forelse($recentEnrollments as $enrollment)
                    <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: rgba(255,255,255,0.02);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">{{ $enrollment->user->initials }}</div>
                            <div>
                                <div class="fw-bold text-white" style="font-size: 0.85rem;">{{ $enrollment->user->full_name }}</div>
                                <span class="text-muted d-block" style="font-size: 0.75rem;">{{ $enrollment->course->title }}</span>
                            </div>
                        </div>
                        <span class="text-muted" style="font-size: 0.75rem;">{{ $enrollment->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Aucune inscription récente.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('registrationChart').getContext('2d');
        
        const monthlyData = @json($stats['monthly_enrollments']);
        const labels = Object.keys(monthlyData);
        const data = Object.values(monthlyData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inscriptions',
                    data: data,
                    borderColor: '#818cf8',
                    backgroundColor: 'rgba(129, 140, 248, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
