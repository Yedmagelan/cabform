@extends('layouts.instructor')

@section('title', 'Sessions de formation')
@section('page_title', 'Sessions Cohortes')

@section('content')
<div class="card card-instructor p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Sessions et Cohortes : {{ $course->title }}</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Organisez les apprenants en groupes de travail avec dates limites.</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('instructor.sessions.create', $course->id) }}" class="btn btn-premium btn-sm"><i class="fas fa-plus me-2"></i>Nouvelle session</a>
            <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
        </div>
    </div>

    <!-- Table lists of sessions -->
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nom de la session</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Nombre d'inscrits</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr>
                        <td><strong class="text-white">{{ $session->name }}</strong></td>
                        <td>{{ $session->start_date }}</td>
                        <td>{{ $session->end_date ?? 'Self-paced (continu)' }}</td>
                        <td>{{ $session->enrolled_count }} / {{ $session->max_students ?? 'Illimité' }}</td>
                        <td>
                            <span class="badge bg-{{ $session->status === 'active' ? 'success' : ($session->status === 'completed' ? 'secondary' : 'warning') }}">
                                {{ match($session->status) {
                                    'upcoming' => 'À venir',
                                    'active' => 'Active',
                                    'completed' => 'Clôturée',
                                    'cancelled' => 'Annulée',
                                    default => $session->status
                                } }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('instructor.sessions.show', [$course->id, $session->id]) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-users-cog me-1"></i> Gérer</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Aucune session configurée pour cette formation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
