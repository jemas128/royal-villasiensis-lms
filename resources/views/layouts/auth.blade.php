<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign In') — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Villasis State Polytechnic University — Learning Management System')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-layout-v2">

    {{-- LEFT — Form Panel --}}
    <div class="auth-panel-left">
        <div class="auth-form-wrap">

            {{-- Brand --}}
            <div class="auth-v2-brand">
                <div class="auth-v2-brand-crest" style="font-size: 0.8rem; letter-spacing: 0.05em;">VSPU</div>
                <div class="auth-v2-brand-text">
                    <div class="auth-v2-brand-name">Villasis State Polytechnic University</div>
                    <div class="auth-v2-brand-sub">Learning Management System</div>
                </div>
            </div>

            @yield('content')
        </div>
    </div>

    {{-- RIGHT — Showcase Panel --}}
    <div class="auth-panel-right">
        {{-- Ambient orbs --}}
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>

        <div class="auth-showcase">
            {{-- Animated emblem --}}
            <div class="auth-showcase-emblem">
                <span class="auth-showcase-monogram" style="font-size: 2rem;">VSPU</span>
            </div>

            <h2 class="auth-showcase-name">Villasis State<br>Polytechnic University</h2>
            <div class="auth-showcase-dept">Learning Management System</div>

            {{-- Quote --}}
            <div class="auth-showcase-quote">
                <p class="auth-showcase-quote-text">
                    Excellence in knowledge,<br>distinction in character.
                </p>
            </div>

            {{-- Feature pills --}}
            <div class="auth-showcase-pills">
                <span class="auth-showcase-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Courses & Syllabus
                </span>
                <span class="auth-showcase-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Assignments
                </span>
                <span class="auth-showcase-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Timed Quizzes
                </span>
                <span class="auth-showcase-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Grade Tracking
                </span>
            </div>
        </div>
    </div>

</div>
</body>
</html>
