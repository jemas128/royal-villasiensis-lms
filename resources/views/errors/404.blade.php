<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Record Not Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="error-page">
        <div>
            <div class="error-code">404</div>
            <h1 class="error-title">Academic Record Not Found</h1>
            <p class="error-message">The requested course, deliverable, or portal document does not exist in the university repository.</p>
            <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="btn btn-primary">{{ auth()->check() ? 'Return to Dashboard' : 'Return to Home' }}</a>
        </div>
    </div>
</body>
</html>
