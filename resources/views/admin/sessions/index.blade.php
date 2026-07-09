@extends('layouts.admin')
@section('title', 'Gestion des Sessions & Cohortes')
@section('breadcrumb')
    <li class="breadcrumb-item active">Sessions</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-700 mb-0"><i class="fas fa-calendar-alt text-cb-primary me-2"></i>Sessions & Cohortes</h4>
    <a href="{{ route('admin.sessions.create') }}" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">
        <i class="fas fa-plus me-1"></i>Nouvelle session
    </a>
</div>

<!-- KPIs Cohortes -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Total Sessions</div>
            <h4 class="fw-700 mb-0">{{ $totalSessions }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Sessions Actives</div>
            <h4 class="fw-700 mb-0 text-success">{{ $activeSessions }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Clôturées</div>
            <h4 class="fw-700 mb-0 text-cb-primary">{{ $completedSessions }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-cabform p-3">
            <div class="small text-cb-muted fw-600 mb-1">Taux de Remplissage</div>
            <h4 class="fw-700 mb-0 text-warning">{{ $occupancyRate }}%</h4>
        </div>
    </div>
</div>

<!-- Filtres et Recherche -->
<div class="card-cabform p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-lg-4">
            <label class="form-label text-cb-muted small fw-600 mb-1">Recherche</label>
            <input type="text" name="search" class="form-control form-control-cabform" placeholder="Nom de session..." value="{{ request('search') }}">
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Statut</label>
            <select name="status" class="form-control form-control-cabform">
                <option value="">Tous les statuts</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>À venir</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Clôturée</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
        </div>
    </form>
</div>

<!-- Liste des Sessions -->
<div class="card-cabform">
    <div class="table-responsive">
        <table class="table table-cabform mb-0">
            <thead>
                <tr>
                    <th>Session / Cohorte</th>
                    <th>Formation</th>
                    <th>Date début</th>
                    <th>Capacité</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr>
                    <td><span class="fw-600">{{ $session->name }}</span></td>
                    <td>{{ $session->course->title ?? '-' }}</td>
                    <td>{{ $session->start_date?->format('d/m/Y') }}</td>
                    <td>
                        <span class="fw-600 text-cb-primary">{{ $session->enrolled_count }}</span>
                        <span class="text-cb-muted">/ {{ $session->max_students ?? '∞' }}</span>
                    </td>
                    <td>
                        @if($session->status === 'active')
                            <span class="badge-cabform badge-success">Active</span>
                        @elseif($session->status === 'upcoming')
                            <span class="badge-cabform badge-warning">À venir</span>
                        @elseif($session->status === 'completed')
                            <span class="badge-cabform badge-primary">Clôturée</span>
                        @else
                            <span class="badge-cabform badge-danger">Annulée</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-cabform-glass btn-cabform-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('admin.sessions.show', $session->id) }}"><i class="fas fa-eye me-2"></i>Détails</a>
                                <a class="dropdown-item" href="{{ route('admin.sessions.edit', $session->id) }}"><i class="fas fa-edit me-2"></i>Modifier</a>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('dup-session-{{ $session->id }}').submit();"><i class="fas fa-copy me-2"></i>Dupliquer</a>
                                @if($session->status === 'active')
                                    <form method="POST" action="{{ route('admin.sessions.close', $session->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-warning"><i class="fas fa-lock me-2"></i>Clôturer</button>
                                    </form>
                                @endif
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('admin.sessions.destroy', $session->id) }}" onsubmit="return confirm('Confirmer la suppression définitive de cette session ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i>Supprimer</button>
                                </form>
                            </div>
                        </div>

                        <!-- Form secrète pour duplication -->
                        <form id="dup-session-{{ $session->id }}" action="{{ route('admin.sessions.duplicate', $session->id) }}" method="POST" class="d-none">@csrf</form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-cb-muted py-4">Aucune session enregistrée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $sessions->withQueryString()->links() }}</div>
</div>
@endsection
