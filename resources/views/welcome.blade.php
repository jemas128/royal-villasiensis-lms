<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Villasis State Polytechnic University — Academic Portal</title>
    <meta name="description" content="Official Academic Portal and Learning Management System for Villasis State Polytechnic University.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: var(--black); color: var(--ivory);">

    {{-- Collegiate Top Navigation Bar --}}
    <nav class="landing-nav" style="background: var(--black); border-bottom: 1px solid var(--border-dark);">
        <a href="{{ route('home') }}" class="landing-nav-brand flex items-center gap-3">
            <div class="flex items-center justify-center font-bold text-serif" style="width: 38px; height: 38px; border-radius: var(--radius-md); background: var(--gold-dark); color: var(--warm-white); font-size: 0.8rem;">
                VSPU
            </div>
            <div>
                <span class="block text-ivory text-serif font-semibold" style="letter-spacing: 0.04em;">VILLASIS STATE POLYTECHNIC UNIVERSITY</span>
                <span class="block text-gold uppercase font-semibold" style="font-size: 0.625rem; letter-spacing: 0.14em;">Academic Management</span>
            </div>
        </a>

        <div class="landing-nav-links">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Go to Portal Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Sign In
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Student Registration
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    {{-- Refined Hero Section --}}
    <header class="landing-hero" style="padding-top: calc(64px + var(--space-12)); padding-bottom: var(--space-12); background: var(--black);">
        <div style="max-width: 860px; margin: 0 auto; text-align: center;">
            <div class="inline-flex items-center gap-2 mb-6" style="padding: 6px 16px; border: 1px solid var(--border-dark); border-radius: 999px;">
                <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--gold); display: inline-block;"></span>
                <span class="text-xs text-gold uppercase font-semibold" style="letter-spacing: 0.12em;">University Learning Management System</span>
            </div>

            <h1 class="landing-hero-title mb-4" style="font-size: clamp(1.75rem, 4vw, 2.75rem);">
                Distinction in Scholarship, <br><span class="text-gold">Excellence in Leadership</span>
            </h1>

            <p class="landing-hero-subtitle text-secondary mb-8 mx-auto" style="max-width: 620px;">
                The central learning gateway for Villasis State Polytechnic University. Providing modern curriculum delivery, assessment analytics, and academic administration.
            </p>

            <div class="landing-hero-actions flex justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Open Academic Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Access Portal
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Apply for Admission
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Institutional Metrics Strip --}}
    <section class="landing-section" style="background: var(--charcoal); padding: var(--space-8); border-top: 1px solid var(--border-dark); border-bottom: 1px solid var(--border-dark);">
        <div style="max-width: 1040px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-6); text-align: center;">
            <div class="p-4">
                <div class="text-3xl font-bold text-gold text-serif">{{ $departmentsCount ?: 4 }}</div>
                <div class="text-xs text-muted uppercase font-medium mt-1" style="letter-spacing: 0.08em;">Academic Colleges</div>
            </div>
            <div class="p-4">
                <div class="text-3xl font-bold text-ivory text-serif">{{ $coursesCount ?: 12 }}</div>
                <div class="text-xs text-muted uppercase font-medium mt-1" style="letter-spacing: 0.08em;">Accredited Courses</div>
            </div>
            <div class="p-4">
                <div class="text-3xl font-bold text-gold text-serif">{{ $facultyCount ?: 18 }}</div>
                <div class="text-xs text-muted uppercase font-medium mt-1" style="letter-spacing: 0.08em;">Distinguished Faculty</div>
            </div>
            <div class="p-4">
                <div class="text-3xl font-bold text-ivory text-serif">{{ $studentsCount ?: 150 }}+</div>
                <div class="text-xs text-muted uppercase font-medium mt-1" style="letter-spacing: 0.08em;">Enrolled Scholars</div>
            </div>
        </div>
    </section>

    {{-- Academic Capabilities Grid --}}
    <section class="landing-section" style="padding: var(--space-16) var(--space-8);">
        <div style="max-width: 1040px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: var(--space-12);">
                <h2 class="landing-section-title" style="margin-bottom: var(--space-2);">Integrated Academic Ecosystem</h2>
                <p class="landing-section-subtitle">Dedicated instructional tools crafted for scholars, educators, and university leadership</p>
            </div>

            <div class="landing-features" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: var(--space-6);">
                <div class="landing-feature">
                    <div class="landing-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <h3>Courseware & Syllabus</h3>
                    <p>Access structured course modules, syllabus requirements, reading materials, and curriculum outlines with clarity.</p>
                </div>

                <div class="landing-feature">
                    <div class="landing-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <h3>Deliverables & Submissions</h3>
                    <p>Submit coursework deliverables, research projects, and code solutions with deadline verification and instructor rubrics.</p>
                </div>

                <div class="landing-feature">
                    <div class="landing-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3>Timed Examination Engine</h3>
                    <p>Undergo structured, timed assessments with automated calculation, randomized questions, and analytical feedback.</p>
                </div>

                <div class="landing-feature">
                    <div class="landing-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3>Institutional Gradebook</h3>
                    <p>Institutional grade reporting with numeric and letter standings, GPA tracking, and official Dean's Honor Roll standing.</p>
                </div>

                <div class="landing-feature">
                    <div class="landing-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <h3>Attendance & Engagement</h3>
                    <p>Track academic attendance for seminars, laboratory sessions, and colloquiums with institutional presence records.</p>
                </div>

                <div class="landing-feature">
                    <div class="landing-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Governance & Approvals</h3>
                    <p>Administrative governance, faculty allocation, registration vetting, and centralized collegiate data management.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Collegiate Footer --}}
    <footer class="landing-footer">
        <div class="landing-footer-brand">VILLASIS STATE POLYTECHNIC UNIVERSITY</div>
        <div class="landing-footer-text">
            &copy; {{ date('Y') }} Villasis State Polytechnic University. Educational Demonstration & Coursework Project.
        </div>
    </footer>

</body>
</html>
