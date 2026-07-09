@extends('layouts.admin')
@section('title', 'Profil Utilisateur : ' . $user->full_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
    <li class="breadcrumb-item active">{{ $user->full_name }}</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Sidebar Infos Profil -->
    <div class="col-lg-4">
        <div class="card-cabform text-center p-4">
            <div class="user-avatar mx-auto mb-3" style="width: 90px; height: 90px; font-size: 2.2rem;">
                {{ $user->initials }}
            </div>
            <h5 class="fw-700 mb-1">{{ $user->full_name }}</h5>
            <p class="text-cb-muted small mb-3">{{ $user->email }}</p>
            
            <div class="d-flex justify-content-center gap-2 mb-4">
                @foreach($user->roles as $role)
                    <span class="badge-cabform badge-primary">{{ ucfirst($role->name) }}</span>
                @endforeach
                <span class="badge-cabform {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>

            <div class="row g-2 text-center pt-3 border-top border-cb-glass-border">
                <div class="col-4">
                    <h6 class="fw-700 mb-0" style="color: var(--cb-primary);">{{ $enrollments->count() }}</h6>
                    <span class="text-cb-muted" style="font-size: 0.75rem;">Formations</span>
                </div>
                <div class="col-4 border-start border-end border-cb-glass-border">
                    <h6 class="fw-700 mb-0 text-success">{{ $certificates->count() }}</h6>
                    <span class="text-cb-muted" style="font-size: 0.75rem;">Certificats</span>
                </div>
                <div class="col-4">
                    <h6 class="fw-700 mb-0 text-warning">{{ $payments->count() }}</h6>
                    <span class="text-cb-muted" style="font-size: 0.75rem;">Paiements</span>
                </div>
            </div>
        </div>

        <!-- Actions rapides de session / statut -->
        <div class="card-cabform p-3 mt-4">
            <h6 class="fw-700 text-cb-primary mb-3">Sécurité & Session</h6>
            
            <form action="{{ route('admin.users.logout-sessions', $user->id) }}" method="POST" class="mb-3" onsubmit="return confirm('Voulez-vous vraiment déconnecter de force cet utilisateur de toutes ses sessions actives ?');">
                @csrf
                <button type="submit" class="btn btn-cabform btn-cabform-outline-danger btn-cabform-sm w-100 text-start">
                    <i class="fas fa-sign-out-alt me-2"></i>Déconnecter les sessions
                </button>
            </form>

            <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm w-100 text-start" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                <i class="fas fa-toggle-on me-2"></i>Changer statut / Suspendre
            </button>
        </div>
    </div>

    <!-- Contenu Principal avec Onglets -->
    <div class="col-lg-8">
        <div class="card-cabform p-0">
            <!-- Navigation des Onglets -->
            <ul class="nav nav-tabs nav-tabs-cabform p-3 pb-0" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">Général</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab">Formations</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Paiements</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab">Certificats</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab">Permissions (Override)</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">Activité</button>
                </li>
            </ul>

            <!-- Contenu des Onglets -->
            <div class="tab-content p-4" id="profileTabsContent">
                <!-- Onglet Général -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-cb-muted mb-1">Prénom</label>
                            <p class="fw-600 mb-0">{{ $user->first_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-cb-muted mb-1">Nom</label>
                            <p class="fw-600 mb-0">{{ $user->last_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-cb-muted mb-1">E-mail</label>
                            <p class="fw-600 mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-cb-muted mb-1">Téléphone</label>
                            <p class="fw-600 mb-0">{{ $user->phone ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="small text-cb-muted mb-1">Biographie</label>
                            <p class="mb-0 text-cb-muted">{{ $user->profile->bio ?? 'Aucune biographie.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Onglet Formations -->
                <div class="tab-pane fade" id="courses" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-cabform mb-0">
                            <thead>
                                <tr>
                                    <th>Formation</th>
                                    <th>Date d'inscription</th>
                                    <th>Progression</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $enrollment)
                                <tr>
                                    <td><span class="fw-600">{{ $enrollment->course->title }}</span></td>
                                    <td>{{ $enrollment->created_at?->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress w-100" style="height: 6px;">
                                                <div class="progress-bar bg-cb-primary" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                            </div>
                                            <span class="small fw-600">{{ $enrollment->progress_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-cabform {{ $enrollment->status === 'completed' ? 'badge-success' : 'badge-primary' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-cb-muted py-3">Aucune inscription enregistrée.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Onglet Paiements -->
                <div class="tab-pane fade" id="payments" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-cabform mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Canal</th>
                                    <th>Statut</th>
                                    <th>Facture</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at?->format('d/m/Y') }}</td>
                                    <td class="fw-600">{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</td>
                                    <td><span class="badge-cabform badge-primary">{{ strtoupper($payment->channel) }}</span></td>
                                    <td>
                                        <span class="badge-cabform {{ $payment->status === 'completed' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->order)
                                            <a href="#" class="btn btn-cabform-glass btn-cabform-sm"><i class="fas fa-file-pdf text-danger me-1"></i>Facture</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-cb-muted py-3">Aucun paiement enregistré.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Onglet Certificats -->
                <div class="tab-pane fade" id="certificates" role="tabpanel">
                    <div class="row g-3">
                        @forelse($certificates as $certificate)
                        <div class="col-md-6">
                            <div class="border border-cb-glass-border p-3 rounded-cb d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-600 small">{{ $certificate->course->title }}</div>
                                    <div class="text-cb-muted" style="font-size: 0.75rem;">N° : {{ $certificate->certificate_number }}</div>
                                    <div class="text-cb-muted" style="font-size: 0.75rem;">Date : {{ $certificate->issued_at?->format('d/m/Y') }}</div>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-cabform-glass btn-cabform-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ asset('storage/' . $certificate->pdf_path) }}" target="_blank"><i class="fas fa-eye me-2"></i>Voir PDF</a>
                                        <form method="POST" action="{{ route('admin.certificates.revoke', $certificate->id) }}" onsubmit="return confirm('Confirmer la révocation du certificat ?');">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-ban me-2"></i>Révoquer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-center text-cb-muted py-3">Aucun certificat obtenu.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Onglet Permissions (Override) -->
                <div class="tab-pane fade" id="permissions" role="tabpanel">
                    <form action="{{ route('admin.users.permissions', $user->id) }}" method="POST">
                        @csrf
                        <p class="text-cb-muted small mb-4">Attribuez des permissions spécifiques à cet utilisateur (ces permissions surchargeront ses rôles) :</p>
                        
                        <div class="row g-2 mb-4">
                            @foreach($permissions as $permission)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" {{ in_array($permission->name, $directPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                        {{ $permission->name }} 
                                        @if(in_array($permission->name, $userPermissions) && !in_array($permission->name, $directPermissions))
                                            <span class="text-cb-muted" style="font-size: 0.7rem;">(héritée du rôle)</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Enregistrer les permissions</button>
                    </form>
                </div>

                <!-- Onglet Activité -->
                <div class="tab-pane fade" id="activity" role="tabpanel">
                    <div class="timeline-cabform">
                        @forelse($activity as $log)
                        <div class="timeline-item mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-600 small">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                <span class="text-cb-muted small" style="font-size: 0.75rem;">{{ $log->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-cb-muted mb-0 small">{{ $log->details }}</p>
                        </div>
                        @empty
                        <p class="text-center text-cb-muted py-3">Aucun log d'activité disponible.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Changement Statut -->
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
            @csrf
            <div class="modal-content border-0 rounded-cb shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700">Modifier le statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-600">Statut</label>
                        <select name="status" class="form-select form-control-cabform" required>
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-600">Motif du changement</label>
                        <textarea name="reason" class="form-control form-control-cabform" rows="3" placeholder="Indiquer la raison..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Confirmer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
