<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Villasis State Polytechnic University Learning Management System')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-layout">
        {{-- Sidebar --}}
        <aside class="app-sidebar" id="app-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-name">Villasis State Polytechnic</div>
                <div class="sidebar-brand-sub">University LMS</div>
            </div>

            <nav class="sidebar-nav">
                @auth
                    @if(auth()->user()->role === 'admin')
                        @include('partials.sidebar-admin')
                    @elseif(auth()->user()->role === 'teacher')
                        @include('partials.sidebar-teacher')
                    @else
                        @include('partials.sidebar-student')
                    @endif
                @endauth
            </nav>

            <div class="sidebar-footer">
                @auth
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                        <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
                    </div>
                </div>
                @endauth
            </div>
        </aside>

        {{-- Sidebar Overlay (mobile) --}}
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        {{-- Main Content --}}
        <main class="app-main">
            <header class="app-topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle menu">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 5h14M3 10h14M3 15h14"/>
                        </svg>
                    </button>
                    <div class="topbar-breadcrumb">
                        @yield('breadcrumb')
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="dropdown">
                        <button class="topbar-btn" data-dropdown aria-label="Notifications">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-item" style="padding: var(--space-3) var(--space-4); color: var(--text-dim); font-size: var(--text-xs);">No new notifications</div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="topbar-btn" data-dropdown aria-label="User menu">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu">
                            <a href="{{ route('profile.show') }}" class="dropdown-item">Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="app-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success" data-dismiss>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error" data-dismiss>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning" data-dismiss>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
