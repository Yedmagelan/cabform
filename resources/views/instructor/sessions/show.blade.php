@extends('layouts.instructor')

@section('title', 'Gestion de la Session')
@section('page_title', 'Gestion de Cohorte')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="row g-4 mb-4">
    <!-- Header Summary Details Card -->
    <div class="col-12">
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <span class="badge bg-indigo-subtle text-indigo mb-2" style="background: rgba(99,102,241,0.15); color: #818cf8;">Session: {{ $session->name }}</span>
                    <h4 class="fw-bold text-white mb-2">{{ $course->title }}</h4>
                    <span class="text-muted d-block" style="font-size: 0.9rem;"><i class="fas fa-calendar-day me-2"></i>Début : {{ $session->start_date }} &bull; Fin : {{ $session->end_date ?? 'Self-paced (continu)' }}</span>
                    <span class="text-muted d-block mt-1" style="font-size: 0.9rem;"><i class="fas fa-users me-2"></i>Inscrits : {{ $session->enrolled_count }} / {{ $session->max_students ?? 'Illimité' }}</span>
                </div>
                <div class="d-flex gap-2">
                    @if($session->status !== 'completed')
                        <form action="{{ route('instructor.sessions.close', [$course->id, $session->id]) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment clôturer cette session ? Tous les devoirs seront verrouillés.');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning"><i class="fas fa-lock me-2"></i>Clôturer la session</button>
                        </form>
                    @else
                        <span class="badge bg-secondary px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-lock me-1"></i> Clôturée</span>
                    @endif
                    <a href="{{ route('instructor.sessions.index', $course->id) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left panel: Enrollments List -->
    <div class="col-lg-8">
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4">Liste des apprenants inscrits</h5>
            
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Inscrit le</th>
                            <th>Progression</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar" style="width: 32px; height: 32px;">{{ $enrollment->user->initials }}</div>
                                        <div>
                                            <div class="fw-bold text-white">{{ $enrollment->user->full_name }}</div>
                                            <small class="text-muted">{{ $enrollment->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $enrollment->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2" style="width: 140px;">
                                        <div class="progress w-100" style="height: 6px; background: rgba(255,255,255,0.05);">
                                            <div class="progress-bar bg-indigo" style="width: {{ $enrollment->progress_percentage }}%;"></div>
                                        </div>
                                        <span class="fw-bold" style="font-size: 0.8rem;">{{ round($enrollment->progress_percentage) }}%</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('instructor.sessions.remove-student', [$course->id, $session->id, $enrollment->id]) }}" method="POST" onsubmit="return confirm('Désinscrire cet apprenant de la session ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none"><i class="fas fa-user-minus"></i> Retirer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Aucun apprenant inscrit pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>

    <!-- Right panel: Manual Enrollment -->
    <div class="col-lg-4">
        @if($session->status !== 'completed')
            <div class="card card-instructor p-4 mb-4">
                <h5 class="fw-bold text-white mb-4"><i class="fas fa-user-plus text-indigo me-2"></i>Inscrire un apprenant</h5>
                
                <form action="{{ route('instructor.sessions.add-student', [$course->id, $session->id]) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label text-white">Rechercher l'apprenant</label>
                        <select name="user_id" class="form-select select2-students bg-dark text-white" style="width: 100%;" required>
                            <option value=""></option>
                            @foreach($allStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-premium w-100 py-2">Inscrire l'étudiant</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-students').select2({
            placeholder: "Saisir un nom ou email..."
        });
    });
</script>
@endpush
