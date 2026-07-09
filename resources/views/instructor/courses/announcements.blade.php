@extends('layouts.instructor')

@section('title', 'Annonces')
@section('page_title', 'Annonces de Formation')

@section('content')
<div class="row g-4">
    <!-- Add announcement form -->
    <div class="col-lg-5">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4"><i class="fas fa-bullhorn text-indigo me-2"></i>Publier une annonce</h5>
            
            <form action="{{ route('instructor.announcements.store', $course->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-white">Titre de l'annonce</label>
                    <input type="text" name="title" class="form-control bg-dark border-secondary text-white py-2" placeholder="Ex: Report du quiz de la semaine 2" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Contenu de l'annonce</label>
                    <textarea name="content" class="form-control bg-dark border-secondary text-white" rows="5" placeholder="Bonjour à tous, suite aux retours..." required></textarea>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="notify_email" id="notify-mail" value="1" checked>
                        <label class="form-check-label text-muted" for="notify-mail">Envoyer également une notification par e-mail</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-2">Diffuser l'annonce</button>
            </form>
        </div>
    </div>

    <!-- Announcements Timeline -->
    <div class="col-lg-7">
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">Historique des annonces</h5>
                <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
            </div>

            <div class="d-flex flex-column gap-3">
                @forelse($announcements as $announcement)
                    <div class="p-3 bg-dark border border-secondary rounded position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold text-white mb-0">{{ $announcement['title'] }}</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Diffusée le {{ date('d/m/Y H:i', strtotime($announcement['created_at'])) }}</small>
                            </div>
                            
                            <form action="{{ route('instructor.announcements.delete', [$course->id, $announcement['id']]) }}" method="POST" onsubmit="return confirm('Supprimer cette annonce ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                        <p class="mb-0 text-muted" style="font-size: 0.95rem; white-space: pre-wrap;">{{ $announcement['content'] }}</p>
                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-bullhorn d-block mb-3" style="font-size: 2.5rem;"></i>
                        Aucune annonce publiée pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
