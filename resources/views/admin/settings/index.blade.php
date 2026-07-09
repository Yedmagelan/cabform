@extends('layouts.admin') @section('title', 'Paramètres') @section('breadcrumb')<li class="breadcrumb-item active">Paramètres</li>@endsection
@section('content')
<h4 class="fw-700 mb-4"><i class="fas fa-cog text-cb-primary me-2"></i>Paramètres du système</h4>
@foreach($settings as $group => $items)
<div class="card-cabform p-4 mb-4">
    <h5 class="fw-700 mb-3"><span class="text-gradient">{{ ucfirst($group) }}</span></h5>
    @foreach($items as $s)
    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--cb-glass-border)!important;">
        <div><span class="fw-600">{{ $s->key }}</span>@if($s->description)<br><span class="text-cb-muted" style="font-size:0.8rem;">{{ $s->description }}</span>@endif</div>
        <span class="text-cb-primary fw-600">{{ Str::limit($s->value, 40) }}</span>
    </div>
    @endforeach
</div>
@endforeach
@endsection
