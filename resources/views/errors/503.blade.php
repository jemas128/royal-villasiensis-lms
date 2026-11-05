<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="4">
    <title>503 — Service Temporarily Busy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="error-page">
        <div>
            <div class="error-code">503</div>
            <h1 class="error-title">High Institutional Traffic</h1>
            <p class="error-message">The university servers are currently handling a peak traffic surge. Automatically reconnecting you in a few seconds...</p>
            <div style="margin-top: var(--space-4);">
                <button type="button" class="btn btn-primary" onclick="window.location.reload();">
                    Retry Connection
                </button>
            </div>
        </div>
    </div>
</body>
</html>

