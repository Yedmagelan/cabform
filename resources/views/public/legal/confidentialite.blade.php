@extends('layouts.app')
@section('title', 'Politique de Confidentialité')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-cabform p-5">
                <h1 class="mb-4 text-cb-primary fw-800">Politique de Confidentialité</h1>
                <p class="text-cb-muted mb-5">Dernière mise à jour : {{ date('d/m/Y') }}</p>

                <h4 class="fw-700 mt-4 text-dark">1. Collecte des données personnelles</h4>
                <p>Nous collectons les informations que vous nous fournissez directement lors de la création d'un compte, l'achat d'une formation ou la prise de contact (nom, prénom, email, numéro de téléphone, historique des achats).</p>

                <h4 class="fw-700 mt-4 text-dark">2. Utilisation des données</h4>
                <p>Vos données sont utilisées pour :</p>
                <ul>
                    <li>Gérer votre compte et vos accès aux formations</li>
                    <li>Traiter vos paiements de manière sécurisée</li>
                    <li>Émettre vos certificats de réussite</li>
                    <li>Vous envoyer des communications liées à votre apprentissage</li>
                </ul>

                <h4 class="fw-700 mt-4 text-dark">3. Protection des données</h4>
                <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données personnelles contre l'accès, la modification, la divulgation ou la destruction non autorisée.</p>

                <h4 class="fw-700 mt-4 text-dark">4. Partage des données</h4>
                <p>Nous ne vendons ni ne louons vos données personnelles à des tiers. Elles peuvent être partagées uniquement avec nos prestataires de services (ex: passerelle de paiement) dans la limite nécessaire à l'exécution de leurs services.</p>

                <h4 class="fw-700 mt-4 text-dark">5. Vos droits</h4>
                <p>Vous disposez d'un droit d'accès, de rectification, de suppression et de portabilité de vos données. Pour exercer ces droits, vous pouvez nous contacter à l'adresse email : privacy@cabform.com.</p>
            </div>
        </div>
    </div>
</div>
@endsection
