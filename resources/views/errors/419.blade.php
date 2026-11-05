<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
    <title>Session Expired — Redirecting</title>
    <script>
        window.location.replace("{{ route('login') }}");
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="error-page">
        <div>
            <div class="error-code">419</div>
            <h1 class="error-title">Session Expired</h1>
            <p class="error-message">Your session has timed out. Redirecting you to the sign in page...</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Go to Sign In</a>
        </div>
    </div>
</body>
</html>

