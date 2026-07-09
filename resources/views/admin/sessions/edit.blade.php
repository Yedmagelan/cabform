@extends('layouts.admin')
@section('title', 'Modifier la Session : ' . $session->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sessions.index') }}">Sessions</a></li>
    <li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="card-cabform p-4 max-width-600 mx-auto">
    <h5 class="fw-700 text-cb-primary mb-4"><i class="fas fa-edit me-2"></i>Modifier la Session / Cohorte</h5>
    
    <form action="{{ route('admin.sessions.update', $session->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label small fw-600">Nom de la session</label>
            <input type="text" name="name" class="form-control form-control-cabform" value="{{ $session->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-600">Formation</label>
            <select name="course_id" class="form-select form-control-cabform" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $session->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-600">Description</label>
            <textarea name="description" class="form-control form-control-cabform" rows="3">{{ $session->description }}</textarea>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label class="form-label small fw-600">Date de début</label>
                <input type="date" name="start_date" class="form-control form-control-cabform" value="{{ $session->start_date?->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-600">Date de fin</label>
                <input type="date" name="end_date" class="form-control form-control-cabform" value="{{ $session->end_date?->format('Y-m-d') }}">
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label class="form-label small fw-600">Date limite d'inscription</label>
                <input type="date" name="enrollment_deadline" class="form-control form-control-cabform" value="{{ $session->enrollment_deadline?->format('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-600">Capacité maximale d'élèves</label>
                <input type="number" name="max_students" class="form-control form-control-cabform" value="{{ $session->max_students }}" min="1">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-600">Statut</label>
            <select name="status" class="form-select form-control-cabform" required>
                <option value="upcoming" {{ $session->status === 'upcoming' ? 'selected' : '' }}>À venir (Inscriptions fermées)</option>
                <option value="active" {{ $session->status === 'active' ? 'selected' : '' }}>Active (Inscriptions ouvertes)</option>
                <option value="completed" {{ $session->status === 'completed' ? 'selected' : '' }}>Clôturée</option>
                <option value="cancelled" {{ $session->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-cabform btn-cabform-outline-primary btn-cabform-sm">Annuler</a>
            <button type="submit" class="btn btn-cabform btn-cabform-primary btn-cabform-sm">Enregistrer les modifications</button>
        </div>
    </form>
</div>
@endsection
