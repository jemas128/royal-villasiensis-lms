<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Server Incident</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="error-page">
        <div>
            <div class="error-code">500</div>
            <h1 class="error-title">Institutional Server Exception</h1>
            <p class="error-message">An unexpected internal execution event occurred. System administrators have been alerted.</p>
            <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="btn btn-primary">{{ auth()->check() ? 'Return to Dashboard' : 'Return to Home' }}</a>
        </div>
    </div>
</body>
</html>
