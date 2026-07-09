@extends('layouts.app')
@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? '')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="fade-in">
                    <h1 class="section-title mb-4">{{ $page->title }}</h1>
                    <div class="card-cabform p-4">
                        <div class="text-cb-secondary" style="line-height: 1.8;">{!! $page->content !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
