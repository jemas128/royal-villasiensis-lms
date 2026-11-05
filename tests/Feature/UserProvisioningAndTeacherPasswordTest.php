<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProvisioningAndTeacherPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->department = Department::create([
            'name' => 'College of Arts & Sciences',
            'code' => 'CAS',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Chief Administrator',
            'email' => 'chief.admin@rvu.edu',
            'password' => Hash::make('adminPassword123!'),
            'role' => 'admin',
            'employee_id' => 'ADM-2024-001',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_provision_teacher_with_auto_generated_id_and_password(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Dr. James Moriarty',
            'email' => 'moriarty@rvu.edu',
            'role' => 'teacher',
            'department_id' => $this->department->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $teacher = User::where('email', 'moriarty@rvu.edu')->first();
        $this->assertNotNull($teacher);
        $this->assertNull($teacher->student_id);
        $this->assertNotNull($teacher->employee_id);
        $this->assertStringStartsWith('FAC-' . date('Y') . '-', $teacher->employee_id);

        // Verify initial password was generated and encrypted
        $this->assertNotNull($teacher->initial_password_encrypted);
        $plainPassword = Crypt::decryptString($teacher->initial_password_encrypted);
        $this->assertNotEmpty($plainPassword);
        $this->assertTrue(Hash::check($plainPassword, $teacher->password));
    }

    public function test_admin_can_provision_student_with_auto_generated_id_and_password(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'John Watson',
            'email' => 'watson@rvu.edu',
            'role' => 'student',
            'department_id' => $this->department->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $student = User::where('email', 'watson@rvu.edu')->first();
        $this->assertNotNull($student);
        $this->assertNull($student->employee_id);
        $this->assertNotNull($student->student_id);
        $this->assertStringStartsWith('STU-' . date('Y') . '-', $student->student_id);

        $plainPassword = Crypt::decryptString($student->initial_password_encrypted);
        $this->assertNotEmpty($plainPassword);
        $this->assertTrue(Hash::check($plainPassword, $student->password));
    }

    public function test_teacher_can_login_with_employee_id_or_email(): void
    {
        // 1. Provision teacher
        $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Professor Higgins',
            'email' => 'higgins@rvu.edu',
            'role' => 'teacher',
            'department_id' => $this->department->id,
            'is_active' => true,
        ]);

        $teacher = User::where('email', 'higgins@rvu.edu')->first();
        $plainPassword = Crypt::decryptString($teacher->initial_password_encrypted);

        // Logout admin
        auth()->logout();

        // 2. Login using Employee ID
        $loginWithIdResponse = $this->post(route('login'), [
            'portal_id' => $teacher->employee_id,
            'password' => $plainPassword,
        ]);

        $loginWithIdResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teacher);

        // Logout teacher
        auth()->logout();

        // 3. Login using Email address
        $loginWithEmailResponse = $this->post(route('login'), [
            'portal_id' => $teacher->email,
            'password' => $plainPassword,
        ]);

        $loginWithEmailResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teacher);
    }

    public function test_teacher_can_update_their_password(): void
    {
        $oldPassword = 'OldSecretPass123!';
        $newPassword = 'NewSecretPass999@';

        $teacher = User::create([
            'name' => 'Prof. Charles Xavier',
            'email' => 'xavier@rvu.edu',
            'password' => Hash::make($oldPassword),
            'role' => 'teacher',
            'employee_id' => 'FAC-2024-0099',
            'is_active' => true,
        ]);

        // Teacher updates password under profile
        $response = $this->actingAs($teacher)->put(route('profile.password'), [
            'current_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSessionHas('success');

        $teacher->refresh();
        $this->assertTrue(Hash::check($newPassword, $teacher->password));
        $this->assertFalse(Hash::check($oldPassword, $teacher->password));

        // Teacher can now log in with the new password
        auth()->logout();

        $loginResponse = $this->post(route('login'), [
            'portal_id' => $teacher->employee_id,
            'password' => $newPassword,
        ]);

        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teacher);
    }
}
