@extends('layouts.admin') @section('title', 'FAQ') @section('breadcrumb')<li class="breadcrumb-item active">FAQ</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-700 mb-0"><i class="fas fa-question text-cb-primary me-2"></i>FAQ</h4><a href="#" class="btn btn-cabform btn-cabform-primary btn-cabform-sm"><i class="fas fa-plus me-1"></i>Ajouter</a></div>
<div class="card-cabform p-4">@forelse($faqs as $f)<div class="py-2 border-bottom" style="border-color:var(--cb-glass-border)!important;"><span class="fw-600">{{ $f->question }}</span><br><span class="text-cb-muted" style="font-size:0.85rem;">{{ Str::limit($f->answer, 80) }}</span></div>@empty<p class="text-cb-muted text-center">Aucune FAQ.</p>@endforelse<div class="mt-3">{{ $faqs->links() }}</div></div>
@endsection
