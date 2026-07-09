@extends('layouts.app')
@section('title', 'Mentions Légales')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-cabform p-5">
                <h1 class="mb-4 text-cb-primary fw-800">Mentions Légales</h1>
                <p class="text-cb-muted mb-5">Dernière mise à jour : {{ date('d/m/Y') }}</p>

                <h4 class="fw-700 mt-4 text-dark">1. Éditeur du site</h4>
                <p>Le site <strong>CabForm</strong> est édité par la société [Nom de la société], [Forme juridique] au capital de [Montant] €, dont le siège social est situé à [Adresse complète].</p>
                <p>Immatriculée au Registre du Commerce et des Sociétés sous le numéro : [Numéro RCS]</p>
                <p>Email : contact@cabform.com <br> Téléphone : +225 XX XX XX XX</p>

                <h4 class="fw-700 mt-4 text-dark">2. Directeur de la publication</h4>
                <p>Le directeur de la publication du site est [Nom du Directeur], en qualité de [Fonction].</p>

                <h4 class="fw-700 mt-4 text-dark">3. Hébergement</h4>
                <p>Le site est hébergé par [Nom de l'hébergeur], [Forme juridique], situé à [Adresse de l'hébergeur].</p>
                <p>Contact de l'hébergeur : [Téléphone/Email hébergeur]</p>

                <h4 class="fw-700 mt-4 text-dark">4. Propriété intellectuelle</h4>
                <p>L'ensemble du contenu (textes, images, vidéos, code, logos) présent sur le site CabForm est la propriété exclusive de [Nom de la société] ou fait l'objet d'une autorisation d'utilisation. Toute reproduction, distribution, modification ou utilisation non autorisée est strictement interdite.</p>
            </div>
        </div>
    </div>
</div>
@endsection
