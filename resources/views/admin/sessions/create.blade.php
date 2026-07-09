@extends('layouts.admin')
@section('title', 'Créer une Session')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sessions.index') }}">Sessions</a></li>
    <li class="breadcrumb-item active">Nouvelle session</li>
@endsection

@section('content')
<div class="card-cabform p-4 max-width-600 mx-auto">
    <h5 class="fw-700 text-cb-primary mb-4"><i class="fas fa-calendar-plus me-2"></i>Créer une Session / Cohorte</h5>
    
    <form action="{{ route('admin.sessions.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label small fw-600">Nom de la session</label>
            <input type="text" name="name" class="form-control form-control-cabform" placeholder="Ex: Cohorte Hiver 2026" required>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-600">Formation</label>
            <select name="course_id" class="form-select form-control-cabform" required>
                <option value="">Sélectionner une formation...</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-600">Description / Objectifs spécifiques</label>
            <textarea name="description" class="form-control form-control-cabform" rows="3" placeholder="Description facultative..."></textarea>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label class="form-label small fw-600">Date de début</label>
                <input type="date" name="start_date" class="form-control form-control-cabform" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-600">Date de fin</label>
                <input type="date" name="end_date" class="form-control form-control-cabform">
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label class="form-label small fw-600">Date limite d'inscription</label>
                <input type="date" name="enrollment_deadline" class="form-control form-control-cabform">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-600">Capacité maximale d'élèves</label>
                <input type="number" name="max_students" class="form-control form-control-cabform" min="1" placeholder="Ex: 50 (laisser vide si infini)">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-600">Statut initial</label>
            <select name="status" class="form-select form-control-cabform" required>
                <option value="upcoming">À venir (Inscriptions fermées)</option>
                <option value="active" selected>Active (Inscriptions ouvertes)</option>
            </select>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm">Annuler</a>
            <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Créer la session</button>
        </div>
    </form>
</div>
@endsection
