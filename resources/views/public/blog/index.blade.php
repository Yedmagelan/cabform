@extends('layouts.app')
@section('title', 'Blog')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="text-center mb-5 fade-in">
            <span class="section-subtitle"><i class="fas fa-newspaper me-2"></i>Blog</span>
            <h1 class="section-title">Nos <span class="text-gradient">Articles</span></h1>
            <p class="section-description">Découvrez nos derniers articles, conseils et actualités sur la formation professionnelle.</p>
        </div>
        <div class="row g-4">
            @forelse($posts as $post)
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="card-cabform h-100">
                    <div class="card-img-wrapper">
                        <div style="height: 200px; background: var(--cb-gradient-dark); display: flex; align-items: center; justify-content: center;">
                            @if($post->featured_image)<img src="{{ asset('storage/'.$post->featured_image) }}" class="card-img-top" alt="{{ $post->title }}">@else<i class="fas fa-newspaper" style="font-size: 3rem; opacity: 0.3;"></i>@endif
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        @if($post->category)<span class="badge-cabform badge-primary mb-2">{{ $post->category->name }}</span>@endif
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text flex-grow-1">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 120) }}</p>
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-3" style="border-top: 1px solid var(--cb-glass-border);">
                            <div class="text-cb-muted" style="font-size: 0.8rem;"><i class="fas fa-user me-1"></i>{{ $post->author->full_name ?? 'CabForm' }}</div>
                            <div class="text-cb-muted" style="font-size: 0.8rem;"><i class="fas fa-clock me-1"></i>{{ $post->read_time }} min</div>
                        </div>
                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-cabform btn-cabform-outline btn-cabform-sm w-100 mt-3">Lire l'article <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5"><i class="fas fa-newspaper text-cb-muted" style="font-size:4rem;opacity:0.2;"></i><h5 class="text-cb-muted mt-3">Aucun article pour le moment</h5></div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-5">{{ $posts->links() }}</div>
    </div>
</section>
@endsection
