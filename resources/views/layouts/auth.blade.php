<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentification') — CabForm</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Logo-CabForm.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        .auth-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; overflow: hidden; }
        .auth-wrapper::before { content: ''; position: absolute; top: -30%; right: -20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(5,0,216,0.1) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
        .auth-wrapper::after { content: ''; position: absolute; bottom: -20%; left: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(77,107,254,0.06) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
        .auth-card { background: var(--cb-gradient-card); backdrop-filter: blur(20px); border: 1px solid var(--cb-glass-border); border-radius: var(--cb-border-radius-xl); padding: 3rem; width: 100%; max-width: 480px; position: relative; z-index: 1; }
        .auth-card .auth-logo { text-align: center; margin-bottom: 2rem; }
        .auth-card .auth-logo img { height: 50px; margin-bottom: 1rem; }
        .auth-card .auth-title { font-size: 1.75rem; font-weight: 800; text-align: center; margin-bottom: 0.5rem; }
        .auth-card .auth-subtitle { text-align: center; color: var(--cb-text-secondary); margin-bottom: 2rem; font-size: 0.95rem; }
        .auth-divider { display: flex; align-items: center; margin: 1.5rem 0; color: var(--cb-text-muted); font-size: 0.85rem; }
        .auth-divider::before, .auth-divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--cb-glass-border); }
        .auth-divider span { padding: 0 12px; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        @yield('content')
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
