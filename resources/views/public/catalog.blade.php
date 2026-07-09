@extends('layouts.app')

@section('title', 'Catalogue des formations')

@section('content')
<section style="padding: 120px 0 60px;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5 fade-in">
            <span class="section-subtitle"><i class="fas fa-book-open me-2"></i>Catalogue</span>
            <h1 class="section-title">Nos <span class="text-gradient">Formations</span></h1>
            <p class="section-description">Trouvez la formation qui correspond à vos objectifs professionnels et développez vos compétences.</p>
        </div>

        <!-- Filters Bar -->
        <div class="card-cabform p-4 mb-4 fade-in">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label-cabform">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border); color: var(--cb-text-muted);"><i class="fas fa-search"></i></span>
                        <input type="text" id="catalog-search" class="form-control form-control-cabform" placeholder="Rechercher une formation..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label-cabform">Catégorie</label>
                    <select id="filter-category" class="form-control form-control-cabform">
                        <option value="">Toutes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label-cabform">Niveau</label>
                    <select id="filter-level" class="form-control form-control-cabform">
                        <option value="">Tous</option>
                        <option value="debutant" {{ request('level') == 'debutant' ? 'selected' : '' }}>Débutant</option>
                        <option value="intermediaire" {{ request('level') == 'intermediaire' ? 'selected' : '' }}>Intermédiaire</option>
                        <option value="avance" {{ request('level') == 'avance' ? 'selected' : '' }}>Avancé</option>
                        <option value="expert" {{ request('level') == 'expert' ? 'selected' : '' }}>Expert</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label-cabform">Trier par</label>
                    <select id="filter-sort" class="form-control form-control-cabform">
                        <option value="latest">Plus récent</option>
                        <option value="popular">Plus populaire</option>
                        <option value="rating">Mieux noté</option>
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix décroissant</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <button id="btn-filter" class="btn btn-cabform btn-cabform-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>

        <!-- Results count -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-cb-muted mb-0" id="results-count"><strong>{{ $courses->total() }}</strong> formations trouvées</p>
            <div class="d-flex gap-2">
                <button class="btn btn-cabform-glass btn-cabform-sm active" data-view="grid"><i class="fas fa-th"></i></button>
                <button class="btn btn-cabform-glass btn-cabform-sm" data-view="list"><i class="fas fa-list"></i></button>
            </div>
        </div>

        <!-- Course Grid -->
        <div id="course-grid">
            @include('public.partials.course-grid', ['courses' => $courses])
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5" id="pagination-container">
            {{ $courses->withQueryString()->links() }}
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Ajax filter
    function loadCatalog(page) {
        page = page || 1;
        var params = {
            search: $('#catalog-search').val(),
            category: $('#filter-category').val(),
            level: $('#filter-level').val(),
            sort: $('#filter-sort').val(),
            page: page
        };

        $.ajax({
            url: '{{ route("catalog") }}',
            data: params,
            beforeSend: function() {
                $('#course-grid').css('opacity', '0.5');
            },
            success: function(data) {
                if (data.html) {
                    $('#course-grid').html(data.html).css('opacity', '1');
                    $('#pagination-container').html(data.pagination);
                    $('#results-count').html('<strong>' + data.total + '</strong> formations trouvées');
                }
            },
            error: function() {
                $('#course-grid').css('opacity', '1');
            }
        });
    }

    window.loadCatalog = loadCatalog;

    $('#btn-filter').on('click', function() { loadCatalog(1); });
    $('#filter-category, #filter-level, #filter-sort').on('change', function() { loadCatalog(1); });
});
</script>
@endpush
