@extends('layouts.instructor')

@section('title', 'Mes Formations')
@section('page_title', 'Mes Formations')

@section('content')
<div class="card card-instructor p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <!-- Filtres -->
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size: 0.9rem;">Filtrer :</span>
            <div class="btn-group">
                <a href="{{ route('instructor.courses') }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ !request()->has('status') ? 'active' : '' }}">Tous</a>
                <a href="{{ route('instructor.courses', ['status' => 'draft']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'draft' ? 'active' : '' }}">Brouillons</a>
                <a href="{{ route('instructor.courses', ['status' => 'pending_review']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'pending_review' ? 'active' : '' }}">En révision</a>
                <a href="{{ route('instructor.courses', ['status' => 'published']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'published' ? 'active' : '' }}">Publiés</a>
                <a href="{{ route('instructor.courses', ['status' => 'archived']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'archived' ? 'active' : '' }}">Archivés</a>
            </div>
        </div>

        <a href="{{ route('instructor.courses.create') }}" class="btn btn-premium"><i class="fas fa-plus me-2"></i>Nouvelle formation</a>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Formation</th>
                    <th>Date de création</th>
                    <th>Prix</th>
                    <th>Progression / Élèves</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-white">{{ $course->title }}</div>
                                    <span class="text-muted" style="font-size: 0.8rem;">{{ $course->category->name ?? 'Sans catégorie' }} &bull; {{ $course->level_label }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $course->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($course->is_free)
                                <span class="badge bg-success-subtle text-success">Gratuit</span>
                            @else
                                <span class="fw-bold text-white">{{ $course->formatted_price }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;">
                                <strong>{{ $course->enrollments_count }}</strong> étudiants inscrits
                            </div>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $course->modules_count }} modules &bull; {{ $course->total_lessons }} leçons</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $course->status }}">
                                {{ match($course->status) {
                                    'draft' => 'Brouillon',
                                    'pending_review' => 'En révision',
                                    'published' => 'Publié',
                                    'archived' => 'Archivé',
                                    default => $course->status
                                } }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-cabform-glass btn-cabform-sm dropdown-toggle text-white" type="button" data-bs-toggle="dropdown">
                                    Options
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                                    <li><a class="dropdown-item text-white" href="{{ route('instructor.courses.edit', $course->id) }}"><i class="fas fa-edit me-2"></i>Éditer / Gérer</a></li>
                                    <li><a class="dropdown-item text-white" href="{{ route('instructor.courses.statistics', $course->id) }}"><i class="fas fa-chart-line me-2"></i>Statistiques</a></li>
                                    <li><a class="dropdown-item text-white" href="{{ route('instructor.students', $course->id) }}"><i class="fas fa-user-graduate me-2"></i>Suivi Étudiants</a></li>
                                    <li><a class="dropdown-item text-white" href="{{ route('instructor.sessions.index', $course->id) }}"><i class="fas fa-users-cog me-2"></i>Sessions Cohortes</a></li>
                                    <li><a class="dropdown-item text-white" href="{{ route('instructor.forum.index', $course->id) }}"><i class="fas fa-comments me-2"></i>Forum & Modération</a></li>
                                    <li><a class="dropdown-item text-white" href="{{ route('instructor.announcements.index', $course->id) }}"><i class="fas fa-bullhorn me-2"></i>Annonces</a></li>
                                    <li><hr class="dropdown-divider border-secondary"></li>
                                    <li>
                                        <form action="{{ route('instructor.courses.duplicate', $course->id) }}" method="POST">@csrf
                                            <button type="submit" class="dropdown-item text-white"><i class="fas fa-copy me-2"></i>Dupliquer formation</button>
                                        </form>
                                    </li>
                                    @if($course->status !== 'archived')
                                        <li>
                                            <form action="{{ route('instructor.courses.archive', $course->id) }}" method="POST">@csrf
                                                <button type="submit" class="dropdown-item text-warning"><i class="fas fa-archive me-2"></i>Archiver</button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <form action="{{ route('instructor.courses.restore', $course->id) }}" method="POST">@csrf
                                                <button type="submit" class="dropdown-item text-info"><i class="fas fa-undo me-2"></i>Restaurer Brouillon</button>
                                            </form>
                                        </li>
                                    @endif
                                    <li>
                                        <form action="{{ route('instructor.courses.delete', $course->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette formation ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Supprimer</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-graduation-cap d-block mb-3" style="font-size: 2.5rem;"></i>
                            Aucune formation trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $courses->links() }}
    </div>
</div>
@endsection
