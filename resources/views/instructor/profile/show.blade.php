@extends('layouts.instructor')

@section('title', 'Mon Profil')
@section('page_title', 'Profil Public')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-lg-10">
        <!-- Profile Banner -->
        <div class="card card-instructor p-4 mb-4 text-center">
            <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">{{ $user->initials }}</div>
            <h4 class="fw-bold text-white mb-1">{{ $user->full_name }}</h4>
            <span class="badge bg-indigo-subtle text-indigo px-3 py-2 mb-3" style="background: rgba(99,102,241,0.15); color: #818cf8;">Formateur Expert</span>
            
            <div class="d-flex justify-content-center gap-3 mb-2" style="font-size: 1.2rem;">
                @if($profile->linkedin_url)
                    <a href="{{ $profile->linkedin_url }}" target="_blank" class="text-indigo"><i class="fab fa-linkedin"></i></a>
                @endif
                @if($profile->website_url)
                    <a href="{{ $profile->website_url }}" target="_blank" class="text-indigo"><i class="fas fa-globe"></i></a>
                @endif
            </div>

            <div class="mt-3">
                <a href="{{ route('instructor.profile.edit') }}" class="btn btn-premium btn-sm"><i class="fas fa-user-edit me-2"></i>Modifier mes informations</a>
            </div>
        </div>

        <!-- Bio & Expertise -->
        <div class="card card-instructor p-4 mb-4">
            <h5 class="fw-bold text-white mb-3">Biographie professionnelle</h5>
            <p class="text-muted" style="line-height: 1.6; white-space: pre-wrap;">{{ $profile->bio ?? 'Aucune biographie rédigée.' }}</p>

            <hr class="border-secondary my-4">

            <h5 class="fw-bold text-white mb-3">Domaines d'expertise</h5>
            <div class="d-flex flex-wrap gap-2">
                @forelse($profile->interests['expertises'] ?? [] as $exp)
                    <span class="badge bg-secondary px-3 py-2 text-white">{{ $exp }}</span>
                @empty
                    <span class="text-muted">Aucun domaine d'expertise renseigné.</span>
                @endforelse
            </div>
        </div>

        <!-- Courses Created list -->
        <div class="card card-instructor p-4">
            <h5 class="fw-bold text-white mb-4">Formations dispensées ({{ $courses->count() }})</h5>
            <div class="row g-3">
                @forelse($courses as $course)
                    <div class="col-md-6">
                        <div class="p-3 bg-dark border border-secondary rounded d-flex gap-3 align-items-center">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            @endif
                            <div>
                                <strong class="text-white d-block" style="font-size: 0.95rem;">{{ $course->title }}</strong>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $course->level_label }} &bull; {{ $course->enrollments_count }} élèves</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3">Aucune formation active publiée.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
