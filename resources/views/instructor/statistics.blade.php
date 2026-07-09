@extends('layouts.instructor')

@section('title', 'Statistiques & Analyses')
@section('page_title', 'Statistiques Générales')

@section('content')
<div class="card card-instructor p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Rapports d'activité de vos formations</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Suivez l'engagement, la réussite et les avis de vos apprenants.</span>
        </div>
        
        <!-- Period Selector Filter Form -->
        <form action="{{ route('instructor.statistics.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size: 0.85rem; white-space: nowrap;">Période :</span>
            <select name="period" class="form-select bg-dark border-secondary text-white btn-sm" onchange="this.form.submit()" style="width: 160px;">
                <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Depuis le début</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                <option value="3_months" {{ $period === '3_months' ? 'selected' : '' }}>3 derniers mois</option>
                <option value="6_months" {{ $period === '6_months' ? 'selected' : '' }}>6 derniers mois</option>
                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Cette année</option>
            </select>
        </form>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Inscriptions sur la période</span>
                <span class="fw-bold text-indigo" style="font-size: 1.5rem;">{{ $stats['total_students'] }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Progression moyenne</span>
                <span class="fw-bold text-white" style="font-size: 1.5rem;">{{ $stats['avg_progress'] }}%</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Taux de réussite (Diplômés)</span>
                <span class="fw-bold text-success" style="font-size: 1.5rem;">{{ $stats['completion_rate'] }}%</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Nombre de diplômes</span>
                <span class="fw-bold text-warning" style="font-size: 1.5rem;">{{ $stats['total_certificates'] }}</span>
            </div>
        </div>
    </div>

    <!-- Charts Workspace -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="p-4 bg-dark border border-secondary rounded h-100">
                <h6 class="fw-bold text-white mb-4">Évolution des Inscriptions</h6>
                <div style="height: 280px;">
                    <canvas id="detailsRegistrationChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-dark border border-secondary rounded h-100">
                <h6 class="fw-bold text-white mb-4">Satisfaction par niveau</h6>
                <div class="text-center py-4">
                    <i class="fas fa-star text-warning" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold text-white mt-3">{{ $stats['satisfaction_rate'] }} / 5</h3>
                    <span class="text-muted">Moyenne générale calculée sur les avis.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Course Completeness metrics -->
    <div class="p-4 bg-dark border border-secondary rounded">
        <h6 class="fw-bold text-white mb-4">Taux de réussite par formation</h6>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Titre de la formation</th>
                        <th>Nombre d'inscrits</th>
                        <th>Taux de réussite</th>
                        <th class="text-end">Rapports</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['completion_per_course'] ?? [] as $title => $data)
                        <tr>
                            <td>{{ $title }}</td>
                            <td>{{ $data['enrollments'] }} apprenants</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 100px; height: 6px; background: rgba(255,255,255,0.05);">
                                        <div class="progress-bar bg-success" style="width: {{ $data['completion_rate'] }}%;"></div>
                                    </div>
                                    <span class="fw-bold">{{ $data['completion_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="text-end">
                                @php
                                    $courseInstance = $courses->where('title', $title)->first();
                                @endphp
                                @if($courseInstance)
                                    <a href="{{ route('instructor.courses.export-pdf', $courseInstance->id) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-file-pdf"></i> Export PDF</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('detailsRegistrationChart').getContext('2d');
        const monthlyData = @json($stats['monthly_enrollments']);
        const labels = Object.keys(monthlyData);
        const data = Object.values(monthlyData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inscriptions',
                    data: data,
                    backgroundColor: '#818cf8',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8', precision: 0 } }
                }
            }
        });
    });
</script>
@endpush
