@extends('layouts.app')
@section('title', 'Conditions Générales d\'Utilisation et de Vente (CGU/CGV)')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-cabform p-5">
                <h1 class="mb-4 text-cb-primary fw-800">Conditions Générales d'Utilisation et de Vente (CGU/CGV)</h1>
                <p class="text-cb-muted mb-5">Dernière mise à jour : {{ date('d/m/Y') }}</p>

                <h4 class="fw-700 mt-4 text-dark">1. Objet</h4>
                <p>Les présentes Conditions Générales d'Utilisation et de Vente régissent l'utilisation de la plateforme CabForm et l'achat de formations en ligne proposées par notre plateforme.</p>

                <h4 class="fw-700 mt-4 text-dark">2. Accès aux formations</h4>
                <p>L'accès aux formations payantes est conditionné par le paiement intégral du prix indiqué. Dès validation du paiement, l'utilisateur a accès au contenu dans son espace apprenant.</p>

                <h4 class="fw-700 mt-4 text-dark">3. Tarifs et Paiement</h4>
                <p>Les prix de nos formations sont indiqués en [Devise] toutes taxes comprises (TTC). Les paiements sont sécurisés et traités par nos partenaires financiers (CinetPay, etc.).</p>

                <h4 class="fw-700 mt-4 text-dark">4. Droit de rétractation</h4>
                <p>Conformément à la législation en vigueur sur les contenus numériques fournis sur un support immatériel, le droit de rétractation ne peut être exercé une fois que l'exécution de la formation a commencé avec votre accord préalable exprès.</p>

                <h4 class="fw-700 mt-4 text-dark">5. Responsabilités de l'utilisateur</h4>
                <p>L'utilisateur s'engage à utiliser la plateforme de manière loyale et à ne pas partager ses identifiants de connexion, ni redistribuer les contenus de formation protégés par le droit d'auteur.</p>
            </div>
        </div>
    </div>
</div>
@endsection
