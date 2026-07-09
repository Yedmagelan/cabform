@extends('layouts.learner')

@section('title', 'Détails de la commande')
@section('page_title', 'Facture d\'achat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-instructor p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-white mb-1">Détails de la facture</h5>
                    <span class="text-muted" style="font-size: 0.85rem;">Commande : {{ $order->order_number }}</span>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-print me-1"></i> Imprimer</button>
                    <a href="{{ route('learner.orders.index') }}" class="btn btn-sm btn-outline-secondary border-secondary text-white"><i class="fas fa-arrow-left"></i></a>
                </div>
            </div>

            <!-- Invoice details header -->
            <div class="row g-3 mb-4 text-white">
                <div class="col-md-6">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Date d'achat :</span>
                    <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Statut du paiement :</span>
                    <span class="badge bg-{{ $order->status === 'paid' ? 'success' : 'warning' }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <!-- Items list -->
            <div class="mb-4">
                <h6 class="fw-bold text-indigo mb-3">Formations achetées :</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Formation</th>
                                <th style="width: 20%;">Prix unitaire</th>
                                <th style="width: 15%;">Qté</th>
                                <th style="width: 25%;" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong class="text-white">{{ $item->course->title ?? 'Formation' }}</strong>
                                        <span class="d-block text-muted" style="font-size: 0.8rem;">{{ $item->course->category->name ?? '' }}</span>
                                    </td>
                                    <td>{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold text-white">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial breakdown -->
            <div class="row justify-content-end text-white">
                <div class="col-md-5">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sous-total :</span>
                        <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                        <span class="text-muted">Taxes / TVA :</span>
                        <span>0 FCFA</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong class="fs-5">TOTAL PAYÉ :</strong>
                        <strong class="fs-5 text-indigo">{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong>
                    </div>
                </div>
            </div>

            @if($order->payment)
                <hr class="border-secondary my-4">
                
                <!-- Payment info -->
                <div class="p-3 bg-dark border border-secondary rounded text-white" style="font-size: 0.85rem;">
                    <h6 class="fw-bold mb-2 text-indigo">Informations de transaction :</h6>
                    <div class="row g-2">
                        <div class="col-sm-6"><strong>Moyen de paiement :</strong> {{ strtoupper($order->payment->channel) }}</div>
                        <div class="col-sm-6"><strong>ID de transaction :</strong> <code class="text-white">{{ $order->payment->transaction_id }}</code></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
