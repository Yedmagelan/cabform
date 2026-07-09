<div id="courses-grid" class="view-mode-div active">
    <div class="row g-4">
        @forelse($activeCoursesPaginated as $enrollment)
            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card h-100 d-flex flex-column justify-content-between p-3 position-relative hover-premium">
                    <!-- Favorite Star -->
                    <button class="btn btn-sm btn-link position-absolute end-0 top-0 text-warning bookmark-star-btn mt-2 me-2" data-course-id="{{ $enrollment->course->id }}">
                        <i class="far fa-star"></i>
                    </button>

                    <div>
                        <div class="mb-3 position-relative overflow-hidden rounded" style="height: 120px;">
                            @if($enrollment->course->thumbnail)
                                <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" class="img-fluid w-100 h-100 object-fit-cover">
                            @else
                                <div class="bg-indigo-subtle d-flex align-items-center justify-content-center w-100 h-100 text-white" style="background: var(--cb-gradient-primary);">
                                    <i class="fas fa-graduation-cap fs-2"></i>
                                </div>
                            @endif
                        </div>

                        <span class="badge bg-indigo-subtle text-indigo mb-2" style="background: rgba(99,102,241,0.15); color: #818cf8;">{{ $enrollment->course->category->name ?? 'Général' }}</span>
                        <h6 class="fw-bold text-white mb-2 text-truncate" title="{{ $enrollment->course->title }}">{{ $enrollment->course->title }}</h6>
                        
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="user-avatar" style="width: 24px; height: 24px; font-size: 0.7rem;">{{ $enrollment->course->instructor->initials ?? 'F' }}</div>
                            <small class="text-muted text-truncate">{{ $enrollment->course->instructor->full_name ?? 'Formateur' }}</small>
                        </div>
                    </div>

                    <div>
                        @php
                            $prog = $enrollment->progress_percentage;
                            $barColor = $prog > 75 ? 'bg-success' : ($prog > 50 ? 'bg-warning' : 'bg-danger');
                        @endphp
                        <div class="progress mb-2" style="height: 6px; background: rgba(255,255,255,0.05);">
                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $prog }}%;"></div>
                        </div>

                        <div class="d-flex justify-content-between text-muted mb-3" style="font-size: 0.8rem;">
                            <span>{{ number_format($prog, 0) }}% complété</span>
                            <span><i class="fas fa-clock me-1"></i>{{ $enrollment->time_spent_minutes ?? 0 }} min</span>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('learner.course.player', $enrollment->course->slug) }}" class="btn btn-premium btn-sm w-100 py-1"><i class="fas fa-play me-1"></i>Continuer</a>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary border-secondary text-white" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                                    <li><a class="dropdown-item text-white" href="{{ route('learner.course.player', $enrollment->course->slug) }}"><i class="fas fa-eye me-2"></i>Détails</a></li>
                                    @if($enrollment->status === 'completed')
                                        <li><a class="dropdown-item text-success" href="{{ route('learner.certificates') }}"><i class="fas fa-award me-2"></i>Certificat</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center text-muted">
                <i class="fas fa-book-open d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                Aucune formation en cours ne correspond à ces critères.
            </div>
        @endforelse
    </div>
</div>

<div id="courses-list" class="view-mode-div d-none">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Formation</th>
                    <th>Formateur</th>
                    <th>Progression</th>
                    <th>Temps passé</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeCoursesPaginated as $enrollment)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($enrollment->course->thumbnail)
                                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong class="text-white">{{ $enrollment->course->title }}</strong>
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">{{ $enrollment->course->category->name ?? 'Général' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $enrollment->course->instructor->full_name ?? 'Formateur' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2" style="width: 140px;">
                                <div class="progress w-100" style="height: 6px; background: rgba(255,255,255,0.05);">
                                    @php
                                        $prog = $enrollment->progress_percentage;
                                        $barColor = $prog > 75 ? 'bg-success' : ($prog > 50 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress-bar {{ $barColor }}" style="width: {{ $prog }}%;"></div>
                                </div>
                                <span class="fw-bold" style="font-size: 0.8rem;">{{ round($prog) }}%</span>
                            </div>
                        </td>
                        <td>{{ $enrollment->time_spent_minutes ?? 0 }} min</td>
                        <td class="text-end">
                            <a href="{{ route('learner.course.player', $enrollment->course->slug) }}" class="btn btn-sm btn-premium">Continuer</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Aucune formation trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center" id="ajax-pagination-links">
    {{ $activeCoursesPaginated->links() }}
</div>
