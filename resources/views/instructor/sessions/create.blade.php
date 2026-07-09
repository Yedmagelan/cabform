@extends('layouts.instructor')

@section('title', 'Créer une session')
@section('page_title', 'Créer une Session')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-instructor p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0">Nouvelle Session de Cohorte</h5>
                <a href="{{ route('instructor.sessions.index', $course->id) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
            </div>

            <form action="{{ route('instructor.sessions.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label text-white">Nom de la session / cohorte</label>
                    <input type="text" name="name" class="form-control bg-dark border-secondary text-white py-2" placeholder="Ex: Promotion Automne 2026" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Description (Optionnel)</label>
                    <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="Saisissez des notes sur ce groupe d'étudiants..."></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white">Date de début</label>
                        <input type="date" name="start_date" class="form-control bg-dark border-secondary text-white" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Date de fin (Optionnel)</label>
                        <input type="date" name="end_date" class="form-control bg-dark border-secondary text-white">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-white">Capacité maximale d'apprenants (Optionnel)</label>
                        <input type="number" name="max_students" class="form-control bg-dark border-secondary text-white" placeholder="Illimité" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Importer des participants par CSV (Optionnel)</label>
                        <input type="file" name="csv_file" class="form-control bg-dark border-secondary text-white" accept=".csv">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format attendu : Nom, Email (Première ligne pour les en-têtes)</small>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-premium px-4 py-2"><i class="fas fa-save me-2"></i>Créer la session</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
