@extends('layouts.admin')
@section('title', 'Détails de la Session : ' . $session->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sessions.index') }}">Sessions</a></li>
    <li class="breadcrumb-item active">{{ $session->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-700 mb-1">{{ $session->name }}</h4>
        <span class="text-cb-muted small">Formation : <a href="{{ route('admin.courses.show', $session->course_id) }}" class="text-cb-primary fw-600">{{ $session->course->title ?? '-' }}</a></span>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('admin.sessions.duplicate', $session->id) }}">
            @csrf
            <button type="submit" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm">
                <i class="fas fa-copy me-1"></i>Dupliquer
            </button>
        </form>
        @if($session->status === 'active')
            <form method="POST" action="{{ route('admin.sessions.close', $session->id) }}" onsubmit="return confirm('Voulez-vous vraiment clôturer cette session ?');">
                @csrf
                <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">
                    <i class="fas fa-lock me-1"></i>Clôturer la session
                </button>
            </form>
        @endif
    </div>
</div>

<!-- KPIs Cohorte -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Apprenants Inscrits</div>
            <h4 class="fw-700 mb-0">
                {{ $session->enrolled_count }}
                <span class="text-cb-muted" style="font-size: 1rem;">/ {{ $session->max_students ?? '∞' }}</span>
            </h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Taux de Remplissage</div>
            <h4 class="fw-700 mb-0 text-success">
                {{ $session->max_students ? round(($session->enrolled_count / $session->max_students) * 100, 1) : 100 }}%
            </h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Progression Moyenne</div>
            <h4 class="fw-700 mb-0 text-warning">{{ round($avgProgress, 1) }}%</h4>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Informations détaillées -->
    <div class="col-lg-4">
        <div class="card-cabform p-4">
            <h5 class="fw-700 text-cb-primary mb-3">Informations</h5>
            
            <div class="mb-3">
                <label class="small text-cb-muted d-block mb-1">Description</label>
                <p class="mb-0 text-cb-muted">{{ $session->description ?? 'Aucune description spécifiée.' }}</p>
            </div>
            
            <div class="mb-3 border-top border-cb-glass-border pt-3">
                <label class="small text-cb-muted d-block mb-1">Date de début</label>
                <p class="fw-600 mb-0">{{ $session->start_date?->format('d/m/Y') }}</p>
            </div>

            <div class="mb-3">
                <label class="small text-cb-muted d-block mb-1">Date de fin</label>
                <p class="fw-600 mb-0">{{ $session->end_date?->format('d/m/Y') ?? 'Non définie' }}</p>
            </div>

            <div class="mb-3">
                <label class="small text-cb-muted d-block mb-1">Limite d'inscription</label>
                <p class="fw-600 mb-0">{{ $session->enrollment_deadline?->format('d/m/Y') ?? 'Aucune limite' }}</p>
            </div>

            <div class="border-top border-cb-glass-border pt-3">
                <label class="small text-cb-muted d-block mb-1">Statut actuel</label>
                @if($session->status === 'active')
                    <span class="badge-cabform badge-success">Active</span>
                @elseif($session->status === 'upcoming')
                    <span class="badge-cabform badge-warning">À venir</span>
                @elseif($session->status === 'completed')
                    <span class="badge-cabform badge-primary">Clôturée</span>
                @else
                    <span class="badge-cabform badge-danger">Annulée</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Liste des apprenants de la cohorte -->
    <div class="col-lg-8">
        <div class="card-cabform p-4">
            <h5 class="fw-700 text-cb-primary mb-3">Apprenants de la cohorte</h5>
            
            <div class="table-responsive">
                <table class="table table-cabform mb-0">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Email</th>
                            <th>Date d'inscription</th>
                            <th>Progression</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width:28px;height:28px;font-size:0.65rem;">{{ $enrollment->user->initials }}</div>
                                    <span class="fw-600">{{ $enrollment->user->full_name }}</span>
                                </div>
                            </td>
                            <td>{{ $enrollment->user->email }}</td>
                            <td>{{ $enrollment->created_at?->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress w-100" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="small fw-600">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-cb-muted py-4">Aucun apprenant inscrit dans cette cohorte.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pt-3">{{ $enrollments->links() }}</div>
        </div>
    </div>
</div>
@endsection
