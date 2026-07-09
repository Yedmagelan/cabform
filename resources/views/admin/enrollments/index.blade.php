@extends('layouts.admin')
@section('title', 'Mes apprenants')
@section('breadcrumb')
    <li class="breadcrumb-item active">Mes apprenants</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-700 mb-0"><i class="fas fa-user-graduate text-cb-primary me-2"></i>Mes apprenants</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.enrollments.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm">
            <i class="fas fa-file-excel me-1"></i>Exporter Excel
        </a>
        <a href="{{ route('admin.enrollments.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm">
            <i class="fas fa-file-pdf me-1"></i>Exporter PDF
        </a>
        <button type="button" class="btn btn-cabform btn-cabform-primary btn-cabform-sm" data-bs-toggle="modal" data-bs-target="#createEnrollmentModal">
            <i class="fas fa-plus me-1"></i>Inscrire un apprenant
        </button>
    </div>
</div>

<!-- Filtres et Recherche -->
<div class="card-cabform p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-lg-4">
            <label class="form-label text-cb-muted small fw-600 mb-1">Recherche apprenant</label>
            <input type="text" name="search" class="form-control form-control-cabform" placeholder="Nom, email..." value="{{ request('search') }}">
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Formation</label>
            <select name="course_id" class="form-control form-control-cabform">
                <option value="">Toutes les formations</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Statut</label>
            <select name="status" class="form-control form-control-cabform">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complété</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
        </div>
    </form>
</div>

<!-- Liste des Inscriptions -->
<div class="card-cabform">
    <div class="table-responsive">
        <table class="table table-cabform mb-0">
            <thead>
                <tr>
                    <th>Apprenant</th>
                    <th>Formation</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Date d'inscription</th>
                    <th width="100"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $e)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:30px;height:30px;font-size:0.7rem;">{{ $e->user->initials ?? 'AP' }}</div>
                            <span class="fw-600">{{ $e->user->full_name ?? '-' }}</span>
                        </div>
                    </td>
                    <td>{{ Str::limit($e->course->title ?? '-', 40) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress w-100" style="height: 6px; min-width: 100px;">
                                <div class="progress-bar bg-cb-primary" role="progressbar" style="width: {{ $e->progress_percentage }}%"></div>
                            </div>
                            <span class="small fw-600">{{ number_format($e->progress_percentage, 0) }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($e->status === 'active')
                            <span class="badge-cabform badge-success">Actif</span>
                        @elseif($e->status === 'completed')
                            <span class="badge-cabform badge-primary">Complété</span>
                        @else
                            <span class="badge-cabform badge-danger">Suspendu</span>
                        @endif
                    </td>
                    <td class="text-cb-muted" style="font-size:0.85rem;">{{ $e->created_at?->format('d/m/Y') }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-cabform-glass btn-cabform-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <button class="dropdown-item" onclick="openEditModal({{ json_encode($e) }})"><i class="fas fa-edit me-2"></i>Modifier</button>
                                
                                @if($e->status === 'completed')
                                    <form method="POST" action="{{ route('admin.certificates.generate', $e->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-award me-2"></i>Générer Certificat</button>
                                    </form>
                                @endif
                                
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('admin.enrollments.delete', $e->id) }}" onsubmit="return confirm('Voulez-vous vraiment désinscrire cet apprenant de cette formation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-user-times me-2"></i>Désinscrire</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-cb-muted py-4">Aucune inscription enregistrée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $enrollments->withQueryString()->links() }}</div>
</div>

<!-- Modal Inscrire un Apprenant -->
<div class="modal fade" id="createEnrollmentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.enrollments.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Inscrire un apprenant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Sélectionner l'apprenant</label>
                        <select name="user_id" class="form-select form-control-cabform shadow-none" required>
                            <option value="">Sélectionner un utilisateur...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Sélectionner la formation</label>
                        <select name="course_id" class="form-select form-control-cabform shadow-none" required>
                            <option value="">Sélectionner une formation...</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Inscrire</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier une Inscription -->
<div class="modal fade" id="editEnrollmentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="edit-enrollment-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Modifier l'inscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Progression (%)</label>
                        <input type="number" name="progress_percentage" id="edit_progress" class="form-control form-control-cabform" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Statut</label>
                        <select name="status" id="edit_status" class="form-select form-control-cabform shadow-none" required>
                            <option value="active">Actif</option>
                            <option value="completed">Complété</option>
                            <option value="suspended">Suspendu</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(enrollment) {
        $('#edit_progress').val(enrollment.progress_percentage);
        $('#edit_status').val(enrollment.status);
        
        $('#edit-enrollment-form').attr('action', '/admin/enrollments/' + enrollment.id);
        
        var myModal = new bootstrap.Modal(document.getElementById('editEnrollmentModal'));
        myModal.show();
    }
</script>
@endpush
