@extends('layouts.admin')
@section('title', 'Gestion des Formations')
@section('breadcrumb')
    <li class="breadcrumb-item active">Formations</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-700 mb-0"><i class="fas fa-book-open text-cb-primary me-2"></i>Gestion des formations</h4>
    <a href="#" class="btn btn-cabform btn-cabform-primary btn-cabform-sm" data-bs-toggle="modal" data-bs-target="#createCourseModal">
        <i class="fas fa-plus me-1"></i>Nouvelle formation
    </a>
</div>

<!-- Filtres et Recherche -->
<div class="card-cabform p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-lg-4">
            <label class="form-label text-cb-muted small fw-600 mb-1">Recherche</label>
            <input type="text" name="search" class="form-control form-control-cabform" placeholder="Rechercher..." value="{{ request('search') }}">
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Catégorie</label>
            <select name="category" class="form-control form-control-cabform">
                <option value="">Toutes catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Statut</label>
            <select name="status" class="form-control form-control-cabform">
                <option value="">Tous les statuts</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivé</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
        </div>
    </form>
</div>

<!-- Formulaire pour Bulk Actions -->
<form id="bulk-action-form" action="{{ route('admin.courses.bulk') }}" method="POST">
    @csrf
    
    <!-- Bulk Actions Toolbar -->
    <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-light rounded border border-cb-glass-border d-none" id="bulk-actions-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-600 text-cb-primary" id="selected-count">0</span> formations sélectionnées
        </div>
        <div class="d-flex align-items-center gap-2">
            <select name="action" id="bulk-action-select" class="form-select form-select-sm w-auto" required>
                <option value="">Choisir une action...</option>
                <option value="publish">Publier les formations</option>
                <option value="archive">Archiver les formations</option>
                <option value="change_category">Changer de catégorie</option>
                <option value="delete">Supprimer</option>
            </select>
            
            <select name="category_id" id="bulk-category-select" class="form-select form-select-sm w-auto d-none">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            
            <button type="button" class="btn btn-cabform btn-cabform-primary btn-cabform-sm" onclick="confirmBulkAction()">Appliquer</button>
        </div>
    </div>

    <!-- Table des Formations -->
    <div class="card-cabform">
        <div class="table-responsive">
            <table class="table table-cabform mb-0">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                        </th>
                        <th>Formation</th>
                        <th>Catégorie</th>
                        <th>Formateur</th>
                        <th>Prix</th>
                        <th>Inscrits</th>
                        <th>Version</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td>
                            <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" class="form-check-input course-checkbox">
                        </td>
                        <td>
                            <span class="fw-600">{{ Str::limit($course->title, 40) }}</span>
                        </td>
                        <td><span class="badge-cabform badge-primary">{{ $course->category->name ?? '-' }}</span></td>
                        <td>{{ $course->instructor->full_name ?? '-' }}</td>
                        <td class="fw-600">{{ $course->formatted_price }}</td>
                        <td>
                            <a href="{{ route('admin.enrollments.index', ['course_id' => $course->id]) }}" class="badge-cabform bg-cb-primary text-white text-decoration-none">
                                <i class="fas fa-user-graduate me-1"></i>{{ $course->enrollments_count }}
                            </a>
                        </td>
                        <td>v{{ $course->version }}</td>
                        <td>
                            @if($course->status === 'published')
                                <span class="badge-cabform badge-success">Publié</span>
                            @elseif($course->status === 'draft')
                                <span class="badge-cabform badge-warning">Brouillon</span>
                            @elseif($course->status === 'archived')
                                <span class="badge-cabform badge-danger">Archivé</span>
                            @else
                                <span class="badge-cabform badge-secondary">{{ ucfirst($course->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-cabform-glass btn-cabform-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.courses.show', $course->id) }}"><i class="fas fa-eye me-2"></i>Tableau de bord</a>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('dup-form-{{ $course->id }}').submit();"><i class="fas fa-copy me-2"></i>Dupliquer</a>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('ver-form-{{ $course->id }}').submit();"><i class="fas fa-code-branch me-2"></i>Nouvelle version</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.courses.delete', $course->id) }}" onsubmit="return confirm('Confirmer la suppression définitive de cette formation ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i>Supprimer</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Forms secrets pour Duplication & Versioning -->
                            <form id="dup-form-{{ $course->id }}" action="{{ route('admin.courses.duplicate', $course->id) }}" method="POST" class="d-none">@csrf</form>
                            <form id="ver-form-{{ $course->id }}" action="{{ route('admin.courses.version', $course->id) }}" method="POST" class="d-none">@csrf</form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-cb-muted py-4">Aucune formation trouvée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $courses->withQueryString()->links() }}</div>
    </div>
</form>

<!-- Modal Nouvelle Formation -->
<div class="modal fade" id="createCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Créer une formation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Titre de la formation</label>
                        <input type="text" name="title" class="form-control form-control-cabform" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Catégorie</label>
                        <select name="category_id" class="form-select form-control-cabform" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Formateur / Instructeur</label>
                        <select name="instructor_id" class="form-select form-control-cabform" required>
                            @foreach(\App\Models\User::role('formateur')->get() as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Tarif (XOF)</label>
                        <input type="number" name="price" class="form-control form-control-cabform" value="0" min="0" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Créer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Selection globale
        $('#select-all-checkbox').change(function() {
            $('.course-checkbox').prop('checked', $(this).prop('checked')).trigger('change');
        });

        // Toggle Toolbar
        $('.course-checkbox, #select-all-checkbox').change(function() {
            var selectedCount = $('.course-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#selected-count').text(selectedCount);
                $('#bulk-actions-bar').removeClass('d-none');
            } else {
                $('#bulk-actions-bar').addClass('d-none');
            }
        });

        // Toggle select de catégorie
        $('#bulk-action-select').change(function() {
            if ($(this).val() === 'change_category') {
                $('#bulk-category-select').removeClass('d-none');
            } else {
                $('#bulk-category-select').addClass('d-none');
            }
        });
    });

    function confirmBulkAction() {
        var action = $('#bulk-action-select').val();
        if (!action) return;
        var actionText = $('#bulk-action-select option:selected').text();
        if (confirm("Confirmez-vous l'action en masse : '" + actionText + "' ?")) {
            $('#bulk-action-form').submit();
        }
    }
</script>
@endpush
