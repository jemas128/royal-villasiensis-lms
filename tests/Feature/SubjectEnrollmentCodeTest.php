<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SubjectEnrollmentCodeTest extends TestCase
{
    use RefreshDatabase;

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
            'code' => 'CS201',
            'name' => 'Data Structures & Algorithms',
            'credits' => 3,
            'department_id' => $this->department->id,
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
            'schedule' => 'Mon / Wed 09:00 - 10:30 AM',
            'room' => 'Hall 402 - Turing Lab',
            'is_active' => true,
        ]);
    }

    public function test_course_auto_generates_enrollment_code(): void
    {
        $this->assertNotEmpty($this->course->enrollment_code);
        $this->assertStringStartsWith('CS201-', $this->course->enrollment_code);
    }

    public function test_student_can_request_entry_with_valid_code(): void
    {
        $response = $this->actingAs($this->student)->post(route('student.courses.join'), [
            'code' => strtolower($this->course->enrollment_code), // Case-insensitive test
        ]);

        $response->assertRedirect(route('student.courses.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'pending',
        ]);
    }

    public function test_student_cannot_join_with_invalid_code(): void
    {
        $response = $this->actingAs($this->student)->post(route('student.courses.join'), [
            'code' => 'INVALID-999',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $this->student->id,
        ]);
    }

    public function test_student_cannot_access_course_while_pending(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.courses.show', $this->course));
        $response->assertRedirect(route('student.courses.index'));
        $response->assertSessionHas('info');
    }

    public function test_teacher_can_see_pending_applicant_and_approve(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'pending',
        ]);

        // Teacher views course page and sees pending applicant
        $pageResponse = $this->actingAs($this->teacher)->get(route('teacher.courses.show', $this->course));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee($this->student->name);
        $pageResponse->assertSee($this->course->enrollment_code);

        // Teacher approves enrollment
        $approveResponse = $this->actingAs($this->teacher)->post(
            route('teacher.courses.enrollments.approve', ['course' => $this->course, 'enrollment' => $enrollment])
        );

        $approveResponse->assertSessionHas('success');
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_approved_student_can_access_course_and_view_rich_ui(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'enrolled',
        ]);

        $response = $this->actingAs($this->student)->get(route('student.courses.show', $this->course));
        $response->assertStatus(200);
        $response->assertSee('CS201');
        $response->assertSee('Data Structures & Algorithms');
        $response->assertSee('Prof. Alan Turing');
        $response->assertSee('Overview & Syllabus', false);
        $response->assertSee('Assignments');
        $response->assertSee('Assessments & Quizzes', false);
        $response->assertSee('Learning Materials');
        $response->assertSee('Hall 402 - Turing Lab');
    }

    public function test_teacher_can_reject_pending_applicant(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'pending',
        ]);

        $rejectResponse = $this->actingAs($this->teacher)->post(
            route('teacher.courses.enrollments.reject', ['course' => $this->course, 'enrollment' => $enrollment])
        );

        $rejectResponse->assertSessionHas('info');
        $this->assertDatabaseMissing('enrollments', [
            'id' => $enrollment->id,
        ]);
    }

    public function test_student_can_cancel_pending_request(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'pending',
        ]);

        $cancelResponse = $this->actingAs($this->student)->delete(
            route('student.courses.cancel-request', $enrollment)
        );

        $cancelResponse->assertRedirect(route('student.courses.index'));
        $cancelResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('enrollments', [
            'id' => $enrollment->id,
        ]);
    }

    public function test_teacher_can_regenerate_enrollment_code(): void
    {
        $oldCode = $this->course->enrollment_code;

        $response = $this->actingAs($this->teacher)->post(
            route('teacher.courses.regenerate-code', $this->course)
        );

        $response->assertSessionHas('success');
        $this->course->refresh();
        $this->assertNotEquals($oldCode, $this->course->enrollment_code);
        $this->assertStringStartsWith('CS201-', $this->course->enrollment_code);
    }

    public function test_direct_self_enroll_is_blocked(): void
    {
        $response = $this->actingAs($this->student)->post(
            route('student.courses.enroll', $this->course)
        );

        $response->assertRedirect(route('student.courses.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }
}
