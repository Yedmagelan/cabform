<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'CabForm - Plateforme de Formation et Certification en Ligne. Formations certifiantes, cours en ligne, certificats vérifiables.')">
    <meta name="keywords" content="formation en ligne, certification, e-learning, cours, CabForm">
    <meta name="author" content="CabForm">

    <title>@yield('title', 'CabForm') — Formation & Certification en Ligne</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Logo-CabForm.png') }}">

    <!-- Google Fonts - DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CabForm CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Page Loader -->
    <div class="loader-cabform" id="page-loader">
        <div class="text-center">
            <div class="spinner-cabform mb-3"></div>
            <img src="{{ asset('assets/img/Logo-CabForm.png') }}" alt="CabForm" height="40" class="float-animation">
        </div>
    </div>

    <!-- Ajax Loader (inline) -->
    <div id="ajax-loader" class="d-none position-fixed top-0 start-0 w-100" style="z-index: 9999; height: 3px;">
        <div class="progress-cabform" style="height: 3px; border-radius: 0;">
            <div class="progress-bar" style="width: 100%; animation: shimmer 1s infinite;"></div>
        </div>
    </div>

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- CabForm JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
