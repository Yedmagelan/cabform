@extends('layouts.instructor')

@section('title', 'Soumissions du devoir')
@section('page_title', 'Correction des devoirs')

@section('content')
<div class="card card-instructor p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Soumissions : {{ $assignment->title }}</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Formation : {{ $course->title }} &bull; Max : {{ $assignment->max_score }} pts</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('instructor.submissions.export', [$course->id, $assignment->id]) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-file-archive me-2"></i>Télécharger les livrables (ZIP)</a>
            <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-arrow-left"></i></a>
        </div>
    </div>

    <!-- Quick Stats row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">En attente de correction</span>
                <span class="fw-bold text-danger" style="font-size: 1.5rem;">{{ $pendingCount }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Moyenne de la classe</span>
                <span class="fw-bold text-white" style="font-size: 1.5rem;">
                    {{ round($assignment->submissions()->where('status', 'graded')->avg('score') ?? 0, 1) }} / {{ $assignment->max_score }}
                </span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-dark border border-secondary rounded text-center">
                <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Taux de réussite</span>
                @php
                    $gradedCount = $assignment->submissions()->where('status', 'graded')->count();
                    $passedCount = $assignment->submissions()->where('status', 'graded')->where('passed', true)->count();
                    $passRate = $gradedCount > 0 ? round(($passedCount / $gradedCount) * 100) : 0;
                @endphp
                <span class="fw-bold text-success" style="font-size: 1.5rem;">{{ $passRate }}%</span>
            </div>
        </div>
    </div>

    <!-- Filtering & Bulk Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size: 0.85rem;">Filtre statut :</span>
            <div class="btn-group">
                <a href="{{ route('instructor.submissions.index', [$course->id, $assignment->id]) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ !request()->has('status') ? 'active' : '' }}">Tous</a>
                <a href="{{ route('instructor.submissions.index', [$course->id, $assignment->id, 'status' => 'submitted']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'submitted' ? 'active' : '' }}">À corriger</a>
                <a href="{{ route('instructor.submissions.index', [$course->id, $assignment->id, 'status' => 'graded']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'graded' ? 'active' : '' }}">Corrigés</a>
                <a href="{{ route('instructor.submissions.index', [$course->id, $assignment->id, 'status' => 'returned']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'returned' ? 'active' : '' }}">Renvoyés</a>
            </div>
        </div>

        <!-- Bulk Grade Button -->
        <button type="button" id="btn-bulk-grade-trigger" class="btn btn-sm btn-indigo text-white" style="display: none; background: #6366f1;" data-bs-toggle="modal" data-bs-target="#bulkGradeModal"><i class="fas fa-edit me-2"></i>Noter la sélection</button>
    </div>

    <!-- Submissions list table -->
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="check-all-submissions" class="form-check-input"></th>
                    <th>Apprenant</th>
                    <th>Date de remise</th>
                    <th>Retard</th>
                    <th>Livrable</th>
                    <th>Statut</th>
                    <th>Note</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                    <tr>
                        <td><input type="checkbox" class="form-check-input sub-checkbox" value="{{ $sub->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.85rem;">{{ $sub->user->initials }}</div>
                                <div>
                                    <div class="fw-bold text-white">{{ $sub->user->full_name }}</div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $sub->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            @if($assignment->due_date && $sub->submitted_at && $sub->submitted_at->gt($assignment->due_date))
                                <span class="text-danger" style="font-size: 0.85rem; font-weight: bold;"><i class="fas fa-exclamation-triangle me-1"></i> Retard</span>
                            @else
                                <span class="text-success" style="font-size: 0.85rem;">À temps</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="text-indigo text-decoration-none" style="color: #818cf8; font-size: 0.85rem;">
                                    <i class="fas fa-file-download me-1"></i> {{ Str::limit($sub->file_name, 20) }}
                                </a>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">Saisie en ligne</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $sub->status === 'graded' ? 'success' : ($sub->status === 'returned' ? 'warning' : 'danger') }}">
                                {{ match($sub->status) {
                                    'submitted' => 'À corriger',
                                    'under_review' => 'En cours',
                                    'graded' => 'Corrigé',
                                    'returned' => 'Renvoyé',
                                    default => $sub->status
                                } }}
                            </span>
                        </td>
                        <td>
                            @if($sub->score !== null)
                                <strong class="text-white">{{ $sub->score }}</strong> / {{ $assignment->max_score }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('instructor.submissions.show', [$course->id, $assignment->id, $sub->id]) }}" class="btn btn-sm btn-premium"><i class="fas fa-gavel me-1"></i> Noter</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">Aucune soumission pour ce devoir.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
</div>

<!-- =========================================================================
     MODAL NOTE DE MASSE (BULK GRADE)
     ========================================================================= -->
<div class="modal fade" id="bulkGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">Noter la sélection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulk-grade-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Note globale à attribuer (sur {{ $assignment->max_score }})</label>
                        <input type="number" name="score" id="bulk-score" class="form-control bg-dark border-secondary text-white" min="0" max="{{ $assignment->max_score }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback commun aux apprenants</label>
                        <textarea name="feedback" id="bulk-feedback" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="Qualité globale appréciée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-premium">Appliquer les notes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const $checkAll = $('#check-all-submissions');
        const $checkboxes = $('.sub-checkbox');
        const $bulkBtn = $('#btn-bulk-grade-trigger');

        function toggleBulkBtn() {
            const checkedCount = $('.sub-checkbox:checked').length;
            if (checkedCount > 0) {
                $bulkBtn.show();
            } else {
                $bulkBtn.hide();
            }
        }

        $checkAll.on('change', function() {
            $checkboxes.prop('checked', this.checked);
            toggleBulkBtn();
        });

        $checkboxes.on('change', function() {
            toggleBulkBtn();
        });

        // Submit bulk grades via Ajax
        $('#bulk-grade-form').on('submit', function(e) {
            e.preventDefault();
            const selectedIds = [];
            $('.sub-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            $.ajax({
                url: `/instructor/courses/{{ $course->id }}/assignments/{{ $assignment->id }}/submissions/bulk-grade`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds,
                    score: $('#bulk-score').val(),
                    feedback: $('#bulk-feedback').val()
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Erreur lors de l\'attribution des notes de masse.');
                }
            });
        });
    });
</script>
@endpush
