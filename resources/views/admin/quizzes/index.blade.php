@extends('layouts.admin') @section('title', 'Quiz & Examens') @section('breadcrumb')<li class="breadcrumb-item active">Quiz</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-700 mb-0"><i class="fas fa-question-circle text-cb-primary me-2"></i>Quiz & Examens</h4></div>
<div class="card-cabform"><div class="table-responsive"><table class="table table-cabform mb-0"><thead><tr><th>Titre</th><th>Formation</th><th>Questions</th><th>Tentatives</th><th>Score min.</th><th></th></tr></thead><tbody>
@forelse($quizzes as $quiz)<tr><td class="fw-600">{{ $quiz->title }}</td><td>{{ Str::limit($quiz->course->title ?? '-', 30) }}</td><td>{{ $quiz->questions_count }}</td><td>{{ $quiz->attempts_count }}</td><td>{{ $quiz->passing_score }}%</td><td><button class="btn btn-cabform-glass btn-cabform-sm"><i class="fas fa-edit"></i></button></td></tr>
@empty<tr><td colspan="6" class="text-center text-cb-muted py-4">Aucun quiz.</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $quizzes->links() }}</div></div>
@endsection
