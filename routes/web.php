<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\SemesterController as AdminSemesterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\MaterialController as TeacherMaterialController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Villasis State Polytechnic University LMS
|--------------------------------------------------------------------------
*/

// Public Landing Page (with fail-safe database fallback)
Route::get('/', function () {
    try {
        $departmentsCount = \App\Models\Department::count();
        $coursesCount = \App\Models\Course::count();
        $studentsCount = \App\Models\User::where('role', 'student')->count();
        $facultyCount = \App\Models\User::where('role', 'teacher')->count();
    } catch (\Throwable $e) {
        $departmentsCount = 0;
        $coursesCount = 0;
        $studentsCount = 0;
        $facultyCount = 0;
    }

    return view('welcome', compact('departmentsCount', 'coursesCount', 'studentsCount', 'facultyCount'));
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/captcha/verify-checkbox', [RegisterController::class, 'verifyCheckboxCaptcha'])->name('captcha.verify');
    Route::get('/register/verify', [RegisterController::class, 'showVerifyForm'])->name('register.verify');
    Route::post('/register/verify', [RegisterController::class, 'confirmVerification']);
    Route::post('/register/resend', [RegisterController::class, 'resendCode'])->name('register.resend');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Logout Route (supports GET & POST, handles expired sessions smoothly)
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Role-based Router
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Announcements (All Roles can view, faculty & admin can post)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Secure Downloads (Materials & Submissions)
    Route::get('/materials/{material}/download', [\App\Http\Controllers\MaterialDownloadController::class, 'download'])->name('materials.download');
    Route::get('/submissions/{submission}/download', [\App\Http\Controllers\MaterialDownloadController::class, 'downloadSubmission'])->name('submissions.download');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Departments CRUD
        Route::resource('departments', AdminDepartmentController::class);

        // Courses CRUD
        Route::resource('courses', AdminCourseController::class);

        // Semesters CRUD
        Route::resource('semesters', AdminSemesterController::class)->except(['create', 'show', 'edit']);

        // Users CRUD & Approvals
        Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/change-role', [AdminUserController::class, 'changeRole'])->name('users.change-role');
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/resend-credentials', [AdminUserController::class, 'resendCredentials'])->name('users.resend-credentials');
        Route::resource('users', AdminUserController::class);

        // Enrollments
        Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments', [AdminEnrollmentController::class, 'store'])->name('enrollments.store');
        Route::delete('/enrollments/{enrollment}', [AdminEnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Teacher Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:teacher,admin')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

        // Courses
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [TeacherCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{course}/regenerate-code', [TeacherCourseController::class, 'regenerateCode'])->name('courses.regenerate-code');
        Route::post('/courses/{course}/enrollments/{enrollment}/approve', [TeacherCourseController::class, 'approveEnrollment'])->name('courses.enrollments.approve');
        Route::post('/courses/{course}/enrollments/{enrollment}/reject', [TeacherCourseController::class, 'rejectEnrollment'])->name('courses.enrollments.reject');
        Route::delete('/courses/{course}/enrollments/{enrollment}', [TeacherCourseController::class, 'removeStudent'])->name('courses.enrollments.remove');

        // Assignments & Submissions
        Route::resource('assignments', TeacherAssignmentController::class);
        Route::post('/submissions/{submission}/grade', [TeacherAssignmentController::class, 'gradeSubmission'])->name('assignments.submissions.grade');

        // Quizzes & Questions
        Route::resource('quizzes', TeacherQuizController::class);
        Route::post('/quizzes/{quiz}/questions', [TeacherQuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::delete('/questions/{question}', [TeacherQuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');

        // Gradebook
        Route::get('/grades', [TeacherGradeController::class, 'index'])->name('grades.index');
        Route::post('/grades', [TeacherGradeController::class, 'store'])->name('grades.store');

        // Attendance
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');

        // Materials
        Route::post('/materials', [TeacherMaterialController::class, 'store'])->name('materials.store');
        Route::delete('/materials/{material}', [TeacherMaterialController::class, 'destroy'])->name('materials.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:student,admin')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Courses
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::post('/courses/join', [StudentCourseController::class, 'joinWithCode'])->name('courses.join');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::post('/courses/{course}/drop', [StudentCourseController::class, 'drop'])->name('courses.drop');
        Route::delete('/enrollments/{enrollment}/cancel', [StudentCourseController::class, 'cancelRequest'])->name('courses.cancel-request');

        // Assignments
        Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');

        // Quizzes
        Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        Route::get('/quizzes/{quiz}/take', [StudentQuizController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quiz-attempts/{attempt}', [StudentQuizController::class, 'result'])->name('quizzes.result');

        // Records
        Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades');
        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');
    });
});
