@extends('layouts.instructor')

@section('title', 'Modifier le Devoir')
@section('page_title', 'Modifier le Devoir')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">Édition du Devoir</h5>
                <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-outline-secondary text-white border-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            </div>

            <form action="{{ route('instructor.assignments.update', [$course->id, $assignment->id]) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label text-white">Titre du Devoir</label>
                    <input type="text" name="title" class="form-control bg-dark border-secondary text-white py-2" value="{{ $assignment->title }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Description / Consignes générales</label>
                    <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="4" required>{{ $assignment->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Consignes détaillées / Livrables attendus</label>
                    <textarea name="instructions" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="Ex: Veuillez téléverser un fichier PDF contenant votre code et votre rapport.">{{ $assignment->instructions }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white">Module</label>
                        <select name="module_id" class="form-select bg-dark border-secondary text-white">
                            <option value="">-- Aucun --</option>
                            @foreach($modules as $m)
                                <option value="{{ $m->id }}" {{ $assignment->module_id == $m->id ? 'selected' : '' }}>{{ $m->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Leçon associée (Optionnel)</label>
                        <select name="lesson_id" class="form-select bg-dark border-secondary text-white">
                            <option value="">-- Aucune --</option>
                            @foreach($lessons as $l)
                                <option value="{{ $l->id }}" {{ $assignment->lesson_id == $l->id ? 'selected' : '' }}>{{ $l->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white">Date limite</label>
                        <input type="text" name="due_date" class="form-control bg-dark border-secondary text-white flatpickr-input" value="{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white">Taille max fichier (MB)</label>
                        <input type="number" name="max_file_size_mb" class="form-control bg-dark border-secondary text-white" value="{{ $assignment->max_file_size_mb }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white">Tentatives max.</label>
                        <input type="number" name="max_submissions" class="form-control bg-dark border-secondary text-white" value="{{ $assignment->max_submissions }}" min="1">
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <!-- GRILLE D'EVALUATION / RUBRIQUE -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-white mb-0"><i class="fas fa-table text-indigo me-2"></i>Grille d'Évaluation (Critères de notation)</h6>
                        <button type="button" id="btn-add-criterion" class="btn btn-sm btn-outline-secondary text-white border-secondary"><i class="fas fa-plus me-1"></i>Ajouter un critère</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle" id="rubric-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Critère</th>
                                    <th>Description / Détails</th>
                                    <th style="width: 15%;">Points Max.</th>
                                    <th style="width: 10%;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="rubric-criteria-body">
                                @forelse($assignment->rubric ?? [] as $index => $criterion)
                                    <tr class="criterion-row" data-index="{{ $index }}">
                                        <td>
                                            <input type="hidden" name="rubric[{{ $index }}][id]" value="{{ $criterion['id'] ?? uniqid() }}">
                                            <input type="text" name="rubric[{{ $index }}][title]" class="form-control bg-dark border-secondary text-white py-1" value="{{ $criterion['title'] }}" placeholder="ex: Qualité du code" required>
                                        </td>
                                        <td>
                                            <textarea name="rubric[{{ $index }}][description]" class="form-control bg-dark border-secondary text-white py-1" rows="1" placeholder="ex: Propreté, indentation...">{{ $criterion['description'] }}</textarea>
                                        </td>
                                        <td>
                                            <input type="number" name="rubric[{{ $index }}][max_points]" class="form-control bg-dark border-secondary text-white py-1 criterion-points" value="{{ $criterion['max_points'] }}" min="1" required>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-link text-danger remove-criterion-btn"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- Exemple prérempli -->
                                    <tr class="criterion-row" data-index="0">
                                        <td>
                                            <input type="hidden" name="rubric[0][id]" value="crit-1">
                                            <input type="text" name="rubric[0][title]" class="form-control bg-dark border-secondary text-white py-1" value="Exactitude des résultats" placeholder="Nom du critère" required>
                                        </td>
                                        <td>
                                            <textarea name="rubric[0][description]" class="form-control bg-dark border-secondary text-white py-1" rows="1" placeholder="Détails...">Répond précisément aux questions du sujet.</textarea>
                                        </td>
                                        <td>
                                            <input type="number" name="rubric[0][max_points]" class="form-control bg-dark border-secondary text-white py-1 criterion-points" value="10" min="1" required>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-link text-danger remove-criterion-btn"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Total Points calculé (Somme des critères)</label>
                            <input type="number" name="max_score" id="total-max-score" class="form-control bg-dark border-secondary text-indigo fw-bold" value="{{ $assignment->max_score }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Note de passage minimale</label>
                            <input type="number" name="passing_score" class="form-control bg-dark border-secondary text-white" value="{{ $assignment->passing_score }}" required>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-premium px-4 py-2"><i class="fas fa-save me-2"></i>Enregistrer le devoir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".flatpickr-input", { enableTime: true, dateFormat: "Y-m-d H:i" });

        // Auto sum criteria points
        function recalculateTotalPoints() {
            let total = 0;
            const pointsInputs = document.querySelectorAll('.criterion-points');
            pointsInputs.forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                }
            });
            document.getElementById('total-max-score').value = total;
        }

        // Event for inputs change
        document.getElementById('rubric-table').addEventListener('input', function(e) {
            if (e.target.classList.contains('criterion-points')) {
                recalculateTotalPoints();
            }
        });

        // Add criterion
        document.getElementById('btn-add-criterion').addEventListener('click', function() {
            const tbody = document.getElementById('rubric-criteria-body');
            const rowCount = tbody.querySelectorAll('.criterion-row').length;
            const uniq = 'crit_' + Date.now();

            const tr = document.createElement('tr');
            tr.className = 'criterion-row';
            tr.dataset.index = rowCount;
            tr.innerHTML = `
                <td>
                    <input type="hidden" name="rubric[${rowCount}][id]" value="${uniq}">
                    <input type="text" name="rubric[${rowCount}][title]" class="form-control bg-dark border-secondary text-white py-1" placeholder="Nom du critère" required>
                </td>
                <td>
                    <textarea name="rubric[${rowCount}][description]" class="form-control bg-dark border-secondary text-white py-1" rows="1" placeholder="Détails..."></textarea>
                </td>
                <td>
                    <input type="number" name="rubric[${rowCount}][max_points]" class="form-control bg-dark border-secondary text-white py-1 criterion-points" value="5" min="1" required>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-link text-danger remove-criterion-btn"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            recalculateTotalPoints();
        });

        // Remove criterion
        document.getElementById('rubric-table').addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-criterion-btn');
            if (btn) {
                btn.closest('.criterion-row').remove();
                recalculateTotalPoints();
            }
        });

        recalculateTotalPoints(); // Initial call
    });
</script>
@endpush
