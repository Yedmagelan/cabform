@extends('layouts.app')
@section('title', 'Politique des Cookies')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-cabform p-5">
                <h1 class="mb-4 text-cb-primary fw-800">Politique d'Utilisation des Cookies</h1>
                <p class="text-cb-muted mb-5">Dernière mise à jour : {{ date('d/m/Y') }}</p>

                <h4 class="fw-700 mt-4 text-dark">1. Qu'est-ce qu'un cookie ?</h4>
                <p>Un cookie est un petit fichier texte déposé sur votre terminal (ordinateur, tablette, smartphone) lors de la visite d'un site ou de la consultation d'une publicité. Il a pour but de collecter des informations relatives à votre navigation et de vous adresser des services adaptés à votre terminal.</p>

                <h4 class="fw-700 mt-4 text-dark">2. Les cookies que nous utilisons</h4>
                <ul>
                    <li><strong>Cookies strictement nécessaires :</strong> Ces cookies sont indispensables au fonctionnement du site (ex: conservation de votre session de connexion, mémorisation de votre panier d'achat).</li>
                    <li><strong>Cookies de performance et statistiques :</strong> Ils nous permettent de connaître l'utilisation et les performances de notre site et d'en améliorer le fonctionnement (ex: pages le plus souvent consultées).</li>
                    <li><strong>Cookies de fonctionnalités :</strong> Ils permettent de personnaliser votre expérience sur notre site (ex: mémorisation de vos préférences d'affichage).</li>
                </ul>

                <h4 class="fw-700 mt-4 text-dark">3. Gestion de vos préférences</h4>
                <p>Vous pouvez à tout moment choisir de désactiver ces cookies en paramétrant votre navigateur. Toutefois, nous vous rappelons que le paramétrage est susceptible de modifier vos conditions d'accès à nos services nécessitant l'utilisation de cookies.</p>
                <p>Pour la gestion des cookies et de vos choix, la configuration de chaque navigateur est différente. Elle est décrite dans le menu d'aide de votre navigateur, qui vous permettra de savoir de quelle manière modifier vos souhaits en matière de cookies.</p>
            </div>
        </div>
    </div>
</div>
@endsection
