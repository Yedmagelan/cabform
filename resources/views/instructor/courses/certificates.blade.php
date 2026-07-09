@extends('layouts.instructor')

@section('title', 'Certificats Délivrés')
@section('page_title', 'Certificats Délivrés')

@section('content')
<div class="card card-instructor p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Certificats de formation : {{ $course->title }}</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Suivez et révoquez les certificats de compétences remis.</span>
        </div>
        <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
    </div>

    <!-- Table of certificates -->
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Apprenant</th>
                    <th>Date d'obtention</th>
                    <th>Numéro unique</th>
                    <th>Score final</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 32px; height: 32px;">{{ $cert->user->initials }}</div>
                                <div>
                                    <div class="fw-bold text-white">{{ $cert->user->full_name }}</div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $cert->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $cert->issued_at->format('d/m/Y') }}</td>
                        <td><code class="text-indigo fw-bold" style="color: #818cf8;">{{ $cert->certificate_number }}</code></td>
                        <td>{{ $cert->final_score ?? '-' }}%</td>
                        <td>
                            <span class="badge bg-{{ $cert->status === 'generated' ? 'success' : 'danger' }}">
                                {{ $cert->status === 'generated' ? 'Valide' : 'Révoqué' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($cert->status === 'generated')
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ asset('storage/' . $cert->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-eye"></i> PDF</a>
                                    
                                    <!-- Revoke button triggers modal or form -->
                                    <button type="button" class="btn btn-sm btn-outline-danger border-danger text-danger btn-revoke" data-id="{{ $cert->id }}" data-bs-toggle="modal" data-bs-target="#revokeModal"><i class="fas fa-ban"></i> Révoquer</button>
                                </div>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">{{ $cert->revocation_reason }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Aucun certificat délivré pour cette formation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $certificates->links() }}
    </div>
</div>

<!-- =========================================================================
     MODAL REVOCATION CERTIFICAT
     ========================================================================= -->
<div class="modal fade" id="revokeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Révoquer le certificat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="revoke-cert-form" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted" style="font-size: 0.9rem;">Attention : Cette action est définitive. L'apprenant concerné ne pourra plus faire valoir ce certificat en ligne.</p>
                    <div class="mb-3">
                        <label class="form-label">Motif de la révocation</label>
                        <select name="reason" class="form-select bg-dark border-secondary text-white" required>
                            <option value="Triche / Non-respect des règles d'évaluation">Triche / Non-respect des règles d'évaluation</option>
                            <option value="Erreur administrative lors de la génération">Erreur administrative lors de la génération</option>
                            <option value="Rétractation de paiement de formation">Rétractation de paiement de formation</option>
                            <option value="Autre motif">Autre motif (Préciser ci-dessous)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer la Révocation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-revoke').on('click', function() {
            const certId = $(this).data('id');
            $('#revoke-cert-form').attr('action', `/instructor/courses/{{ $course->id }}/certificates/${certId}/revoke`);
        });
    });
</script>
@endpush
