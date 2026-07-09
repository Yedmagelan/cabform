@extends('layouts.instructor')

@section('title', $thread->title)
@section('page_title', 'Modération de Sujet')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Original Thread Question -->
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar">{{ $thread->user->initials }}</div>
                    <div>
                        <h5 class="fw-bold text-white mb-1">{{ $thread->title }}</h5>
                        <small class="text-muted">Par {{ $thread->user->full_name }} &bull; Soumis {{ $thread->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <a href="{{ route('instructor.forum.index', $course->id) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
            </div>
            
            <div class="p-3 bg-dark border border-secondary rounded text-white" style="background: rgba(255,255,255,0.02) !important;">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $thread->body }}</p>
            </div>
        </div>

        <!-- Replies List -->
        <h6 class="fw-bold text-white mb-3">Réponses & Messages ({{ $thread->posts->count() }})</h6>
        <div class="d-flex flex-column gap-3 mb-4">
            @forelse($thread->posts as $post)
                @php
                    $isAuthor = $post->user_id === auth()->id();
                @endphp
                <div class="card bg-dark border-secondary p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.85rem;">{{ $post->user->initials }}</div>
                            <div>
                                <strong class="text-white">{{ $post->user->full_name }}</strong>
                                @if($isAuthor)
                                    <span class="badge bg-indigo ms-1" style="background: #6366f1;">Formateur</span>
                                @endif
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        
                        <!-- Moderation delete option -->
                        <form action="{{ route('instructor.forum.destroy-post', [$course->id, $thread->id, $post->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce message ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    <p class="mb-0 text-white" style="font-size: 0.9rem; white-space: pre-wrap;">{{ $post->body }}</p>
                </div>
            @empty
                <div class="p-4 text-center text-muted card bg-dark border-secondary">Aucune réponse pour le moment.</div>
            @endforelse
        </div>

        <!-- Reply Editor Form -->
        @if(!$thread->is_locked)
            <div class="card card-instructor p-4">
                <h6 class="fw-bold text-white mb-3"><i class="fas fa-reply me-2 text-indigo"></i>Ajouter une réponse officielle</h6>
                <form action="{{ route('instructor.forum.reply', [$course->id, $thread->id]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="body" class="form-control bg-dark border-secondary text-white" rows="4" placeholder="Saisissez votre réponse ici..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-premium"><i class="fas fa-paper-plane me-2"></i>Publier le message</button>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-secondary bg-dark border-secondary text-center text-muted py-3">
                <i class="fas fa-lock me-2"></i> Cette discussion est verrouillée. Vous ne pouvez plus y répondre.
            </div>
        @endif
    </div>
</div>
@endsection
