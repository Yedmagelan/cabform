@extends('layouts.learner')
@section('title', $thread->title)
@section('page_title', 'Forum de discussion')

@section('content')
<div class="mb-3">
    <a href="{{ route('learner.forum.index') }}" class="btn btn-cabform btn-cabform-outline btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Retour au forum
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Sujet Original -->
        <div class="card-cabform p-4 mb-4" style="border-left: 4px solid var(--cb-primary);">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar text-center" style="width: 36px; height: 36px; font-size: 0.9rem; line-height: 36px;">
                        {{ $thread->user->initials }}
                    </div>
                    <div>
                        <h6 class="fw-700 mb-0">{{ $thread->user->full_name }}</h6>
                        @if($thread->course)
                            <span class="badge-cabform badge-primary" style="font-size: 0.7rem;">{{ $thread->course->title }}</span>
                        @endif
                    </div>
                </div>
                <span class="text-cb-muted" style="font-size: 0.8rem;"><i class="far fa-clock me-1"></i>{{ $thread->created_at?->diffForHumans() }}</span>
            </div>
            <h4 class="fw-800 mb-3">{{ $thread->title }}</h4>
            <p class="text-cb-text mb-0" style="white-space: pre-line;">{{ $thread->body }}</p>
        </div>

        <!-- Réponses -->
        <h5 class="fw-700 mb-3"><i class="fas fa-reply text-cb-primary me-2"></i>Réponses ({{ $replies->count() }})</h5>

        @if($replies->count() > 0)
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($replies as $reply)
                    <div class="card-cabform p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar text-center" style="width: 32px; height: 32px; font-size: 0.8rem; line-height: 32px;">
                                    {{ $reply->user->initials }}
                                </div>
                                <h6 class="fw-700 mb-0" style="font-size: 0.9rem;">{{ $reply->user->full_name }}</h6>
                                @if($reply->user->id === $thread->user_id)
                                    <span class="badge-cabform badge-outline" style="font-size: 0.65rem;">Auteur</span>
                                @endif
                            </div>
                            <span class="text-cb-muted" style="font-size: 0.8rem;">{{ $reply->created_at?->diffForHumans() }}</span>
                        </div>
                        <p class="text-cb-text mb-0" style="font-size: 0.95rem; white-space: pre-line;">{{ $reply->body }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-cabform border-0 rounded-cb p-4 text-center mb-4" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border);">
                <p class="text-cb-muted mb-0">Aucune réponse à ce sujet pour le moment. Soyez le premier à répondre !</p>
            </div>
        @endif

        <!-- Formulaire de Réponse -->
        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-3"><i class="fas fa-edit text-cb-primary me-2"></i>Votre réponse</h5>
            <form method="POST" action="{{ route('learner.forum.reply.store', $thread->id) }}">
                @csrf
                <div class="mb-3">
                    <textarea name="body" class="form-control form-control-cabform" rows="5" placeholder="Saisissez votre réponse ici..." required></textarea>
                </div>
                <button type="submit" class="btn btn-cabform btn-cabform-primary">
                    <i class="fas fa-reply me-2"></i>Publier ma réponse
                </button>
            </form>
        </div>
    </div>

    <!-- Méta-informations du Sujet -->
    <div class="col-lg-4">
        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-3"><i class="fas fa-info-circle text-cb-primary me-2"></i>Informations</h5>
            <div class="text-cb-muted d-flex flex-column gap-2" style="font-size: 0.9rem;">
                <div><i class="fas fa-user me-2"></i>Créé par <strong>{{ $thread->user->full_name }}</strong></div>
                <div><i class="far fa-calendar-alt me-2"></i>Le {{ $thread->created_at?->format('d/m/Y à H:i') }}</div>
                <div><i class="far fa-comment me-2"></i>{{ $thread->replies_count }} réponse(s)</div>
                <div><i class="far fa-eye me-2"></i>{{ $thread->views_count }} vue(s)</div>
            </div>
        </div>
    </div>
</div>
@endsection
