@extends('layouts.admin')
@section('title', 'Rapports & Exports')
@section('breadcrumb')
    <li class="breadcrumb-item active">Rapports</li>
@endsection

@section('content')
<h4 class="fw-700 mb-4"><i class="fas fa-chart-bar text-cb-primary me-2"></i>Rapports & Statistiques</h4>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="dashboard-card">
            <div class="card-icon primary mx-auto"><i class="fas fa-users"></i></div>
            <div class="text-cb-muted mt-2 fw-600" style="font-size:0.9rem;">Total Utilisateurs</div>
            <h3 class="fw-800 text-cb-secondary mt-1">{{ $stats['users_total'] }}</h3>
            <span class="text-success" style="font-size:0.8rem;"><i class="fas fa-plus me-1"></i>{{ $stats['users_new_month'] }} ce mois-ci</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-card">
            <div class="card-icon success mx-auto"><i class="fas fa-money-bill-wave"></i></div>
            <div class="text-cb-muted mt-2 fw-600" style="font-size:0.9rem;">Chiffre d'Affaires</div>
            <h3 class="fw-800 text-cb-secondary mt-1">{{ number_format($stats['revenue_total'], 2, ',', ' ') }} €</h3>
            <span class="text-cb-muted" style="font-size:0.8rem;">Sur {{ $stats['orders_count'] }} commandes payées</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-card">
            <div class="card-icon warning mx-auto"><i class="fas fa-graduation-cap"></i></div>
            <div class="text-cb-muted mt-2 fw-600" style="font-size:0.9rem;">Formations Actives</div>
            <h3 class="fw-800 text-cb-secondary mt-1">{{ $stats['courses_total'] }}</h3>
            <span class="text-primary" style="font-size:0.8rem;"><i class="fas fa-chart-line me-1"></i>{{ number_format($stats['avg_progress'], 1) }}% de progression moyenne</span>
        </div>
    </div>
</div>

<!-- CSV Exports -->
<div class="card-cabform p-4">
    <h5 class="fw-700 mb-4"><i class="fas fa-file-export text-cb-primary me-2"></i>Export des Données au format CSV</h5>
    <p class="text-cb-muted mb-4">Sélectionnez le rapport que vous souhaitez exporter. Le fichier sera téléchargé instantanément et compatible avec Excel.</p>
    
    <div class="row g-4">
        <!-- Export Utilisateurs -->
        <div class="col-md-4">
            <div class="p-3 rounded-cb text-center" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border);">
                <i class="fas fa-users-cog mb-3" style="font-size: 2.5rem; color: var(--cb-primary);"></i>
                <h6 class="fw-700 mb-2">Utilisateurs</h6>
                <p class="text-cb-muted mb-3" style="font-size:0.85rem;">Liste complète des comptes avec rôles et dates d'inscription.</p>
                <form method="POST" action="{{ route('admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="users">
                    <button type="submit" class="btn btn-cabform btn-cabform-outline btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </form>
            </div>
        </div>

        <!-- Export Financier -->
        <div class="col-md-4">
            <div class="p-3 rounded-cb text-center" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border);">
                <i class="fas fa-file-invoice-dollar mb-3" style="font-size: 2.5rem; color: var(--cb-success);"></i>
                <h6 class="fw-700 mb-2">Financier (Commandes)</h6>
                <p class="text-cb-muted mb-3" style="font-size:0.85rem;">Historique des ventes avec montants et modes de paiement.</p>
                <form method="POST" action="{{ route('admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="financial">
                    <button type="submit" class="btn btn-cabform btn-cabform-outline btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </form>
            </div>
        </div>

        <!-- Export Formations -->
        <div class="col-md-4">
            <div class="p-3 rounded-cb text-center" style="background: var(--cb-glass-bg); border: 1px solid var(--cb-glass-border);">
                <i class="fas fa-book-reader mb-3" style="font-size: 2.5rem; color: var(--cb-warning);"></i>
                <h6 class="fw-700 mb-2">Formations & Catalogue</h6>
                <p class="text-cb-muted mb-3" style="font-size:0.85rem;">Liste des cours avec prix, nombre d'inscrits et version.</p>
                <form method="POST" action="{{ route('admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="courses">
                    <button type="submit" class="btn btn-cabform btn-cabform-outline btn-sm w-100">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
