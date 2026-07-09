@extends('layouts.learner')

@section('title', 'Mes Commandes')
@section('page_title', 'Historique des Commandes')

@section('content')
<div class="card card-instructor p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-white mb-1">Historique d'achat</h5>
            <span class="text-muted" style="font-size: 0.85rem;">Consultez vos transactions, factures et statuts d'inscription.</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Commande #</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><code class="text-indigo fw-bold" style="color: #818cf8;">{{ $order->order_number }}</code></td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="fw-bold text-white">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ match($order->status) {
                                    'pending' => 'En attente',
                                    'paid' => 'Payée',
                                    'failed' => 'Échouée',
                                    'refunded' => 'Remboursée',
                                    default => $order->status
                                } }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('learner.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-eye me-1"></i> Facture / Détails</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">Aucune commande passée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
