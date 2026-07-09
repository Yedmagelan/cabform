@extends('layouts.learner')

@section('title', 'Paiement Réussi')
@section('page_title', 'Confirmation de paiement')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 text-center">
        <div class="card card-instructor p-5 text-white">
            <div class="mx-auto mb-4 d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 100px; height: 100px; background: rgba(16,185,129,0.15) !important;">
                <i class="fas fa-check-circle" style="font-size: 3.5rem;"></i>
            </div>
            
            <h2 class="fw-bold text-success mb-2">Achat validé avec succès ! 🎉</h2>
            <p class="text-muted mb-4">Merci pour votre confiance. Votre inscription a bien été validée et vous pouvez désormais accéder aux cours.</p>

            <div class="p-3 bg-dark border border-secondary rounded mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Numéro de Commande :</span>
                    <strong>{{ $order->order_number }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Montant payé :</span>
                    <strong>{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Formation :</span>
                    <strong>{{ $order->items->first()?->course->title ?? '' }}</strong>
                </div>
            </div>

            <div class="d-grid gap-2">
                @if($order->items->first()?->course)
                    <a href="{{ route('learner.course.player', $order->items->first()->course->slug) }}" class="btn btn-premium py-2 fw-bold fs-6">Commencer la formation</a>
                @endif
                <a href="{{ route('learner.orders.show', $order->id) }}" class="btn btn-outline-secondary border-secondary text-white"><i class="fas fa-print me-1"></i> Consulter la Facture</a>
            </div>
        </div>
    </div>
</div>

<!-- Canvas Confetti effect -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var duration = 3 * 1000;
        var end = Date.now() + duration;

        (function frame() {
            confetti({
                particleCount: 3,
                angle: 60,
                spread: 55,
                origin: { x: 0 }
            });
            confetti({
                particleCount: 3,
                angle: 120,
                spread: 55,
                origin: { x: 1 }
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    });
</script>
@endsection
