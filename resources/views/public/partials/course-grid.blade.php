<div class="row g-4">
    @forelse($courses as $course)
        <div class="col-lg-4 col-md-6">
            <div class="card-cabform h-100">
                <div class="card-img-wrapper">
                    <div style="height: 200px; background: linear-gradient(135deg, rgba(5,0,216,0.3), rgba(77,107,254,0.1)); display: flex; align-items: center; justify-content: center;">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="card-img-top" alt="{{ $course->title }}">
                        @else
                            <i class="fas fa-graduation-cap" style="font-size: 3rem; opacity: 0.4; color: var(--cb-primary-light);"></i>
                        @endif
                    </div>
                    <span class="badge-price {{ $course->is_free ? 'badge-free' : '' }}">
                        {{ $course->formatted_price }}
                    </span>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge-cabform badge-primary">{{ $course->category->name ?? 'Général' }}</span>
                        <span class="badge-cabform" style="background: rgba(var(--cb-accent-rgb),0.1); color: var(--cb-accent);">{{ $course->level_label }}</span>
                    </div>
                    <h5 class="card-title">{{ $course->title }}</h5>
                    <p class="card-text flex-grow-1">{{ Str::limit($course->description, 100) }}</p>

                    <!-- Rating -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($course->rating) ? '' : 'empty' }}"></i>
                            @endfor
                        </div>
                        <span class="text-cb-muted" style="font-size: 0.8rem;">{{ number_format($course->rating, 1) }} ({{ $course->rating_count }})</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-auto pt-3" style="border-top: 1px solid var(--cb-glass-border);">
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:28px;height:28px;font-size:0.6rem;">{{ $course->instructor->initials ?? 'CF' }}</div>
                            <span class="text-cb-muted" style="font-size: 0.78rem;">{{ $course->instructor->full_name ?? 'CabForm' }}</span>
                        </div>
                        <div class="text-cb-muted" style="font-size: 0.78rem;">
                            <i class="fas fa-clock me-1"></i>{{ $course->duration_hours }}h
                            <i class="fas fa-users ms-2 me-1"></i>{{ $course->enrollment_count }}
                        </div>
                    </div>
                    <a href="{{ route('course.show', $course->slug) }}" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100 mt-3">
                        <i class="fas fa-arrow-right me-1"></i>Voir la formation
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="mb-4"><i class="fas fa-search" style="font-size: 4rem; color: var(--cb-text-muted); opacity: 0.3;"></i></div>
            <h4 class="text-cb-muted">Aucune formation trouvée</h4>
            <p class="text-cb-muted">Essayez de modifier vos critères de recherche.</p>
        </div>
    @endforelse
</div>
