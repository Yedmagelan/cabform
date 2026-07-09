@extends('layouts.app')
@section('title', 'Quiz: ' . $quiz->title)
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <nav class="mb-3"><a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-cb-muted"><i class="fas fa-arrow-left me-1"></i>{{ $course->title }}</a></nav>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-800">{{ $quiz->title }}</h2>
            <span class="badge-cabform badge-primary">Score min: {{ $quiz->passing_score }}%</span>
        </div>

        <!-- Questions existantes -->
        @foreach($quiz->questions as $i => $question)
            <div class="card-cabform p-4 mb-3">
                <h6 class="fw-700"><span class="text-gradient">Q{{ $i + 1 }}.</span> {{ $question->question_text }}</h6>
                <div class="ms-3 mt-2">
                    @foreach($question->answers as $answer)
                        <div class="d-flex align-items-center gap-2 py-1">
                            <i class="fas {{ $answer->is_correct ? 'fa-check-circle text-cb-success' : 'fa-circle text-cb-muted' }}" style="font-size:0.8rem;"></i>
                            <span class="{{ $answer->is_correct ? 'fw-600 text-cb-success' : 'text-cb-muted' }}">{{ $answer->answer_text }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Ajouter une question -->
        <div class="card-cabform p-4" style="border: 1px dashed rgba(5,0,216,0.3);">
            <h5 class="fw-700 mb-3"><i class="fas fa-plus-circle text-cb-primary me-2"></i>Ajouter une question</h5>
            <form method="POST" action="{{ route('instructor.questions.store', [$course->id, $quiz->id]) }}">
                @csrf
                <div class="mb-3"><label class="form-label-cabform">Question</label><textarea name="question_text" class="form-control form-control-cabform" rows="2" required></textarea></div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6"><label class="form-label-cabform">Type</label><select name="type" class="form-control form-control-cabform"><option value="multiple_choice">Choix multiple</option><option value="true_false">Vrai/Faux</option></select></div>
                    <div class="col-md-6"><label class="form-label-cabform">Points</label><input type="number" name="points" class="form-control form-control-cabform" value="1" min="1"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label-cabform">Réponses (cochez la bonne)</label>
                    @for($r = 0; $r < 4; $r++)
                        <div class="input-group mb-2">
                            <div class="input-group-text" style="background:var(--cb-glass-bg);border:1px solid var(--cb-glass-border);"><input type="checkbox" name="answers[{{ $r }}][is_correct]" value="1" class="form-check-input m-0"></div>
                            <input type="text" name="answers[{{ $r }}][text]" class="form-control form-control-cabform" placeholder="Réponse {{ $r + 1 }}" {{ $r < 2 ? 'required' : '' }}>
                        </div>
                    @endfor
                </div>
                <button type="submit" class="btn btn-cabform btn-cabform-primary"><i class="fas fa-plus me-2"></i>Ajouter la question</button>
            </form>
        </div>
    </div>
</section>
@endsection
