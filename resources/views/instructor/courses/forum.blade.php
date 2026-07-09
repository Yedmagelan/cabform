@extends('layouts.instructor')

@section('title', 'Modération Forum')
@section('page_title', 'Modération du Forum')

@section('content')
<div class="card card-instructor p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Forum de discussion : {{ $course->title }}</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Modérez les discussions, épinglez les sujets importants ou répondez directement.</span>
        </div>
        <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
    </div>

    <!-- Threads Table -->
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Sujet / Auteur</th>
                    <th>Date de création</th>
                    <th>Réponses</th>
                    <th>Options</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($threads as $thread)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 32px; height: 32px;">{{ $thread->user->initials }}</div>
                                <div>
                                    <div class="fw-bold text-white">
                                        @if($thread->is_pinned)
                                            <i class="fas fa-thumbtack text-warning me-1" title="Épinglé"></i>
                                        @endif
                                        @if($thread->is_locked)
                                            <i class="fas fa-lock text-muted me-1" title="Verrouillé"></i>
                                        @endif
                                        <a href="{{ route('instructor.forum.show', [$course->id, $thread->id]) }}" class="text-white text-decoration-none hover-indigo">{{ $thread->title }}</a>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.75rem;">Lancé par {{ $thread->user->full_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $thread->created_at->format('d/m/Y') }}</td>
                        <td><span class="badge bg-indigo-subtle text-indigo">{{ $thread->replies_count }}</span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <form action="{{ route('instructor.forum.pin', [$course->id, $thread->id]) }}" method="POST">@csrf
                                    <button type="submit" class="btn btn-sm btn-cabform-glass text-{{ $thread->is_pinned ? 'warning' : 'white' }}" title="Épingler"><i class="fas fa-thumbtack"></i></button>
                                </form>
                                <form action="{{ route('instructor.forum.lock', [$course->id, $thread->id]) }}" method="POST">@csrf
                                    <button type="submit" class="btn btn-sm btn-cabform-glass text-{{ $thread->is_locked ? 'warning' : 'white' }}" title="Verrouiller"><i class="fas fa-lock"></i></button>
                                </form>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('instructor.forum.show', [$course->id, $thread->id]) }}" class="btn btn-sm btn-premium">Répondre</a>
                                <form action="{{ route('instructor.forum.destroy-thread', [$course->id, $thread->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce sujet ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-cabform-glass text-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">Aucune discussion ouverte pour ce cours.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $threads->links() }}
    </div>
</div>
@endsection
