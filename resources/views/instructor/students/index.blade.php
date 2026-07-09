@extends('layouts.instructor')

@section('title', 'Suivi des Apprenants')
@section('page_title', 'Suivi des Apprenants')

@section('content')
<div class="card card-instructor p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-white mb-1">Apprenants inscrits : {{ $course->title }}</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Gérez les inscriptions et suivez la progression en temps réel.</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('instructor.courses.export-csv', $course->id) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-file-csv me-2"></i>Exporter la liste (CSV)</a>
            <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-arrow-left"></i></a>
        </div>
    </div>

    <!-- Filters & Bulk actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div class="d-flex gap-2 align-items-center">
            <!-- Search bar -->
            <form action="{{ route('instructor.students', $course->id) }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control bg-dark border-secondary text-white btn-sm" placeholder="Rechercher par nom/email" value="{{ request()->search }}" style="width: 240px;">
                <button type="submit" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-search"></i></button>
            </form>
            
            <div class="btn-group ms-2">
                <a href="{{ route('instructor.students', $course->id) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ !request()->has('status') ? 'active' : '' }}">Tous</a>
                <a href="{{ route('instructor.students', [$course->id, 'status' => 'active']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'active' ? 'active' : '' }}">Actifs</a>
                <a href="{{ route('instructor.students', [$course->id, 'status' => 'completed']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'completed' ? 'active' : '' }}">Complétés</a>
                <a href="{{ route('instructor.students', [$course->id, 'status' => 'suspended']) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary {{ request()->status === 'suspended' ? 'active' : '' }}">Suspendus</a>
            </div>
        </div>

        <!-- Bulk Action Dropdown -->
        <div id="bulk-actions-group" style="display: none;">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size: 0.85rem;">Actions groupées :</span>
                <select id="bulk-action-select" class="form-select bg-dark border-secondary text-white form-select-sm" style="width: 140px;">
                    <option value="">-- Choisir --</option>
                    <option value="activate">Activer</option>
                    <option value="suspend">Suspendre</option>
                    <option value="remove">Retirer</option>
                </select>
                <button type="button" id="btn-apply-bulk" class="btn btn-sm btn-indigo text-white" style="background: #6366f1;">Appliquer</button>
            </div>
        </div>
    </div>

    <!-- Student Enrollments table -->
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="check-all-students" class="form-check-input"></th>
                    <th>Apprenant</th>
                    <th>Date d'inscription</th>
                    <th>Progression</th>
                    <th>Dernière Activité</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                    <tr data-id="{{ $enrollment->id }}">
                        <td><input type="checkbox" class="form-check-input student-checkbox" value="{{ $enrollment->id }}"></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar" style="width: 36px; height: 36px; font-size: 0.95rem;">{{ $enrollment->user->initials }}</div>
                                <div>
                                    <div class="fw-bold text-white">{{ $enrollment->user->full_name }}</div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $enrollment->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $enrollment->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2" style="width: 160px;">
                                <div class="progress w-100" style="height: 6px; background: rgba(255,255,255,0.05);">
                                    <div class="progress-bar bg-indigo" style="width: {{ $enrollment->progress_percentage }}%; background: #6366f1;"></div>
                                </div>
                                <span class="fw-bold text-white" style="font-size: 0.8rem;">{{ round($enrollment->progress_percentage) }}%</span>
                            </div>
                        </td>
                        <td>{{ $enrollment->last_accessed_at ? $enrollment->last_accessed_at->diffForHumans() : 'Jamais' }}</td>
                        <td>
                            <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'indigo' : 'secondary') }}">
                                {{ match($enrollment->status) {
                                    'pending' => 'En attente',
                                    'active' => 'Actif',
                                    'completed' => 'Terminé',
                                    'suspended' => 'Suspendu',
                                    'cancelled' => 'Annulé',
                                    default => $enrollment->status
                                } }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('instructor.students.show', [$course->id, $enrollment->user_id]) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-eye me-1"></i> Suivi</a>
                            <a href="{{ route('instructor.messages.index', ['contact_id' => $enrollment->user_id]) }}" class="btn btn-sm btn-outline-secondary text-white border-secondary ms-1"><i class="fas fa-comment-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">Aucun apprenant inscrit à cette formation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const $checkAll = $('#check-all-students');
        const $checkboxes = $('.student-checkbox');
        const $bulkGroup = $('#bulk-actions-group');

        function toggleBulkGroup() {
            const count = $('.student-checkbox:checked').length;
            if (count > 0) {
                $bulkGroup.show();
            } else {
                $bulkGroup.hide();
            }
        }

        $checkAll.on('change', function() {
            $checkboxes.prop('checked', this.checked);
            toggleBulkGroup();
        });

        $checkboxes.on('change', function() {
            toggleBulkGroup();
        });

        $('#btn-apply-bulk').on('click', function() {
            const action = $('#bulk-action-select').val();
            if (!action) return;

            const selectedIds = [];
            $('.student-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (confirm('Voulez-vous vraiment appliquer cette action aux apprenants sélectionnés ?')) {
                $.ajax({
                    url: `/instructor/courses/{{ $course->id }}/students/bulk-action`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds,
                        action: action
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
