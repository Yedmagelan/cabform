@extends('layouts.instructor')

@section('title', 'Médiathèque')
@section('page_title', 'Médiathèque')

@section('content')
<div class="card card-instructor p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Médiathèque de formation</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Gérez et réutilisez l'ensemble des fichiers (PDFs, vidéos, images) téléversés.</span>
        </div>
        <button type="button" class="btn btn-premium btn-sm" data-bs-toggle="modal" data-bs-target="#uploadMediaModal"><i class="fas fa-upload me-2"></i>Téléverser un fichier</button>
    </div>

    <!-- Filters -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <form action="{{ route('instructor.resources.library') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
            <input type="text" name="search" class="form-control bg-dark border-secondary text-white btn-sm" placeholder="Rechercher par nom..." value="{{ request()->search }}" style="width: 240px;">
            <select name="type" class="form-select bg-dark border-secondary text-white" style="width: 140px;" onchange="this.form.submit()">
                <option value="all" {{ request()->type === 'all' ? 'selected' : '' }}>Tous types</option>
                <option value="application/pdf" {{ request()->type === 'application/pdf' ? 'selected' : '' }}>PDFs</option>
                <option value="video" {{ request()->type === 'video' ? 'selected' : '' }}>Vidéos</option>
                <option value="image" {{ request()->type === 'image' ? 'selected' : '' }}>Images</option>
            </select>
        </form>
    </div>

    <!-- Library list -->
    <div class="row g-3">
        @forelse($resources as $resource)
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card bg-dark border-secondary p-3 h-100 d-flex flex-column justify-content-between">
                    <div class="text-center mb-3">
                        @if(Str::startsWith($resource->file_type, 'image/'))
                            <img src="{{ asset('storage/' . $resource->file_path) }}" class="rounded img-fluid" style="height: 120px; object-fit: cover; width: 100%;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-secondary rounded mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                                @if(Str::startsWith($resource->file_type, 'application/pdf'))
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @elseif(Str::startsWith($resource->file_type, 'video/'))
                                    <i class="fas fa-file-video text-indigo"></i>
                                @else
                                    <i class="fas fa-file-alt text-white"></i>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <strong class="text-white d-block text-truncate" style="font-size: 0.9rem;" title="{{ $resource->title }}">{{ $resource->title }}</strong>
                        <span class="text-muted d-block" style="font-size: 0.75rem;">{{ $resource->formatted_size }} &bull; {{ $resource->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary">
                        <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="btn btn-sm btn-link text-indigo text-decoration-none p-0" style="color: #818cf8;">Télécharger</a>
                        <form action="{{ route('instructor.resources.delete', $resource->id) }}" method="POST" onsubmit="return confirm('Supprimer ce fichier de la médiathèque ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none p-0"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center text-muted">
                <i class="fas fa-folder-open d-block mb-3" style="font-size: 3rem;"></i>
                Médiathèque vide.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $resources->links() }}
    </div>
</div>

<!-- =========================================================================
     MODAL UPLOAD FILE
     ========================================================================= -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Téléverser dans la médiathèque</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('instructor.resources.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre / Nom du fichier</label>
                        <input type="text" name="title" class="form-control bg-dark border-secondary text-white" placeholder="Optionnel">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Associer à une leçon</label>
                        <select name="lesson_id" class="form-select bg-dark border-secondary text-white" required>
                            <option value="">-- Sélectionnez une leçon --</option>
                            @php
                                $lessons = \App\Models\Lesson::whereHas('module.course', function ($q) {
                                    $q->where('instructor_id', auth()->id());
                                })->get();
                            @endphp
                            @foreach($lessons as $l)
                                <option value="{{ $l->id }}">{{ $l->title }} ({{ $l->module->course->title }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sélectionner le fichier</label>
                        <input type="file" name="file" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_downloadable" id="media-dl" value="1" checked>
                            <label class="form-check-label text-muted" for="media-dl">Autoriser le téléchargement aux apprenants</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Téléverser</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
