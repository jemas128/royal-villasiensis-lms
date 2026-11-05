# Villasis State Polytechnic University LMS — Project Architecture & Codebase Explanation

This document serves as the comprehensive architectural and structural guide to the **Villasis State Polytechnic University (VSPU) Learning Management System**. It details every directory, file, controller, model, migration, view, and route, outlining their responsibilities, business logic, and interconnections within the application.

---

## Table of Contents
1. [System Overview & Tech Stack](#1-system-overview--tech-stack)
2. [High-Level Directory Map](#2-high-level-directory-map)
3. [Root Configuration Files](#3-root-configuration-files)
4. [Application Core (`app/`)](#4-application-core-app)
   - [HTTP Controllers (`app/Http/Controllers/`)](#http-controllers-apphttpcontrollers)
   - [HTTP Middleware (`app/Http/Middleware/`)](#http-middleware-apphttpmiddleware)
   - [Eloquent Models (`app/Models/`)](#eloquent-models-appmodels)
   - [Mailables (`app/Mail/`)](#mailables-appmail)
   - [Service Providers (`app/Providers/`)](#service-providers-appproviders)
5. [Application Bootstrapping (`bootstrap/`)](#5-application-bootstrapping-bootstrap)
6. [Configuration Subsystem (`config/`)](#6-configuration-subsystem-config)
7. [Database Schema, Migrations & Seeders (`database/`)](#7-database-schema-migrations--seeders-database)
8. [Frontend & Presentation Layer (`resources/`)](#8-frontend--presentation-layer-resources)
   - [Blade Templates & Layouts (`resources/views/`)](#blade-templates--layouts-resourcesviews)
   - [Assets: CSS & JavaScript (`resources/css/`, `resources/js/`)](#assets-css--javascript)
9. [HTTP Routing Engine (`routes/`)](#9-http-routing-engine-routes)
10. [Web Root & Storage (`public/` & `storage/`)](#10-web-root--storage-public--storage)
11. [Deployment Artifacts (`deployment/`)](#11-deployment-artifacts-deployment)

---

## 1. System Overview & Tech Stack

The system is a bespoke, role-based Learning Management System engineered for **Villasis State Polytechnic University**. It features three discrete operational roles:
* **Administrators**: Institutional authority to manage academic departments, semesters, courses, user accounts, and system-wide enrollments.
* **Faculty / Teachers**: Instructors who manage subject classrooms, upload lecture documents, assign deliverables, build interactive quizzes, monitor real-time class attendance, and submit official gradebook marks.
* **Scholars / Students**: Learners who request entry into courses via enrollment codes, study uploaded materials, submit project deliverables, take timed examinations, and review attendance/grade transcripts.

### Key Architectural Traits
* **Framework**: Laravel 11 (PHP 8.2+)
* **Database**: MySQL / MariaDB (support for SQLite in test/local environments)
* **Frontend Engine**: Laravel Blade, Vanilla Modern JavaScript, bespoke Academic Luxury CSS theme (styled with gold, obsidian, charcoal, and ivory hues).
* **Asset Bundler**: Vite
* **Deployment Target**: Apache/Nginx (tailored for shared hosting like ByetHost as well as standard VPS environments).

---

## 2. High-Level Directory Map

```text
royal-villasiensis-lms/
├── app/                  # Application core logic (Controllers, Models, Middleware, Mail)
├── bootstrap/            # Framework startup, exception handling, and middleware registration
├── config/               # Configuration settings (database, sessions, mail, auth, etc.)
├── database/             # Database migrations, seeders, factories, and SQLite storage
├── deployment/           # Production deployment configs and shared-hosting htaccess templates
├── public/               # Public entry point (index.php, compiled CSS/JS, robots.txt)
├── resources/            # Blade templates, raw CSS stylesheets, and client JS scripts
├── routes/               # Web and Console route definitions
├── storage/              # Uploaded course materials, student submissions, logs, and caches
├── tests/                # Automated feature and unit tests
├── .env                  # Environment variable configuration (DB, Mail, App Keys)
├── .htaccess             # Root URL rewrite and redirect protection for Apache
├── artisan               # Laravel command-line interface executable
├── composer.json         # PHP dependency declarations
└── package.json          # Node.js/Vite frontend dependency declarations
```

---

## 3. Root Configuration Files

* **`.env` / `.env.example`**: Defines environment-specific parameters such as database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`), mail server credentials (`MAIL_HOST`, `MAIL_USERNAME`), application URL (`APP_URL`), session driver, and app encryption key (`APP_KEY`).
* **`.htaccess`**: Directs web traffic entering the project root into the `/public` directory without exposing system files. Configured with loop-prevention rules (`RewriteCond %{REQUEST_URI} !^/public/`) ensuring stability on Apache and OpenResty servers.
* **`artisan`**: The CLI entrypoint for running database migrations (`php artisan migrate`), seeding records (`php artisan db:seed`), clearing caches, or generating components.
* **`composer.json` & `composer.lock`**: Manages all PHP dependencies, including Laravel Framework 11, Guzzle HTTP, and Carbon date utilities.
* **`package.json` & `package-lock.json`**: Specifies frontend build dependencies including `vite`, `laravel-vite-plugin`, and PostCSS.
* **`vite.config.js`**: Instructs Vite on compiling the core styling (`resources/css/app.css`) and JavaScript logic (`resources/js/app.js`).

---

## 4. Application Core (`app/`)

The `app/` folder contains all internal application logic.

### HTTP Controllers (`app/Http/Controllers/`)

Controllers handle incoming HTTP requests, coordinate with Eloquent Models, apply business rules, and render Blade views or redirect.

#### Base & Shared Controllers
* **`Controller.php`**: The abstract base controller from which all application controllers inherit.
* **`DashboardController.php`**: Intelligent central traffic router. When an authenticated user accesses `/dashboard`, this controller checks their role (`isAdmin()`, `isTeacher()`, or `isStudent()`) and immediately reroutes them to their respective portal (`admin.dashboard`, `teacher.dashboard`, or `student.dashboard`).
* **`ProfileController.php`**: Enables all authenticated users to view their account information, edit contact numbers/addresses, and securely update their account passwords.
* **`AnnouncementController.php`**: Controls the university broadcast board. All users can read announcements; faculty and administrators can author or delete institutional notices.
* **`MaterialDownloadController.php`**: Dedicated secure download engine. Solves missing symlink issues on shared hosting by directly streaming physical lecture files and student submission attachments via PHP response buffers. It also validates student enrollment before granting file access and seamlessly redirects external resource links.

#### Authentication Controllers (`app/Http/Controllers/Auth/`)
* **`LoginController.php`**: Manages user authentication via Portal ID (Student ID, Employee ID, or institutional email). Enforces account activation approval, safely purges mismatched cross-role `url.intended` session parameters to eliminate unintended 403 errors, and invalidates sessions upon sign-out.
* **`RegisterController.php`**: Manages public student account registration with bot-protection checkboxes, sends email verification codes, generates formatted IDs (e.g. `STU-2026-0001`), and places new accounts in pending status awaiting administrative approval.
* **`ForgotPasswordController.php`**: Handles password reset request dispatches via email links.
* **`ResetPasswordController.php`**: Validates reset tokens and sets new passwords for accounts.

#### Administrator Controllers (`app/Http/Controllers/Admin/`)
* **`AdminDashboardController.php`**: Aggregates institutional analytics: active student count, faculty headcount, total courses, department distribution, pending approvals, and system activity logs.
* **`UserController.php`**: Full CRUD interface for all user accounts. Provides actions to approve pending registrations, toggle account active status, assign or alter user roles, and resend generated credentials.
* **`CourseController.php`**: Handles creation, updating, and deactivation of university courses/subjects, assigning faculty instructors, credit unit definitions, and syllabus parameters.
* **`DepartmentController.php`**: Manages academic colleges/departments (e.g., College of Computer Studies, College of Engineering, College of Business).
* **`SemesterController.php`**: Defines academic years and operational semesters (1st Semester, 2nd Semester, Summer Term).
* **`EnrollmentController.php`**: University-wide administrative oversight of student enrollments across all courses.

#### Teacher Controllers (`app/Http/Controllers/Teacher/`)
* **`TeacherDashboardController.php`**: Summarizes the instructor's active classes, enrolled scholars count, pending joining requests, recent assignment submissions awaiting grading, and quick shortcuts.
* **`CourseController.php`**: Teacher's classroom management portal. Displays roster members, generates and regenerates 6-character classroom join codes (e.g., `MATH10-AB12`), approves or rejects student join requests, and removes dropped scholars.
* **`MaterialController.php`**: Handles uploading syllabus documents, PDF lectures, PowerPoint presentations, or external links (Google Drive, YouTube). Automatically cleans up stored physical files when a resource is deleted.
* **`AssignmentController.php`**: Enables instructors to post course deliverables with deadlines and maximum scores, inspect student submissions, view submitted attachments, assign grades, and provide feedback commentary.
* **`QuizController.php`**: Examination designer. Enables instructors to create quizzes with custom duration limits, max attempts, and date windows, and add 2 to 4 option multiple-choice / True-False questions.
* **`GradeController.php`**: The official course gradebook. Allows batch input and updates of midterm, final, numeric, and letter grades alongside academic remarks.
* **`AttendanceController.php`**: Class attendance registry. Provides date-based roster roll-call where instructors mark students as Present, Late, Absent, or Excused with custom remarks.

#### Student Controllers (`app/Http/Controllers/Student/`)
* **`StudentDashboardController.php`**: Overview of the scholar's academic profile: enrolled subjects, active assignments due, upcoming examination alerts, recent grade postings, and attendance rate.
* **`CourseController.php`**: Scholar's classroom experience. Lets students view their active classes, submit enrollment codes to request entry into new classes, cancel pending requests, and browse learning materials, assignments, and quizzes.
* **`AssignmentController.php`**: Displays active deliverables, assignment instructions, and due dates; handles file upload submissions and text commentaries.
* **`QuizController.php`**: Secure examination interface. Enforces course enrollment and schedule windows, computes live server-side countdown timers, supports resuming in-progress attempts, records answer selections, and calculates instant performance scores.
* **`GradeController.php`**: Scholar's academic transcript view. Displays numeric grades, letter grades, remarks, and GPA computation across courses.
* **`AttendanceController.php`**: Displays the scholar's attendance record, summary breakdown (Present, Absent, Late), and overall attendance percentage rate.

---

### HTTP Middleware (`app/Http/Middleware/`)

* **`RoleMiddleware.php`**: Intercepts every role-protected request (`role:admin`, `role:teacher,admin`, `role:student,admin`). If a guest attempts access, it redirects to the login screen. If an authenticated user attempts to access a section outside their role (e.g., a teacher opening `/admin/dashboard`), instead of throwing an unhelpful 403 error page, it automatically and seamlessly redirects them to their own role's dashboard with a clear notification banner.

---

### Eloquent Models (`app/Models/`)

Eloquent models define database tables, relationships, attributes, and helpers.

* **`User.php`**: The primary user identity model. Handles role identification (`isAdmin()`, `isTeacher()`, `isStudent()`), portal ID resolution (`student_id`, `employee_id`, `email`), encrypted initial passwords, and relationships to enrollments, taught courses, and departments.
* **`Department.php`**: Represents university academic departments. Has many courses and faculty members.
* **`AcademicYear.php`**: Represents school years (e.g., 2026–2027). Has many semesters.
* **`Semester.php`**: Academic terms (1st, 2nd, Summer). Connects courses to time periods.
* **`Course.php`**: Represents a subject/class section. Tracks `code`, `name`, `teacher_id`, `department_id`, `semester_id`, and `enrollment_code`. Has many materials, assignments, quizzes, and enrollments.
* **`Enrollment.php`**: Pivot entity connecting students to courses. Defines statuses: `pending`, `enrolled`, `rejected`, `dropped`.
* **`CourseMaterial.php`**: Documents, PDFs, presentations, or external links uploaded by instructors for student reference.
* **`Assignment.php`**: Course deliverables with title, description, due date, maximum score, and publication toggle. Has many submissions.
* **`Submission.php`**: A student's deliverable response. Stores submission text, uploaded file path, submission timestamp, assigned score, and instructor feedback.
* **`Quiz.php`**: Represents an online assessment. Tracks duration limit in minutes, maximum allowed attempts, scheduling dates, and publication status. Has many questions and attempts.
* **`QuizQuestion.php`**: Individual multiple-choice or True-False assessment item. Stores question prompt, JSON options array, index of correct answer, and points value.
* **`QuizAttempt.php`**: Records a student's attempt at a quiz. Stores JSON submitted answers, score, total points, started timestamp, completed timestamp, and calculated percentage accessor (`percentage`).
* **`Grade.php`**: Final or periodic evaluation scores. Stores numeric grade, letter grade, and instructor remarks for a student in a course.
* **`Attendance.php`**: Daily or session attendance record for a student in a course (`present`, `absent`, `late`, `excused`).
* **`Announcement.php`**: Broadcast notices posted by faculty or administrators.

---

### Mailables (`app/Mail/`)

* **`AccountCreatedMail.php`**: Sends newly generated student or faculty credentials (Portal ID & Temporary Password).
* **`AccountApprovedMail.php`**: Alerts the applicant that their registered portal account has been reviewed and verified by the administration.
* **`EmailVerificationCodeMail.php`**: Transmits 6-digit verification security pins during self-registration.
* **`RegistrationReceivedMail.php`**: Informs registering students that their application is in the queue for administrative verification.
* **`UserRoleUpdatedMail.php`**: Informs a user when their system permissions or institutional roles have been modified.

---

### Service Providers (`app/Providers/`)

* **`AppServiceProvider.php`**: Configures global framework behaviors, sets default pagination styles, configures HTTPS enforcement if in production, and registers global Blade directives.

---

## 5. Application Bootstrapping (`bootstrap/`)

* **`bootstrap/app.php`**: The foundational initialization file in Laravel 11.
  - Registers application routes (`web.php`, `console.php`).
  - Registers route middleware aliases (`'role' => RoleMiddleware::class`).
  - **CSRF Token Exemption**: Configures `$middleware->validateCsrfTokens(except: ['logout'])`, preventing `419 Page Expired` errors when signing out from stale sessions.
  - **Global Exception Handling**: Intercepts 403 (Forbidden) and 419 (Token Mismatch) exceptions globally. Seamlessly redirects authenticated users encountering access restrictions to their personal dashboards and expired sessions to the login page with helpful alerts.
* **`bootstrap/cache/`**: Contains runtime compiled cache files (`packages.php`, `services.php`) to optimize application execution speed.

---

## 6. Configuration Subsystem (`config/`)

Contains application configuration profiles:
* **`config/app.php`**: Application name, locale, timezone, and encryption cipher.
* **`config/auth.php`**: Authentication guards and password reset broker configurations.
* **`config/database.php`**: Database connections (MySQL default, SQLite, PostgreSQL).
* **`config/filesystems.php`**: Defines local, public, and cloud file storage disks.
* **`config/mail.php`**: SMTP server credentials, port definitions, and outgoing sender identity.
* **`config/session.php`**: Session driver (`file`), lifetime (120 minutes), and cookie settings.

---

## 7. Database Schema, Migrations & Seeders (`database/`)

### Migrations (`database/migrations/`)
Migrations define the database tables and columns in code:
1. `0001_01_01_000000_create_users_table.php`: Core users table with portal identity columns (`student_id`, `employee_id`, `role`, `is_active`).
2. `0001_01_01_000001_create_cache_table.php`: Framework cache storage table.
3. `0001_01_01_000002_create_jobs_table.php`: Asynchronous background queue jobs table.
4. `2024_01_01_000001_create_departments_table.php`: Academic departments table.
5. `2024_01_01_000002_create_academic_years_table.php`: School years table.
6. `2024_01_01_000003_create_semesters_table.php`: Terms and semesters table.
7. `2024_01_01_000005_create_courses_table.php`: Courses and classes table.
8. `2024_01_01_000006_create_enrollments_table.php`: Student-course enrollment relationships.
9. `2024_01_01_000007_create_assignments_table.php`: Class deliverables and projects.
10. `2024_01_01_000008_create_submissions_table.php`: Student deliverable submissions.
11. `2024_01_01_000009_create_quizzes_table.php`: Examinations and online quizzes.
12. `2024_01_01_000010_create_quiz_questions_table.php`: Exam items and option answers.
13. `2024_01_01_000011_create_quiz_attempts_table.php`: Student assessment scores and answers.
14. `2024_01_01_000012_create_grades_table.php`: Official grade records.
15. `2024_01_01_000013_create_attendances_table.php`: Class attendance tracking.
16. `2024_01_01_000014_create_announcements_table.php`: Broadcast bulletins table.
17. `2024_01_01_000015_create_course_materials_table.php`: Uploaded lecture files and links.
18. `2026_10_08_151535_add_initial_password_encrypted_to_users_table.php`: Stores encrypted credentials for first-time login issuance.
19. `2026_10_09_000001_add_enrollment_code_and_update_enrollment_status.php`: Adds classroom join code support.

### Seeders & Factories (`database/seeders/`)
* **`DatabaseSeeder.php`**: Populates the database with initial institutional structures: default Admin accounts, sample Faculty and Student accounts, academic departments, semesters, sample courses, and announcements.
* **`UserFactory.php`**: Generates mock user records for testing.

---

## 8. Frontend & Presentation Layer (`resources/`)

### Blade Templates & Layouts (`resources/views/`)

* **`welcome.blade.php`**: Public university portal landing page displaying institutional heritage, core programs, academic statistics, and access portals.

#### Layouts & Partials (`layouts/`, `partials/`)
* **`layouts/app.blade.php`**: The master structural template for all authenticated portals. Renders header navigation, top bar, breadcrumb trails, flash status alerts (success, error, warning), user dropdowns, and responsive sidebar wrappers.
* **`partials/sidebar.blade.php`**: Role-adaptive sidebar menu. Automatically presents Admin tools to administrators, classroom management tools to teachers, and study tools to students.

#### Authentication Views (`views/auth/`)
* **`login.blade.php`**: Institutional portal sign-in interface (supports Portal ID, Employee ID, or Email).
* **`register.blade.php`**: Student portal registration form with department selection and captcha validation.
* **`verify.blade.php`**: Verification page for entering email pins.
* **`forgot-password.blade.php` & `reset-password.blade.php`**: Password reset request and confirmation forms.

#### Dashboard Views (`views/dashboard/`)
* **`admin.blade.php`**: System administration control room with institutional metric cards, quick action tiles, and audit summaries.
* **`teacher.blade.php`**: Faculty command center displaying active classes, pending submissions, and roster metrics.
* **`student.blade.php`**: Scholar hub displaying enrolled classes, pending tasks, recent grades, and attendance metrics.

#### Role-Specific Views
* **`views/admin/`**: Views for managing departments, courses, semesters, users, and enrollments.
* **`views/teacher/`**:
  * `courses/`: Roster review, joining code generator, student approval tables.
  * `materials/`: Lecture upload modal and links manager.
  * `assignments/`: Deliverable creation, submission lists, and grading dialogs.
  * `quizzes/`: Quiz builder, attempt trackers, and multiple-choice question authoring forms.
  * `grades/`: Class gradebook spreadsheet interface.
  * `attendance/`: Daily attendance roll-call interface.
* **`views/student/`**:
  * `courses/`: Class overview, course joining modal, syllabus viewer.
  * `assignments/`: Deliverables list, submission form, and instructor feedback viewer.
  * `quizzes/`: Assessment catalog, timed exam interface with live countdown timer, and detailed item review screen.
  * `grades/`: GPA report card and academic transcript view.
  * `attendance/`: Scholar's personal attendance history and compliance statistics.

#### System Views (`views/announcements/`, `views/profile/`, `views/errors/`)
* **`announcements/index.blade.php`**: University notice board feed.
* **`profile/show.blade.php`**: Account information editor and password changer.
* **`errors/403.blade.php`**: Access restricted fallback with automatic redirection back to the user's dashboard.
* **`errors/419.blade.php`**: Session timeout fallback with immediate redirection to the sign-in screen.
* **`errors/404.blade.php` & `errors/500.blade.php`**: Styled error pages consistent with the university theme.

---

## 9. HTTP Routing Engine (`routes/`)

* **`routes/web.php`**: The primary HTTP route map:
  - **Public Routes**: Welcome landing page (`/`).
  - **Guest Routes**: `/login`, `/register`, `/forgot-password`, `/reset-password`.
  - **Resilient Logout Route**: `Route::match(['get', 'post'], '/logout', ...)` ensures users can sign out cleanly without 419 or 405 errors.
  - **Authenticated Group (`middleware('auth')`)**:
    - Central router: `/dashboard` $\rightarrow$ `DashboardController@index`.
    - Profile: `/profile`.
    - Bulletins: `/announcements`.
    - File Downloads: `/materials/{material}/download` and `/submissions/{submission}/download`.
    - **Admin Routes (`middleware('role:admin')`, prefix `admin`)**: CRUD for departments, courses, semesters, users, and enrollments.
    - **Teacher Routes (`middleware('role:teacher,admin')`, prefix `teacher`)**: Classrooms, materials, deliverables, quiz authoring, gradebook, attendance.
    - **Student Routes (`middleware('role:student,admin')`, prefix `student`)**: Class enrollment, material downloads, assignments, timed quiz attempts, attendance records, grades.
* **`routes/console.php`**: Artisan closure-based terminal commands.

---

## 10. Web Root & Storage (`public/` & `storage/`)

* **`public/`**: The publicly accessible web root:
  - `index.php`: The HTTP front-controller that bootstraps Laravel and handles incoming HTTP requests.
  - `.htaccess`: Configures Apache web server rewrite rules, header optimizations, and security restrictions.
  - `build/`: Holds production-compiled CSS, JavaScript, and font assets generated by Vite.
* **`storage/`**:
  - `storage/app/public/materials/`: Physical storage location for lecture notes, slides, and syllabus documents uploaded by instructors.
  - `storage/app/public/submissions/`: Uploaded student project deliverables and attachments.
  - `storage/framework/`: Framework internal cache, session files, and compiled Blade templates.
  - `storage/logs/laravel.log`: Application runtime exception and event logs.

---

## 11. Deployment Artifacts (`deployment/`)

Contains deployment configurations for deploying the application to shared hosting providers (such as ByetHost / cPanel) or production servers:
* **`htaccess-single-folder.txt`**: Apache configuration template for single-root setups.
* **`index-split-folder.php`**: Bridge script for setups where the `public/` directory is separated from the core framework.
* **`.env.production.example`**: Production environment configuration template with preconfigured cache and security flags.
* **`README.md`**: Step-by-step instructions for deploying to shared hosting platforms via FTP/FileZilla.

---

*Document generated for the Villasis State Polytechnic University Learning Management System codebase.*

