@extends('layouts.admin')
@section('title', 'Catégories')
@section('breadcrumb')
    <li class="breadcrumb-item active">Catégories</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-700 mb-0"><i class="fas fa-layer-group text-cb-primary me-2"></i>Catégories</h4>
    <button type="button" class="btn btn-cabform btn-cabform-primary btn-cabform-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="fas fa-plus me-1"></i>Ajouter une catégorie
    </button>
</div>

<div class="card-cabform">
    <div class="table-responsive">
        <table class="table table-cabform mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Icône</th>
                    <th>Ordre</th>
                    <th>Statut</th>
                    <th width="100"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="fw-600">{{ $cat->name }}</td>
                    <td class="text-cb-muted">{{ $cat->slug }}</td>
                    <td>
                        <span class="btn btn-cabform-glass btn-cabform-sm py-1 px-2">
                            <i class="{{ $cat->icon ?? 'fas fa-folder' }} text-cb-primary"></i>
                        </span>
                    </td>
                    <td>{{ $cat->sort_order }}</td>
                    <td>
                        <span class="badge-cabform {{ $cat->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $cat->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-cabform-glass btn-cabform-sm text-cb-primary" onclick="openEditModal({{ json_encode($cat) }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.categories.delete', $cat->id) }}" onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-cabform-glass btn-cabform-sm text-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-cb-muted py-4">Aucune catégorie enregistrée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter une Catégorie -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Créer une catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Nom de la catégorie</label>
                        <input type="text" name="name" class="form-control form-control-cabform" placeholder="Ex: Développement Web" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Icône (FontAwesome classe)</label>
                        <input type="text" name="icon" class="form-control form-control-cabform" value="fas fa-folder" placeholder="Ex: fas fa-code">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Ordre de tri</label>
                        <input type="number" name="sort_order" class="form-control form-control-cabform" value="0" min="0" required>
                    </div>
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" value="1" checked>
                        <label class="form-check-label small fw-600" for="create_is_active">Catégorie active</label>
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

<!-- Modal Modifier une Catégorie -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="edit-category-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Modifier la catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Nom de la catégorie</label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-cabform" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Icône (FontAwesome classe)</label>
                        <input type="text" name="icon" id="edit_icon" class="form-control form-control-cabform">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Ordre de tri</label>
                        <input type="number" name="sort_order" id="edit_sort_order" class="form-control form-control-cabform" min="0" required>
                    </div>
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                        <label class="form-check-label small fw-600" for="edit_is_active">Catégorie active</label>
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
    function openEditModal(category) {
        $('#edit_name').val(category.name);
        $('#edit_icon').val(category.icon);
        $('#edit_sort_order').val(category.sort_order);
        $('#edit_is_active').prop('checked', category.is_active);
        
        $('#edit-category-form').attr('action', '/admin/categories/' + category.id);
        
        var myModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        myModal.show();
    }
</script>
@endpush
