<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0; url={{ auth()->check() ? route('dashboard') : route('login') }}">
    <title>Redirecting — Access Restricted</title>
    <script>
        window.location.replace("{{ auth()->check() ? route('dashboard') : route('login') }}");
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="error-page">
        <div>
            <div class="error-code">403</div>
            <h1 class="error-title">Restricted Academic Access</h1>
            <p class="error-message">You do not possess the required institutional privileges to access this area. Automatically redirecting you...</p>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary">
                {{ auth()->check() ? 'Go to Dashboard' : 'Go to Sign In' }}
            </a>
        </div>
    </div>
</body>
</html>
