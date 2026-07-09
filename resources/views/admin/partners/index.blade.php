@extends('layouts.admin') @section('title', 'Partenaires') @section('breadcrumb')<li class="breadcrumb-item active">Partenaires</li>@endsection
@section('content')
<h4 class="fw-700 mb-4"><i class="fas fa-handshake text-cb-primary me-2"></i>Partenaires B2B</h4>
<div class="card-cabform"><div class="table-responsive"><table class="table table-cabform mb-0"><thead><tr><th>Entreprise</th><th>Contact</th><th>Inscriptions</th><th>Statut</th></tr></thead><tbody>@forelse($partners as $p)<tr><td class="fw-600">{{ $p->company_name }}</td><td>{{ $p->user->full_name ?? '-' }}</td><td>{{ $p->enrollments_count }}</td><td><span class="badge-cabform badge-success">Actif</span></td></tr>@empty<tr><td colspan="4" class="text-center text-cb-muted py-4">Aucun partenaire.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $partners->links() }}</div></div>
@endsection
