@extends('layouts.admin')
@section('title', 'Tableau de bord')
@section('breadcrumb')
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('content')
<!-- Stats Row -->
<div class="row g-4 mb-4">
    @php
        $cards = [
            ['icon' => 'fa-users', 'color' => 'primary', 'label' => 'Utilisateurs', 'value' => $stats['total_users']],
            ['icon' => 'fa-book-open', 'color' => 'success', 'label' => 'Formations publiées', 'value' => $stats['published_courses']],
            ['icon' => 'fa-user-graduate', 'color' => 'warning', 'label' => 'Inscriptions actives', 'value' => $stats['active_enrollments']],
            ['icon' => 'fa-credit-card', 'color' => 'danger', 'label' => 'Revenu du mois', 'value' => number_format($stats['monthly_revenue'], 0, ',', ' ') . ' FCFA'],
        ];
    @endphp
    @foreach($cards as $card)
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="card-icon {{ $card['color'] }}"><i class="fas {{ $card['icon'] }}"></i></div>
                <div class="text-cb-muted" style="font-size: 0.85rem;">{{ $card['label'] }}</div>
                <div class="fw-800" style="font-size: 1.6rem;">{{ $card['value'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<!-- Summary Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-chart-line text-cb-primary me-2"></i>Revenus mensuels</h5>
            <div class="row g-3">
                @foreach($monthlyRevenue as $data)
                    <div class="col-auto">
                        <div class="dashboard-card text-center" style="min-width: 80px;">
                            <div class="text-cb-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::create()->month($data->month)->translatedFormat('M') }} {{ $data->year }}</div>
                            <div class="fw-700 text-cb-primary" style="font-size: 1rem;">{{ number_format($data->total, 0, ',', ' ') }}</div>
                        </div>
                    </div>
                @endforeach
                @if($monthlyRevenue->isEmpty())
                    <div class="col-12 text-center py-4">
                        <p class="text-cb-muted mb-0">Aucune donnée de revenu disponible.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-cabform p-4 h-100">
            <h5 class="fw-700 mb-4"><i class="fas fa-chart-pie text-cb-success me-2"></i>Résumé</h5>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-cb-muted">Apprenants</span>
                    <span class="fw-700">{{ $stats['total_learners'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-cb-muted">Formateurs</span>
                    <span class="fw-700">{{ $stats['total_instructors'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-cb-muted">Certificats délivrés</span>
                    <span class="fw-700">{{ $stats['total_certificates'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-cb-muted">Commandes en attente</span>
                    <span class="fw-700 text-cb-warning">{{ $stats['pending_orders'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-cb-muted">Revenu total</span>
                    <span class="fw-700 text-cb-success">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-user-graduate text-cb-primary me-2"></i>Inscriptions récentes</h5>
            @forelse($recentEnrollments->take(5) as $enrollment)
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--cb-glass-border) !important;">
                    <div class="user-avatar" style="width:36px;height:36px;font-size:0.7rem;">{{ $enrollment->user->initials ?? 'U' }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-600" style="font-size: 0.85rem;">{{ $enrollment->user->full_name ?? 'Utilisateur' }}</div>
                        <div class="text-cb-muted" style="font-size: 0.75rem;">{{ Str::limit($enrollment->course->title ?? '', 40) }}</div>
                    </div>
                    <span class="badge-cabform {{ $enrollment->status === 'active' ? 'badge-success' : 'badge-primary' }}">{{ ucfirst($enrollment->status) }}</span>
                </div>
            @empty
                <p class="text-cb-muted text-center py-3 mb-0">Aucune inscription récente.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-cabform p-4">
            <h5 class="fw-700 mb-4"><i class="fas fa-credit-card text-cb-success me-2"></i>Paiements récents</h5>
            @forelse($recentPayments->take(5) as $payment)
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--cb-glass-border) !important;">
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(var(--cb-success-rgb),0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-check text-cb-success" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-600" style="font-size: 0.85rem;">{{ $payment->user->full_name ?? 'Utilisateur' }}</div>
                        <div class="text-cb-muted" style="font-size: 0.75rem;">{{ $payment->channel_label }} — {{ $payment->paid_at?->diffForHumans() }}</div>
                    </div>
                    <span class="fw-700 text-cb-success" style="font-size: 0.9rem;">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                </div>
            @empty
                <p class="text-cb-muted text-center py-3 mb-0">Aucun paiement récent.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
