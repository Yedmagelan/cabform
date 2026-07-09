@extends('layouts.learner')
@section('title', $course->title)
@section('page_title', Str::limit($course->title, 40))

@push('styles')
<style>
    .player-sidebar { border-left: 1px solid rgba(255,255,255,0.05); }
    .lesson-active { background: rgba(99,102,241,0.1) !important; font-weight: bold; border-left: 3px solid #6366f1 !important; }
    .note-item { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 10px; padding: 12px; }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Main Player Panel -->
    <div class="col-lg-8">
        <div class="card card-instructor p-0 overflow-hidden mb-4">
            <!-- Header bar -->
            <div class="p-3 bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center flex-wrap gap-2 text-white">
                <div>
                    <span class="text-muted" style="font-size: 0.8rem;">Module : {{ $currentLesson->module->title }}</span>
                    <h5 class="fw-bold mb-0 mt-1 text-white">{{ $currentLesson->title }}</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary border-secondary text-white" id="btn-add-bookmark-star" title="Ajouter un marque-page">
                        <i class="far fa-bookmark text-warning"></i>
                    </button>
                    
                    <button class="btn btn-sm btn-indigo text-white" id="btn-toggle-complete" style="background: #6366f1;">
                        <i class="fas {{ in_array($currentLesson->id, $completedLessons) ? 'fa-check-circle' : 'fa-circle' }} me-2"></i>
                        <span>{{ in_array($currentLesson->id, $completedLessons) ? 'Complétée' : 'Marquer comme complétée' }}</span>
                    </button>
                </div>
            </div>

            <!-- Content Frame -->
            <div class="bg-dark" style="min-height: 400px; display: flex; align-items: center; justify-content: center; position: relative;">
                @if($currentLesson->type === 'video')
                    <video id="course-video" controls class="w-100" style="max-height: 500px;" data-position="{{ $videoPosition }}">
                        <source src="{{ asset('storage/' . $currentLesson->content) }}" type="video/mp4">
                    </video>
                    
                    <!-- Video Playback Speeds controls overlay -->
                    <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                        <select id="video-speed-selector" class="form-select form-select-sm bg-dark border-secondary text-white" style="width: 80px;">
                            <option value="0.75">0.75x</option>
                            <option value="1" selected>1.0x</option>
                            <option value="1.25">1.25x</option>
                            <option value="1.5">1.5x</option>
                            <option value="2">2.0x</option>
                        </select>
                    </div>

                @elseif($currentLesson->type === 'pdf')
                    <div class="w-100 p-2" style="height: 550px;">
                        <div class="d-flex justify-content-between align-items-center bg-secondary p-2 rounded mb-2 text-white">
                            <span>Lecteur PDF</span>
                            <a href="{{ asset('storage/' . $currentLesson->content) }}" target="_blank" class="btn btn-sm btn-dark text-white"><i class="fas fa-external-link-alt me-1"></i>Ouvrir plein écran</a>
                        </div>
                        <iframe src="{{ asset('storage/' . $currentLesson->content) }}" class="w-100 h-100 rounded border-0"></iframe>
                    </div>

                @else
                    <div class="p-4 text-start w-100 bg-white text-dark rounded-bottom" style="min-height: 400px;">
                        {!! $currentLesson->content !!}
                    </div>
                @endif
            </div>

            <!-- Footer navigation -->
            <div class="p-3 bg-dark border-top border-secondary d-flex justify-content-between align-items-center">
                @php
                    $allLessons = $course->modules->flatMap->lessons;
                    $currentIndex = $allLessons->pluck('id')->search($currentLesson->id);
                    $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
                    $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;
                @endphp

                @if($prevLesson)
                    <a href="{{ route('learner.course.lesson', [$course->slug, $prevLesson->id]) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-chevron-left me-1"></i>Précédent</a>
                @else
                    <button class="btn btn-sm btn-outline-secondary border-secondary text-white" disabled><i class="fas fa-chevron-left me-1"></i>Précédent</button>
                @endif

                <span class="text-muted" style="font-size: 0.85rem;">Leçon {{ $currentIndex + 1 }} sur {{ $allLessons->count() }}</span>

                @if($nextLesson)
                    <a href="{{ route('learner.course.lesson', [$course->slug, $nextLesson->id]) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white">Suivant <i class="fas fa-chevron-right ms-1"></i></a>
                @else
                    <button class="btn btn-sm btn-outline-secondary border-secondary text-white" disabled>Suivant <i class="fas fa-chevron-right ms-1"></i></button>
                @endif
            </div>
        </div>

        <!-- Resources & Downloadable links -->
        @if($currentLesson->resources->count() > 0)
            <div class="card card-instructor p-4 mb-4">
                <h6 class="fw-bold text-white mb-3"><i class="fas fa-paperclip text-indigo me-2"></i>Ressources de la leçon</h6>
                <div class="list-group">
                    @foreach($currentLesson->resources as $res)
                        <a href="{{ asset('storage/' . $res->file_path) }}" target="_blank" class="list-group-item list-group-item-action list-group-item-dark border-secondary d-flex justify-content-between align-items-center text-white">
                            <span><i class="fas fa-file-alt me-2 text-indigo"></i>{{ $res->title }}</span>
                            <i class="fas fa-download"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Right Sidebar Navigation Accordions & Notes -->
    <div class="col-lg-4">
        <!-- Main accordion menu -->
        <div class="card card-instructor p-0 overflow-hidden mb-4">
            <div class="p-3 bg-dark border-bottom border-secondary text-white">
                <h6 class="fw-bold mb-2">Progression de la formation</h6>
                <div class="progress mb-1" style="height: 6px; background: rgba(255,255,255,0.05);">
                    <div class="progress-bar bg-success" id="course-progress-bar" style="width: {{ $enrollment->progress_percentage }}%;"></div>
                </div>
                <small class="text-muted" id="course-progress-text">{{ round($enrollment->progress_percentage) }}% complété</small>
            </div>

            <div class="accordion accordion-dark" id="courseOutlineAccordion">
                @foreach($course->modules as $modIndex => $module)
                    <div class="accordion-item bg-dark border-secondary">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-dark text-white collapsed border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMod-{{ $module->id }}">
                                <div>
                                    <small class="text-indigo d-block">Module {{ $modIndex + 1 }}</small>
                                    <strong>{{ $module->title }}</strong>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseMod-{{ $module->id }}" class="accordion-collapse collapse {{ $module->id === $currentLesson->module_id ? 'show' : '' }}" data-bs-parent="#courseOutlineAccordion">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($module->lessons as $les)
                                        <a href="{{ route('learner.course.lesson', [$course->slug, $les->id]) }}" class="list-group-item list-group-item-dark border-secondary d-flex align-items-center gap-3 py-3 {{ $les->id === $currentLesson->id ? 'lesson-active' : '' }}">
                                            <i class="fas {{ in_array($les->id, $completedLessons) ? 'fa-check-circle text-success' : 'fa-circle text-muted' }}"></i>
                                            <div>
                                                <span class="d-block text-white" style="font-size: 0.85rem;">{{ $les->title }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem;"><i class="fas {{ $les->type === 'video' ? 'fa-video' : 'fa-file-alt' }} me-1"></i>{{ $les->duration_minutes }} min</small>
                                            </div>
                                        </a>
                                    @endforeach

                                    <!-- Quizzes -->
                                    @foreach($module->quizzes as $quiz)
                                        <a href="{{ route('learner.quiz.show', [$course->slug, $quiz->id]) }}" class="list-group-item list-group-item-dark border-secondary d-flex align-items-center gap-3 py-3 text-warning">
                                            <i class="fas fa-question-circle"></i>
                                            <div>
                                                <span class="d-block" style="font-size: 0.85rem;">Quiz : {{ $quiz->title }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">Évaluation</small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Notes / Bookmarks Sidebar Workspace -->
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-3">Mes Notes & Annotations</h5>
            
            <form id="add-note-form" class="mb-3">
                <div class="input-group">
                    <input type="text" name="note" id="note-input-field" class="form-control bg-dark border-secondary text-white btn-sm" placeholder="Ajouter une note de cours..." required>
                    <button type="submit" class="btn btn-premium"><i class="fas fa-plus"></i></button>
                </div>
            </form>

            <div class="overflow-y-auto" style="max-height: 300px;" id="notes-list-container">
                <!-- Loaded dynamically via JS -->
                <div class="text-center text-muted py-3">Chargement des notes...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const video = document.getElementById('course-video');

        // Resume Video position if available
        if (video) {
            const startPos = $(video).data('position');
            if (startPos > 0) {
                video.currentTime = startPos;
            }

            // Speed rate selector binding
            $('#video-speed-selector').on('change', function() {
                video.playbackRate = parseFloat($(this).val());
            });

            // Auto save playback position position every 5s
            setInterval(function() {
                if (!video.paused) {
                    $.ajax({
                        url: `{{ route('learner.course.lesson.video-position', [$course->slug, $currentLesson->id]) }}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            position: video.currentTime
                        }
                    });
                }
            }, 5000);
        }

        // Complete lesson toggle trigger
        $('#btn-toggle-complete').on('click', function() {
            $.ajax({
                url: `{{ route('learner.course.lesson.complete', [$course->slug, $currentLesson->id]) }}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#btn-toggle-complete i').removeClass('fa-circle').addClass('fa-check-circle');
                        $('#course-progress-bar').css('width', response.progress + '%');
                        $('#course-progress-text').text(Math.round(response.progress) + '% complété');
                        
                        alert('Leçon marquée comme complétée ! ✅');
                    }
                }
            });
        });

        // Load bookmarks/notes lists
        function loadNotes() {
            $.get(`{{ route('learner.course.lesson.bookmarks', [$course->slug, $currentLesson->id]) }}`, function(response) {
                if (response.success) {
                    const $container = $('#notes-list-container');
                    $container.empty();
                    if (response.bookmarks.length > 0) {
                        response.bookmarks.forEach(note => {
                            $container.append(`
                                <div class="note-item text-white">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <small class="text-indigo fw-bold"><i class="far fa-clock me-1"></i>Note</small>
                                        <button class="btn btn-sm btn-link text-danger p-0 delete-note-btn" data-id="${note.id}"><i class="fas fa-trash"></i></button>
                                    </div>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">${note.note}</p>
                                </div>
                            `);
                        });
                    } else {
                        $container.html('<div class="text-center text-muted py-3">Aucune note pour cette leçon.</div>');
                    }
                }
            });
        }

        loadNotes(); // Initial notes loading

        // Save new note via Ajax
        $('#add-note-form').on('submit', function(e) {
            e.preventDefault();
            const noteText = $('#note-input-field').val();
            
            $.ajax({
                url: `{{ route('learner.course.lesson.bookmark', [$course->slug, $currentLesson->id]) }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    note: noteText
                },
                success: function(response) {
                    if (response.success) {
                        $('#note-input-field').val('');
                        loadNotes();
                    }
                }
            });
        });

        // Bookmark star button
        $('#btn-add-bookmark-star').on('click', function() {
            $.ajax({
                url: `{{ route('learner.course.lesson.bookmark', [$course->slug, $currentLesson->id]) }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    note: 'Marque-page ajouté à cette leçon.'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Marque-page enregistré ! ⭐');
                        loadNotes();
                    }
                }
            });
        });

        // Delete note
        $(document).on('click', '.delete-note-btn', function() {
            const noteId = $(this).data('id');
            if (confirm('Supprimer cette note ?')) {
                $.ajax({
                    url: `/learner/course/{{ $course->slug }}/lesson/{{ $currentLesson->id }}/bookmark/${noteId}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            loadNotes();
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
