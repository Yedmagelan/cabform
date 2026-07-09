@extends('layouts.app')
@section('title', 'FAQ')
@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <div class="text-center mb-5 fade-in">
            <span class="section-subtitle"><i class="fas fa-question-circle me-2"></i>FAQ</span>
            <h1 class="section-title">Questions <span class="text-gradient">fréquentes</span></h1>
            <p class="section-description">Trouvez rapidement des réponses à vos questions.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @foreach($faqs as $category => $items)
                    <h4 class="fw-700 mb-3 mt-4 fade-in"><i class="fas fa-folder text-cb-primary me-2"></i>{{ $category }}</h4>
                    <div class="accordion accordion-cabform mb-4 fade-in">
                        @foreach($items as $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}">{{ $faq->question }}</button></h2>
                                <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse"><div class="accordion-body">{{ $faq->answer }}</div></div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
