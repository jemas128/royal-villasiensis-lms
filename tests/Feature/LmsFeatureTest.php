<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LmsFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $student;
    protected Department $department;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->department = Department::create([
            'name' => 'College of Computer Studies',
            'code' => 'CCS',
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
            'is_current' => true,
        ]);

        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'name' => '1st Semester',
            'start_date' => '2024-08-01',
            'end_date' => '2024-12-20',
            'is_current' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Prof. Alan Turing',
            'email' => 'teacher@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'department_id' => $this->department->id,
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'Ada Lovelace',
            'email' => 'student@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'STU-1001',
            'department_id' => $this->department->id,
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'code' => 'CS101',
            'name' => 'Introduction to Computing',
            'credits' => 3,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
            'is_active' => true,
        ]);

        Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('VILLASIS STATE POLYTECHNIC UNIVERSITY');
    }

    public function test_login_screen_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Portal Authentication');
    }

    public function test_authenticated_admin_redirected_to_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertRedirect(route('admin.dashboard'));

        $dash = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $dash->assertStatus(200);
        $dash->assertSee('Executive Governance Portal');
    }

    public function test_authenticated_teacher_accesses_faculty_dashboard(): void
    {
        $response = $this->actingAs($this->teacher)->get('/dashboard');
        $response->assertRedirect(route('teacher.dashboard'));

        $dash = $this->actingAs($this->teacher)->get(route('teacher.dashboard'));
        $dash->assertStatus(200);
        $dash->assertSee('Faculty Instructional Portal');
    }

    public function test_authenticated_student_accesses_student_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get('/dashboard');
        $response->assertRedirect(route('student.dashboard'));

        $dash = $this->actingAs($this->student)->get(route('student.dashboard'));
        $dash->assertStatus(200);
        $dash->assertSee('Welcome back, Ada Lovelace');
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_student_can_view_enrolled_course(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.courses.show', $this->course));
        $response->assertStatus(200);
        $response->assertSee('CS101');
    }

    public function test_registration_requires_captcha_and_sends_verification_code(): void
    {
        // 1. Missing captcha fails
        $failResponse = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@rvu.edu',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $failResponse->assertSessionHasErrors('captcha');

        // 2. Valid captcha token initiates verification code flow
        $token = 'valid-test-token';
        $this->withSession(['captcha_verified_token' => $token]);

        $successResponse = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@rvu.edu',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'captcha_token' => $token,
        ]);

        $successResponse->assertRedirect(route('register.verify'));
        $this->assertNotNull(session('pending_registration'));
        $this->assertEquals('john.doe@rvu.edu', session('pending_registration.email'));
    }

    public function test_email_verification_code_confirms_and_creates_pending_student(): void
    {
        $this->withSession([
            'pending_registration' => [
                'name' => 'Jane Scholar',
                'email' => 'jane.scholar@rvu.edu',
                'password' => 'secretUserPass99!',
                'department_id' => $this->department->id,
                'id_number' => null,
                'phone' => null,
                'code' => '482910',
            ]
        ]);

        // 1. Wrong code fails
        $fail = $this->post('/register/verify', ['code' => '000000']);
        $fail->assertSessionHasErrors('code');

        // 2. Correct code creates user in database
        $success = $this->post('/register/verify', ['code' => '482910']);
        $success->assertRedirect(route('login'));

        $user = User::where('email', 'jane.scholar@rvu.edu')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
        $this->assertFalse((bool) $user->is_active);
        $this->assertNotNull($user->initial_password_encrypted);

        // Verify that initial password can be decrypted back to the user's input password
        $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($user->initial_password_encrypted);
        $this->assertEquals('secretUserPass99!', $decrypted);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $inactiveUser = User::create([
            'name' => 'Pending Scholar',
            'email' => 'pending@rvu.edu',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@rvu.edu',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_approve_pending_user(): void
    {
        $pendingUser = User::create([
            'name' => 'Applicant Scholar',
            'email' => 'applicant@rvu.edu',
            'password' => Hash::make('password123'),
            'initial_password_encrypted' => \Illuminate\Support\Facades\Crypt::encryptString('password123'),
            'role' => 'student',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.approve', $pendingUser));
        $response->assertSessionHas('success');

        $this->assertTrue((bool) $pendingUser->fresh()->is_active);
    }

    public function test_admin_can_promote_user_to_teacher_or_admin(): void
    {
        $scholar = User::create([
            'name' => 'Rising Scholar',
            'email' => 'rising@rvu.edu',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
        ]);

        // Promote to teacher
        $this->actingAs($this->admin)->post(route('admin.users.change-role', $scholar), [
            'role' => 'teacher',
        ]);
        $this->assertEquals('teacher', $scholar->fresh()->role);
        $this->assertNotNull($scholar->fresh()->employee_id);

        // Promote to admin
        $this->actingAs($this->admin)->post(route('admin.users.change-role', $scholar), [
            'role' => 'admin',
        ]);
        $this->assertEquals('admin', $scholar->fresh()->role);
    }

    public function test_user_can_update_password(): void
    {
        $response = $this->actingAs($this->student)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'newSecretPass123',
            'password_confirmation' => 'newSecretPass123',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newSecretPass123', $this->student->fresh()->password));
    }

    public function test_admin_can_update_user_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->student), [
            'name' => $this->student->name,
            'email' => $this->student->email,
            'role' => 'student',
            'password' => 'adminSetNewPass88!',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertTrue(Hash::check('adminSetNewPass88!', $this->student->fresh()->password));
    }
}
