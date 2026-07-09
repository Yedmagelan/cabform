@extends('layouts.learner')
@section('title', 'Forum de discussion')
@section('page_title', 'Forum de discussion')

@section('content')
<div class="row g-4">
    <!-- Liste des Sujets -->
    <div class="col-lg-8">
        <div class="card-cabform p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-700 mb-0"><i class="fas fa-comments text-cb-primary me-2"></i>Sujets de discussion</h5>
                <form class="d-flex gap-2" method="GET" action="{{ route('learner.forum.index') }}">
                    <select name="course_id" class="form-select form-control-cabform" onchange="this.form.submit()">
                        <option value="">Toutes les formations</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($threads->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($threads as $thread)
                        <div class="p-3 rounded-cb" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border);">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar text-center" style="width: 28px; height: 28px; font-size: 0.75rem; line-height: 28px;">
                                        {{ $thread->user->initials }}
                                    </div>
                                    <span class="fw-600" style="font-size: 0.85rem;">{{ $thread->user->full_name }}</span>
                                    @if($thread->course)
                                        <span class="badge-cabform badge-primary" style="font-size: 0.7rem;">{{ $thread->course->title }}</span>
                                    @endif
                                </div>
                                <span class="text-cb-muted" style="font-size: 0.8rem;"><i class="far fa-clock me-1"></i>{{ $thread->created_at?->diffForHumans() }}</span>
                            </div>
                            <h6 class="fw-700 mb-2">
                                <a href="{{ route('learner.forum.thread.show', $thread->id) }}" class="text-cb-text hover-primary" style="text-decoration: none;">
                                    {{ $thread->title }}
                                </a>
                            </h6>
                            <p class="text-cb-muted mb-3 text-truncate" style="font-size: 0.9rem; max-width: 100%;">{{ Str::limit(strip_tags($thread->body), 150) }}</p>
                            <div class="d-flex align-items-center gap-3 text-cb-muted" style="font-size: 0.8rem;">
                                <span><i class="far fa-comment me-1"></i>{{ $thread->replies_count }} réponse(s)</span>
                                <span><i class="far fa-eye me-1"></i>{{ $thread->views_count }} vue(s)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $threads->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-cb-muted mb-3" style="font-size: 3rem;"><i class="far fa-comments"></i></div>
                    <p class="text-cb-muted mb-0">Aucun sujet créé pour le moment.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Créer un Nouveau Sujet -->
    <div class="col-lg-4">
        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-plus-circle text-cb-primary me-2"></i>Nouveau sujet</h5>

            @if(session('success'))
                <div class="alert alert-success border-0 rounded-cb mb-3">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('learner.forum.thread.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label-cabform">Titre</label>
                    <input type="text" name="title" id="title" class="form-control form-control-cabform" placeholder="Titre de votre question" required>
                </div>
                <div class="mb-3">
                    <label for="course_id" class="form-label-cabform">Formation concernée (Optionnel)</label>
                    <select name="course_id" id="course_id" class="form-select form-control-cabform">
                        <option value="">Aucune spécifique</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label-cabform">Détail de votre question</label>
                    <textarea name="body" id="body" class="form-control form-control-cabform" rows="6" placeholder="Décrivez votre problème ou question..." required></textarea>
                </div>
                <button type="submit" class="btn btn-cabform btn-cabform-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i>Publier le sujet
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
