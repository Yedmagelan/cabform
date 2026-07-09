@extends('layouts.learner')

@section('title', 'Mon Tableau de Bord')
@section('page_title', 'Tableau de Bord')

@push('styles')
<style>
    .kpi-card { background: var(--cb-dark-card); border: 1px solid var(--cb-glass-border); border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: var(--cb-shadow-lg); }
    .timeline-item { position: relative; padding-left: 24px; border-left: 2px solid var(--cb-glass-border); margin-bottom: 20px; }
    .timeline-item::before { content: ''; position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: var(--cb-primary); border: 2px solid var(--cb-dark); }
</style>
@endpush

@section('content')
<!-- Welcome Banner -->
<div class="card card-instructor p-4 mb-4" style="border-left: 4px solid var(--cb-primary);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-white mb-1">
                {{ $greeting }}, {{ auth()->user()->first_name }} ! 
                <span style="color: {{ $greetingColor }};"><i class="fas {{ $greeting === 'Bonjour' ? 'fa-sun' : 'fa-moon' }}"></i></span>
            </h3>
            <p class="text-muted mb-0">Nous sommes le {{ date('d/m/Y') }} &bull; Niveau actuel : <span class="badge bg-indigo">{{ $levelStatus }}</span></p>
            <small class="text-muted">Dernière connexion : {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Première connexion' }}</small>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="auto-refresh-check">
                <label class="form-check-label text-muted" for="auto-refresh-check" style="font-size: 0.85rem;">Auto-refresh</label>
            </div>
            <button id="refresh-dashboard-btn" class="btn btn-sm btn-outline-secondary border-secondary text-white">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
            </button>
        </div>
    </div>
</div>

<!-- KPIs Cards -->
<div class="row g-3 mb-4">
    <!-- Card 1: Enrolled -->
    <div class="col-12 col-md-4">
        <a href="#active-courses-section" class="text-decoration-none">
            <div class="kpi-card p-3 text-center h-100">
                <i class="fas fa-book-open text-indigo fs-3 mb-2"></i>
                <span class="text-muted d-block" style="font-size: 0.8rem;">Formations</span>
                <h4 class="fw-bold text-white mb-0 mt-1">{{ $activeEnrollments->count() }}</h4>
            </div>
        </a>
    </div>
    <!-- Card 2: Certificates -->
    <div class="col-12 col-md-4">
        <a href="#certificates-section" class="text-decoration-none">
            <div class="kpi-card p-3 text-center h-100">
                <i class="fas fa-award text-warning fs-3 mb-2"></i>
                <span class="text-muted d-block" style="font-size: 0.8rem;">Certificats</span>
                <h4 class="fw-bold text-white mb-0 mt-1">{{ $certificatesCount }}</h4>
            </div>
        </a>
    </div>
    <!-- Card 3: Satisfaction -->
    <div class="col-12 col-md-4">
        <div class="kpi-card p-3 text-center h-100">
            <i class="fas fa-star text-warning fs-3 mb-2"></i>
            <span class="text-muted d-block" style="font-size: 0.8rem;">Note Quiz</span>
            <h4 class="fw-bold text-white mb-0 mt-1">{{ $satisfactionRate }}/5</h4>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Active Courses & Sorting/Search workspace -->
    <div class="col-lg-8" id="active-courses-section">
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="fw-bold text-white mb-0"><i class="fas fa-play-circle text-indigo me-2"></i>Mes formations en cours</h5>
                
                <!-- Filter Controls -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="text" id="course-search-input" class="form-control bg-dark border-secondary text-white btn-sm py-1" placeholder="Rechercher..." style="width: 140px;">
                    <select id="course-sort-select" class="form-select bg-dark border-secondary text-white btn-sm py-1" style="width: 130px;">
                        <option value="date">Date Inscription</option>
                        <option value="progression">Progression</option>
                        <option value="title">Titre</option>
                    </select>
                    <div class="btn-group">
                        <button id="toggle-grid-btn" class="btn btn-sm btn-outline-secondary border-secondary text-white active"><i class="fas fa-th-large"></i></button>
                        <button id="toggle-list-btn" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-list"></i></button>
                    </div>
                </div>
            </div>

            <!-- AJAX courses loading container -->
            <div id="ajax-courses-container">
                @include('learner.course._courses_list')
            </div>
        </div>
    </div>

    <!-- Deadlines Timeline & Recommendations Side widgets -->
    <div class="col-lg-4">
        <!-- Deadlines Timeline -->
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-calendar-alt text-indigo me-2"></i>Échéances à venir</h5>
            
            <div class="timeline">
                @forelse($timeline as $item)
                    @php
                        $daysLeft = now()->diffInDays($item['due_date'], false);
                        $deadlineColor = $daysLeft < 3 ? 'text-danger' : ($daysLeft < 7 ? 'text-warning' : 'text-success');
                    @endphp
                    <div class="timeline-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <a href="{{ $item['url'] }}" class="text-white fw-bold text-decoration-none hover-indigo" style="font-size: 0.9rem;">{{ $item['title'] }}</a>
                                <small class="text-muted d-block mt-1">Limite : {{ $item['due_date']->format('d/m/Y H:i') }}</small>
                            </div>
                            <span class="badge bg-dark border border-secondary {{ $deadlineColor }}" style="font-size: 0.75rem;">
                                @if($daysLeft < 0)
                                    Dépassée
                                @else
                                    {{ round($daysLeft) }}j restants
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">Aucune échéance à venir.</div>
                @endforelse
            </div>
        </div>

        <!-- Recommended Courses -->
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-lightbulb text-indigo me-2"></i>Recommandations</h5>
            
            <div class="d-flex flex-column gap-3">
                @foreach($recommendedCourses as $course)
                    <div class="p-3 bg-dark border border-secondary rounded d-flex gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <strong class="text-white d-block" style="font-size: 0.85rem;">{{ $course->title }}</strong>
                            <small class="text-muted">{{ $course->category->name ?? 'Général' }} &bull; {{ $course->level_label }}</small>
                        </div>
                        <a href="{{ route('course.show', $course->slug) }}" class="btn btn-sm btn-premium py-1 px-3">Découvrir</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Toast notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="dashboard-toast" class="toast align-items-center text-white bg-indigo border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>Dashboard mis à jour !
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let refreshTimer = null;

        // Toggle Grid vs List views
        $('#toggle-grid-btn').on('click', function() {
            $(this).addClass('active').siblings().removeClass('active');
            $('#courses-grid').removeClass('d-none').addClass('active');
            $('#courses-list').addClass('d-none').removeClass('active');
        });

        $('#toggle-list-btn').on('click', function() {
            $(this).addClass('active').siblings().removeClass('active');
            $('#courses-list').removeClass('d-none').addClass('active');
            $('#courses-grid').addClass('d-none').removeClass('active');
        });

        // Trigger Ajax Filtering and Search
        function fetchCourses(page = 1) {
            const search = $('#course-search-input').val();
            const sort = $('#course-sort-select').val();
            const $icon = $('#refresh-icon');

            $icon.addClass('fa-spin');

            $.ajax({
                url: `{{ route('learner.dashboard.courses-ajax') }}?page=${page}&search=${search}&sort=${sort}`,
                method: 'GET',
                success: function(html) {
                    $('#ajax-courses-container').html(html);
                    $icon.removeClass('fa-spin');

                    // Preserve grid/list active modes
                    if ($('#toggle-list-btn').hasClass('active')) {
                        $('#courses-list').removeClass('d-none');
                        $('#courses-grid').addClass('d-none');
                    }
                }
            });
        }

        $('#course-search-input').on('keyup', function() {
            fetchCourses();
        });

        $('#course-sort-select').on('change', function() {
            fetchCourses();
        });

        $(document).on('click', '#ajax-pagination-links a', function(e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            fetchCourses(page);
        });

        // Refresh widgets manual
        $('#refresh-dashboard-btn').on('click', function() {
            fetchCourses();
            const toast = new bootstrap.Toast(document.getElementById('dashboard-toast'));
            toast.show();
        });

        // Auto refresh
        $('#auto-refresh-check').on('change', function() {
            if (this.checked) {
                refreshTimer = setInterval(function() {
                    fetchCourses();
                }, 60000); // 60 seconds
            } else {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                }
            }
        });
    });
</script>
@endpush
