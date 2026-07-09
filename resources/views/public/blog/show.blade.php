@extends('layouts.app')
@section('title', $post->meta_title ?? $post->title)
@section('meta_description', $post->meta_description ?? Str::limit(strip_tags($post->content), 160))
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 fade-in">
                <a href="{{ route('blog.index') }}" class="text-cb-muted mb-4 d-inline-block"><i class="fas fa-arrow-left me-1"></i>Retour au blog</a>
                @if($post->category)<span class="badge-cabform badge-primary mb-3 d-inline-block ms-3">{{ $post->category->name }}</span>@endif
                <h1 style="font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 800; margin-bottom: 1.5rem;">{{ $post->title }}</h1>
                <div class="d-flex align-items-center gap-3 mb-4 text-cb-muted" style="font-size: 0.9rem;">
                    <div class="d-flex align-items-center gap-2"><div class="user-avatar" style="width:32px;height:32px;font-size:0.7rem;">{{ $post->author->initials ?? 'CF' }}</div><span>{{ $post->author->full_name ?? 'CabForm' }}</span></div>
                    <span>•</span>
                    <span><i class="fas fa-calendar me-1"></i>{{ $post->published_at?->format('d/m/Y') }}</span>
                    <span>•</span>
                    <span><i class="fas fa-clock me-1"></i>{{ $post->read_time }} min de lecture</span>
                    <span>•</span>
                    <span><i class="fas fa-eye me-1"></i>{{ $post->views_count }} vues</span>
                </div>
                <div class="card-cabform p-4 mb-5">
                    <div class="text-cb-secondary" style="line-height: 1.9; font-size: 1.05rem;">{!! $post->content !!}</div>
                </div>
                @if($relatedPosts->count())
                <h4 class="fw-700 mb-4">Articles similaires</h4>
                <div class="row g-4">
                    @foreach($relatedPosts as $rp)
                    <div class="col-md-4"><div class="dashboard-card"><h6 class="fw-600">{{ Str::limit($rp->title, 50) }}</h6><a href="{{ route('blog.show', $rp->slug) }}" class="text-cb-primary" style="font-size:0.85rem;">Lire <i class="fas fa-arrow-right ms-1"></i></a></div></div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
