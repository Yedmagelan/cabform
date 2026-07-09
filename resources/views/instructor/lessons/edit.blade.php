@extends('layouts.instructor')

@section('title', 'Modifier la leçon')
@section('page_title', 'Modifier la Leçon')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-white mb-1">{{ $lesson->title }}</h5>
                    <span class="text-muted" style="font-size: 0.85rem;">Module : {{ $module->title }} &bull; Type : {{ strtoupper($lesson->type) }}</span>
                </div>
                <a href="{{ route('instructor.courses.edit', ['course' => $course->id, 'tab' => 'structure']) }}" class="btn btn-outline-secondary text-white border-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            </div>

            <form action="{{ route('instructor.lessons.update', [$course->id, $module->id, $lesson->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label text-white">Titre de la leçon</label>
                    <input type="text" name="title" class="form-control bg-dark border-secondary text-white py-2" value="{{ $lesson->title }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white">Durée estimée (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control bg-dark border-secondary text-white" value="{{ $lesson->duration_minutes }}" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white">Options de visibilité</label>
                        <div class="d-flex gap-4 py-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_free_preview" id="free-prev" value="1" {{ $lesson->is_free_preview ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="free-prev">Aperçu gratuit</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_downloadable" id="downloadable" value="1" {{ $lesson->is_downloadable ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="downloadable">Téléchargeable</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenu dynamique selon le type de leçon -->
                <div class="card bg-dark border-secondary p-4 mb-4 mt-4">
                    @if($lesson->type === 'video')
                        <h6 class="fw-bold text-white mb-3"><i class="fas fa-video me-2 text-indigo"></i>Source de la vidéo</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Fournisseur de vidéo</label>
                            <select name="video_provider" id="video-provider-select" class="form-select bg-dark border-secondary text-white">
                                <option value="upload" {{ $lesson->video_provider === 'upload' ? 'selected' : '' }}>Télécharger un fichier (MP4, Max 500MB)</option>
                                <option value="youtube" {{ $lesson->video_provider === 'youtube' ? 'selected' : '' }}>Lien YouTube</option>
                                <option value="vimeo" {{ $lesson->video_provider === 'vimeo' ? 'selected' : '' }}>Lien Vimeo</option>
                            </select>
                        </div>

                        <!-- Groupe Upload Fichier -->
                        <div id="video-upload-group" class="mb-3" style="display: {{ $lesson->video_provider === 'upload' || !$lesson->video_provider ? 'block' : 'none' }};">
                            <label class="form-label">Fichier Vidéo (MP4, MOV, MKV)</label>
                            @if($lesson->video_url && $lesson->video_provider === 'upload')
                                <div class="alert alert-secondary d-flex justify-content-between align-items-center bg-dark border-secondary mb-2 text-white">
                                    <span><i class="fas fa-file-video me-2 text-indigo"></i>{{ basename($lesson->video_url) }}</span>
                                    <span class="badge bg-success">Transcodé</span>
                                </div>
                            @endif
                            <input type="file" name="local_video" class="form-control bg-dark border-secondary text-white">
                        </div>

                        <!-- Groupe Lien Externe -->
                        <div id="video-url-group" class="mb-3" style="display: {{ in_array($lesson->video_provider, ['youtube', 'vimeo']) ? 'block' : 'none' }};">
                            <label class="form-label">URL de la vidéo (YouTube / Vimeo)</label>
                            <input type="url" name="video_url" class="form-control bg-dark border-secondary text-white" value="{{ in_array($lesson->video_provider, ['youtube', 'vimeo']) ? $lesson->video_url : '' }}" placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                    @elseif($lesson->type === 'pdf')
                        <h6 class="fw-bold text-white mb-3"><i class="fas fa-file-pdf me-2 text-indigo"></i>Document PDF du cours</h6>
                        @if($lesson->content)
                            <div class="alert alert-secondary d-flex justify-content-between align-items-center bg-dark border-secondary mb-2 text-white">
                                <span><i class="fas fa-file-pdf me-2 text-danger"></i>Visualiser le PDF existant</span>
                                <a href="{{ asset('storage/' . $lesson->content) }}" target="_blank" class="btn btn-sm btn-link text-indigo text-decoration-none">Télécharger</a>
                            </div>
                        @endif
                        <input type="file" name="pdf_file" class="form-control bg-dark border-secondary text-white" accept="application/pdf">

                    @elseif($lesson->type === 'text')
                        <h6 class="fw-bold text-white mb-3"><i class="fas fa-file-alt me-2 text-indigo"></i>Éditeur de texte riche</h6>
                        <textarea name="content" id="wysiwyg-editor" class="form-control bg-dark border-secondary text-white" rows="12">{{ $lesson->content }}</textarea>
                    
                    @elseif($lesson->type === 'audio')
                        <h6 class="fw-bold text-white mb-3"><i class="fas fa-headphones me-2 text-indigo"></i>Fichier Audio (MP3)</h6>
                        <input type="file" name="audio_file" class="form-control bg-dark border-secondary text-white" accept="audio/*">

                    @else
                        <div class="text-center text-muted py-4">Éditeur spécifique non requis ou contenu personnalisable standard.</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label text-white">Description courte / Notes pédagogiques</label>
                    <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="3">{{ $lesson->description }}</textarea>
                </div>

                <div class="mt-4 text-end d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 0.85rem;"><i class="fas fa-clock me-1"></i> Sauvegarde automatique active</span>
                    <button type="submit" class="btn btn-premium px-4"><i class="fas fa-save me-2"></i>Enregistrer la leçon</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load TinyMCE for WYSIWYG Content Editing -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init TinyMCE
        if (document.getElementById('wysiwyg-editor')) {
            tinymce.init({
                selector: '#wysiwyg-editor',
                plugins: 'lists link image media table code wordcount',
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
                skin: 'oxide-dark',
                content_css: 'dark',
                height: 400
            });
        }

        // Toggle Video Provider Input Views
        const providerSelect = document.getElementById('video-provider-select');
        if (providerSelect) {
            providerSelect.addEventListener('change', function() {
                const val = this.value;
                const uploadGroup = document.getElementById('video-upload-group');
                const urlGroup = document.getElementById('video-url-group');

                if (val === 'upload') {
                    uploadGroup.style.display = 'block';
                    urlGroup.style.display = 'none';
                } else {
                    uploadGroup.style.display = 'none';
                    urlGroup.style.display = 'block';
                }
            });
        }
    });
</script>
@endpush
