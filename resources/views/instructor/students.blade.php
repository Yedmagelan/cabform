@extends('layouts.app')
@section('title', 'Étudiants: ' . $course->title)
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <nav class="mb-3"><a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-cb-muted"><i class="fas fa-arrow-left me-1"></i>{{ $course->title }}</a></nav>
        <h2 class="fw-800 mb-4"><i class="fas fa-users text-cb-primary me-2"></i>Apprenants inscrits</h2>
        <div class="card-cabform">
            <div class="table-responsive">
                <table class="table table-cabform mb-0">
                    <thead><tr><th>Apprenant</th><th>E-mail</th><th>Progression</th><th>Statut</th><th>Inscrit le</th></tr></thead>
                    <tbody>
                        @forelse($enrollments as $e)
                        <tr>
                            <td class="fw-600">{{ $e->user->full_name ?? '-' }}</td>
                            <td class="text-cb-muted">{{ $e->user->email ?? '-' }}</td>
                            <td><div class="d-flex align-items-center gap-2"><div class="progress-cabform" style="width:100px;"><div class="progress-bar" style="width:{{ $e->progress_percentage }}%"></div></div><span class="text-cb-muted" style="font-size:0.75rem;">{{ number_format($e->progress_percentage,0) }}%</span></div></td>
                            <td><span class="badge-cabform {{ $e->status === 'active' ? 'badge-success' : ($e->status === 'completed' ? 'badge-primary' : 'badge-warning') }}">{{ ucfirst($e->status) }}</span></td>
                            <td class="text-cb-muted" style="font-size:0.85rem;">{{ $e->created_at?->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-cb-muted py-4">Aucun apprenant inscrit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $enrollments->links() }}</div>
        </div>
    </div>
</section>
@endsection
