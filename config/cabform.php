<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CabForm Platform Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique à la plateforme de formation CabForm.
    |
    */

    // Nom officiel du cabinet
    'name' => env('CABFORM_NAME', 'CabForm'),

    // Slogan
    'tagline' => env('CABFORM_TAGLINE', 'Excellence en Formation & Certification'),

    // Devise
    'currency' => env('CABFORM_CURRENCY', 'XOF'),
    'currency_symbol' => env('CABFORM_CURRENCY_SYMBOL', 'FCFA'),

    // Langues supportées
    'locales' => ['fr', 'en'],
    'default_locale' => 'fr',

    // Certificats
    'certificate' => [
        'prefix' => 'CABF',
        'logo_path' => 'assets/img/Logo-CabForm.png',
        'signature_path' => 'assets/img/signature.png',
        'validity_years' => 3,
    ],

    // Upload
    'upload' => [
        'max_video_size' => 512000, // 500 MB en KB
        'max_file_size' => 10240,   // 10 MB en KB
        'allowed_video' => ['mp4', 'webm', 'avi', 'mov'],
        'allowed_documents' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'],
        'allowed_images' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
        'allowed_audio' => ['mp3', 'wav', 'ogg'],
    ],

    // Pagination
    'pagination' => [
        'catalog' => 12,
        'admin_list' => 20,
        'blog' => 9,
        'forum' => 15,
    ],

    // Quiz
    'quiz' => [
        'max_attempts' => 3,
        'passing_score' => 70,
        'time_limit_minutes' => 60,
    ],

    // Design
    'colors' => [
        'primary' => '#0500d8',
        'dark' => '#00000f',
        'secondary' => '#0a0a2e',
        'accent' => '#4d6bfe',
    ],

];
