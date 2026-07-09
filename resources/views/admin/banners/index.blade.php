@extends('layouts.admin') @section('title', 'Bannières') @section('breadcrumb')<li class="breadcrumb-item active">Bannières</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h4 class="fw-700 mb-0"><i class="fas fa-images text-cb-primary me-2"></i>Bannières</h4><a href="#" class="btn btn-cabform btn-cabform-primary btn-cabform-sm"><i class="fas fa-plus me-1"></i>Ajouter</a></div>
<div class="card-cabform p-4">@forelse($banners as $b)<div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:var(--cb-glass-border)!important;"><span class="fw-600">{{ $b->title }}</span><span class="badge-cabform {{ $b->is_active ? 'badge-success' : 'badge-danger' }}">{{ $b->is_active ? 'Actif' : 'Inactif' }}</span></div>@empty<p class="text-cb-muted text-center">Aucune bannière.</p>@endforelse<div class="mt-3">{{ $banners->links() }}</div></div>
@endsection
