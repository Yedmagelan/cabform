@extends('layouts.admin')
@section('title', 'Gestion des Utilisateurs')
@section('breadcrumb')
    <li class="breadcrumb-item active">Utilisateurs</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-700 mb-0"><i class="fas fa-users text-cb-primary me-2"></i>Gestion des utilisateurs</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-download me-1"></i>Exporter
        </button>
        <button type="button" class="btn btn-cabform btn-cabform-primary btn-cabform-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-1"></i>Nouvel utilisateur
        </button>
    </div>
</div>

<!-- Filtres et Recherche -->
<div class="card-cabform p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-lg-4">
            <label class="form-label text-cb-muted small fw-600 mb-1">Recherche</label>
            <input type="text" name="search" class="form-control form-control-cabform" placeholder="Nom, email..." value="{{ request('search') }}">
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Rôle</label>
            <select name="role" class="form-control form-control-cabform">
                <option value="">Tous les rôles</option>
                <option value="administrateur" {{ request('role') === 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                <option value="formateur" {{ request('role') === 'formateur' ? 'selected' : '' }}>Formateur</option>
                <option value="apprenant" {{ request('role') === 'apprenant' ? 'selected' : '' }}>Apprenant</option>
                <option value="gestionnaire" {{ request('role') === 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
            </select>
        </div>
        <div class="col-lg-3">
            <label class="form-label text-cb-muted small fw-600 mb-1">Statut</label>
            <select name="status" class="form-control form-control-cabform">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
        </div>
    </form>
</div>

<!-- Formulaire pour Bulk Actions -->
<form id="bulk-action-form" action="{{ route('admin.users.bulk') }}" method="POST">
    @csrf
    
    <!-- Bulk Actions Tool Bar -->
    <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-light rounded border border-cb-glass-border d-none" id="bulk-actions-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-600 text-cb-primary" id="selected-count">0</span> utilisateurs sélectionnés
        </div>
        <div class="d-flex align-items-center gap-2">
            <select name="action" id="bulk-action-select" class="form-select form-select-sm w-auto" required>
                <option value="">Choisir une action...</option>
                <option value="activate">Activer les comptes</option>
                <option value="suspend">Suspendre les comptes</option>
                <option value="change_role">Changer de rôle</option>
                <option value="delete">Supprimer</option>
            </select>
            
            <select name="role" id="bulk-role-select" class="form-select form-select-sm w-auto d-none">
                <option value="apprenant">Apprenant</option>
                <option value="formateur">Formateur</option>
                <option value="gestionnaire">Gestionnaire</option>
                <option value="administrateur">Administrateur</option>
            </select>
            
            <button type="button" class="btn btn-cabform btn-cabform-primary btn-cabform-sm" onclick="confirmBulkAction()">Appliquer</button>
        </div>
    </div>

    <!-- Liste des Utilisateurs -->
    <div class="card-cabform">
        <div class="table-responsive">
            <table class="table table-cabform mb-0">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                        </th>
                        <th>Utilisateur</th>
                        <th>E-mail</th>
                        <th>Rôles</th>
                        <th>Inscriptions</th>
                        <th>Statut</th>
                        <th>Inscrit le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:34px;height:34px;font-size:0.7rem;">{{ $user->initials }}</div>
                                <span class="fw-600">{{ $user->full_name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge-cabform badge-primary">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </td>
                        <td>{{ $user->enrollments_count }}</td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge-cabform badge-success">Actif</span>
                            @elseif($user->status === 'suspended')
                                <span class="badge-cabform badge-danger">Suspendu</span>
                            @else
                                <span class="badge-cabform badge-warning">Inactif</span>
                            @endif
                        </td>
                        <td class="text-cb-muted" style="font-size:0.85rem;">{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-cabform-glass btn-cabform-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}"><i class="fas fa-eye me-2"></i>Voir le profil</a>
                                    <a class="dropdown-item" href="#" onclick="openStatusModal({{ $user->id }}, '{{ $user->status }}')"><i class="fas fa-toggle-on me-2"></i>Modifier statut</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i>Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-cb-muted py-4">Aucun utilisateur trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $users->withQueryString()->links() }}</div>
    </div>
</form>

<!-- Modal Export Avancé -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.export.advanced') }}" method="GET">
            <input type="hidden" name="role" value="{{ request('role') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Exporter les utilisateurs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-cb-muted small">Sélectionnez les colonnes à inclure dans l'export CSV :</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="id" id="col_id" checked>
                                <label class="form-check-label" for="col_id">ID</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="first_name" id="col_first" checked>
                                <label class="form-check-label" for="col_first">Prénom</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="last_name" id="col_last" checked>
                                <label class="form-check-label" for="col_last">Nom</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="email" id="col_email" checked>
                                <label class="form-check-label" for="col_email">E-mail</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="role" id="col_role" checked>
                                <label class="form-check-label" for="col_role">Rôles</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="status" id="col_status" checked>
                                <label class="form-check-label" for="col_status">Statut</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm"><i class="fas fa-file-csv me-1"></i>Générer CSV</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Changement Statut -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="status-form" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Modifier le statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Nouveau statut</label>
                        <select name="status" id="status-select" class="form-select form-control-cabform" required>
                            <option value="active">Actif</option>
                            <option value="suspended">Suspendu</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Motif du changement</label>
                        <textarea name="reason" class="form-control form-control-cabform" rows="3" placeholder="Indiquer la raison..." required></textarea>
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

<!-- Modal Nouvel Utilisateur -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Créer un nouvel utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-600">Prénom</label>
                            <input type="text" name="first_name" class="form-control form-control-cabform" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-600">Nom</label>
                            <input type="text" name="last_name" class="form-control form-control-cabform" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">E-mail</label>
                        <input type="email" name="email" class="form-control form-control-cabform" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Mot de passe</label>
                        <input type="password" name="password" class="form-control form-control-cabform" placeholder="Laissez vide pour générer automatiquement">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Rôle</label>
                        <select name="role" class="form-select form-control-cabform" required>
                            <option value="apprenant">Apprenant</option>
                            <option value="formateur">Formateur</option>
                            <option value="gestionnaire">Gestionnaire</option>
                            <option value="administrateur">Administrateur</option>
                        </select>
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
        // Toggle selection de tous les checkboxes
        $('#select-all-checkbox').change(function() {
            $('.user-checkbox').prop('checked', $(this).prop('checked')).trigger('change');
        });

        // Toggle affichage de la toolbar des bulk actions
        $('.user-checkbox, #select-all-checkbox').change(function() {
            var selectedCount = $('.user-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#selected-count').text(selectedCount);
                $('#bulk-actions-bar').removeClass('d-none');
            } else {
                $('#bulk-actions-bar').addClass('d-none');
            }
        });

        // Toggle selection du rôle dans bulk action
        $('#bulk-action-select').change(function() {
            if ($(this).val() === 'change_role') {
                $('#bulk-role-select').removeClass('d-none');
            } else {
                $('#bulk-role-select').addClass('d-none');
            }
        });
    });

    function confirmBulkAction() {
        var action = $('#bulk-action-select').val();
        if (!action) return;
        var actionText = $('#bulk-action-select option:selected').text();
        if (confirm("Confirmez-vous l'exécution de l'action : '" + actionText + "' sur tous les utilisateurs sélectionnés ?")) {
            $('#bulk-action-form').submit();
        }
    }

    function openStatusModal(userId, currentStatus) {
        $('#status-select').val(currentStatus);
        $('#status-form').attr('action', '/admin/users/' + userId + '/status');
        var myModal = new bootstrap.Modal(document.getElementById('statusModal'));
        myModal.show();
    }
</script>
@endpush
