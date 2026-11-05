<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Semester;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departments
        $ccs = Department::create([
            'name' => 'College of Computer Studies',
            'code' => 'CCS',
            'description' => 'Programs in Computer Science, Information Technology, and Software Engineering.',
            'is_active' => true,
        ]);

        $cba = Department::create([
            'name' => 'College of Business & Accountancy',
            'code' => 'CBA',
            'description' => 'Programs in Business Administration, Accountancy, and Management.',
            'is_active' => true,
        ]);

        $cas = Department::create([
            'name' => 'College of Arts & Sciences',
            'code' => 'CAS',
            'description' => 'General education, natural sciences, humanities, and communication arts.',
            'is_active' => true,
        ]);

        $coe = Department::create([
            'name' => 'College of Engineering',
            'code' => 'COE',
            'description' => 'Civil, Electrical, and Computer Engineering disciplines.',
            'is_active' => true,
        ]);

        // 2. Academic Year & Semesters
        $academicYear = AcademicYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
            'is_current' => true,
        ]);

        $sem1 = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => '1st Semester',
            'start_date' => '2024-08-01',
            'end_date' => '2024-12-20',
            'is_current' => true,
        ]);

        $sem2 = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => '2nd Semester',
            'start_date' => '2025-01-10',
            'end_date' => '2025-05-31',
            'is_current' => false,
        ]);

        // 3. Users
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'employee_id' => 'EMP-001',
            'phone' => '+1 (555) 019-2831',
            'is_active' => true,
        ]);

        $teacher1 = User::create([
            'name' => 'Dr. Elizabeth Bennett',
            'email' => 'teacher@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'employee_id' => 'FAC-101',
            'department_id' => $ccs->id,
            'phone' => '+1 (555) 014-9822',
            'is_active' => true,
        ]);

        $teacher2 = User::create([
            'name' => 'Prof. Marcus Aurelius',
            'email' => 'marcus@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'employee_id' => 'FAC-102',
            'department_id' => $cas->id,
            'phone' => '+1 (555) 018-4729',
            'is_active' => true,
        ]);

        $student1 = User::create([
            'name' => 'Alexander Hayes',
            'email' => 'student@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'STU-2024-001',
            'department_id' => $ccs->id,
            'phone' => '+1 (555) 012-3456',
            'is_active' => true,
        ]);

        $student2 = User::create([
            'name' => 'Sophia Vance',
            'email' => 'sophia@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'STU-2024-002',
            'department_id' => $ccs->id,
            'phone' => '+1 (555) 012-7890',
            'is_active' => true,
        ]);

        $student3 = User::create([
            'name' => 'Julian Croft',
            'email' => 'julian@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'STU-2024-003',
            'department_id' => $cba->id,
            'phone' => '+1 (555) 016-5432',
            'is_active' => true,
        ]);

        // Sample newly registered student pending administrative verification
        User::create([
            'name' => 'Clara Oswald',
            'email' => 'clara.oswald@rvu.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'STU-2024-004',
            'department_id' => $ccs->id,
            'phone' => '+1 (555) 019-9944',
            'is_active' => false,
        ]);

        // 4. Courses
        $course1 = Course::create([
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
            'description' => 'Fundamental principles of computation, algorithmic problem-solving, and programming constructs.',
            'credits' => 3,
            'department_id' => $ccs->id,
            'teacher_id' => $teacher1->id,
            'semester_id' => $sem1->id,
            'max_students' => 40,
            'schedule' => 'Mon / Wed 09:00 - 10:30 AM',
            'room' => 'Hall 402 - Turing Lab',
            'is_active' => true,
        ]);

        $course2 = Course::create([
            'code' => 'CS201',
            'name' => 'Data Structures & Algorithms',
            'description' => 'Abstract data types, complexity analysis, trees, graphs, and sorting and search strategies.',
            'credits' => 3,
            'department_id' => $ccs->id,
            'teacher_id' => $teacher1->id,
            'semester_id' => $sem1->id,
            'max_students' => 35,
            'schedule' => 'Tue / Thu 10:30 - 12:00 PM',
            'room' => 'Hall 405 - Lovelace Lab',
            'is_active' => true,
        ]);

        $course3 = Course::create([
            'code' => 'BUS101',
            'name' => 'Principles of Modern Management',
            'description' => 'Organizational leadership, strategic decision analysis, and ethical management theories.',
            'credits' => 3,
            'department_id' => $cba->id,
            'teacher_id' => $teacher2->id,
            'semester_id' => $sem1->id,
            'max_students' => 50,
            'schedule' => 'Mon / Wed 01:00 - 02:30 PM',
            'room' => 'Executive Hall B',
            'is_active' => true,
        ]);

        $course4 = Course::create([
            'code' => 'ENG105',
            'name' => 'Technical Communications & Research',
            'description' => 'Scholarly discourse, technical reporting, and methodology communication in modern sciences.',
            'credits' => 3,
            'department_id' => $cas->id,
            'teacher_id' => $teacher2->id,
            'semester_id' => $sem1->id,
            'max_students' => 30,
            'schedule' => 'Fri 08:30 - 11:30 AM',
            'room' => 'Seminar Room 201',
            'is_active' => true,
        ]);

        // 5. Enrollments
        Enrollment::create(['student_id' => $student1->id, 'course_id' => $course1->id, 'status' => 'enrolled']);
        Enrollment::create(['student_id' => $student1->id, 'course_id' => $course2->id, 'status' => 'enrolled']);
        Enrollment::create(['student_id' => $student1->id, 'course_id' => $course3->id, 'status' => 'enrolled']);

        Enrollment::create(['student_id' => $student2->id, 'course_id' => $course1->id, 'status' => 'enrolled']);
        Enrollment::create(['student_id' => $student2->id, 'course_id' => $course4->id, 'status' => 'enrolled']);

        Enrollment::create(['student_id' => $student3->id, 'course_id' => $course2->id, 'status' => 'enrolled']);
        Enrollment::create(['student_id' => $student3->id, 'course_id' => $course3->id, 'status' => 'enrolled']);
        Enrollment::create(['student_id' => $student3->id, 'course_id' => $course4->id, 'status' => 'enrolled']);

        // 6. Assignments
        $assign1 = Assignment::create([
            'course_id' => $course1->id,
            'title' => 'Laboratory 1: Algorithmic Complexity Analysis',
            'description' => 'Implement Big-O benchmark simulations in your selected language and submit your analytical report with plots.',
            'due_date' => Carbon::now()->addDays(7),
            'max_score' => 100,
            'is_published' => true,
        ]);

        $assign2 = Assignment::create([
            'course_id' => $course1->id,
            'title' => 'Project Milestone: Logic Architecture Document',
            'description' => 'Submit complete UML architectural diagrams and component specifications for the term project.',
            'due_date' => Carbon::now()->addDays(14),
            'max_score' => 100,
            'is_published' => true,
        ]);

        $assign3 = Assignment::create([
            'course_id' => $course2->id,
            'title' => 'Implementation: Self-Balancing AVL Tree',
            'description' => 'Construct an AVL tree module with rebalancing operations, unit tests, and performance benchmarks.',
            'due_date' => Carbon::now()->addDays(5),
            'max_score' => 100,
            'is_published' => true,
        ]);

        // 7. Submissions
        Submission::create([
            'assignment_id' => $assign1->id,
            'student_id' => $student1->id,
            'content' => 'Completed all performance benchmarking runs comparing O(n log n) and O(n^2) algorithms. Please find details attached.',
            'score' => 95,
            'feedback' => 'Exceptional clarity and mathematical rigor in your empirical analysis.',
            'submitted_at' => Carbon::now()->subDays(1),
            'graded_at' => Carbon::now()->subHours(4),
        ]);

        Submission::create([
            'assignment_id' => $assign1->id,
            'student_id' => $student2->id,
            'content' => 'Completed benchmark analysis with Python profiling script.',
            'score' => 88,
            'feedback' => 'Solid implementation. Make sure to discuss cache locality implications in the future.',
            'submitted_at' => Carbon::now()->subDays(2),
            'graded_at' => Carbon::now()->subHours(6),
        ]);

        // 8. Quizzes & Questions
        $quiz1 = Quiz::create([
            'course_id' => $course1->id,
            'title' => 'Quiz 1: Computational Foundations',
            'description' => 'Timed assessment covering basic Big-O, computer memory models, and recursion principles.',
            'duration_minutes' => 20,
            'max_attempts' => 2,
            'available_from' => Carbon::now()->subDays(3),
            'available_until' => Carbon::now()->addDays(10),
            'is_published' => true,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'What is the worst-case time complexity of standard Binary Search on a sorted array of size n?',
            'options' => ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)'],
            'correct_answer' => 1,
            'points' => 5,
            'sort_order' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Which memory region is primarily used for dynamic memory allocation during runtime in C/C++?',
            'options' => ['Call Stack', 'Data Segment', 'Heap', 'Code Segment'],
            'correct_answer' => 2,
            'points' => 5,
            'sort_order' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'Which algorithmic design paradigm does Merge Sort utilize?',
            'options' => ['Greedy method', 'Divide and Conquer', 'Dynamic Programming', 'Backtracking'],
            'correct_answer' => 1,
            'points' => 5,
            'sort_order' => 3,
        ]);

        QuizAttempt::create([
            'quiz_id' => $quiz1->id,
            'student_id' => $student1->id,
            'answers' => [1 => 1, 2 => 2, 3 => 1],
            'score' => 15,
            'total_points' => 15,
            'started_at' => Carbon::now()->subDays(1)->subMinutes(25),
            'completed_at' => Carbon::now()->subDays(1)->subMinutes(10),
        ]);

        // 9. Grades
        Grade::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'numeric_grade' => 96.50,
            'letter_grade' => 'A',
            'remarks' => 'Dean\'s Honor Roll performance.',
        ]);

        Grade::create([
            'student_id' => $student1->id,
            'course_id' => $course2->id,
            'numeric_grade' => 92.00,
            'letter_grade' => 'A-',
            'remarks' => 'Consistent high performance.',
        ]);

        Grade::create([
            'student_id' => $student2->id,
            'course_id' => $course1->id,
            'numeric_grade' => 89.25,
            'letter_grade' => 'B+',
            'remarks' => 'Strong participation and project delivery.',
        ]);

        // 10. Attendance
        Attendance::create([
            'course_id' => $course1->id,
            'student_id' => $student1->id,
            'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'status' => 'present',
        ]);
        Attendance::create([
            'course_id' => $course1->id,
            'student_id' => $student2->id,
            'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'status' => 'present',
        ]);
        Attendance::create([
            'course_id' => $course1->id,
            'student_id' => $student1->id,
            'date' => Carbon::now()->subDays(4)->format('Y-m-d'),
            'status' => 'present',
        ]);

        // 11. Announcements
        Announcement::create([
            'author_id' => $admin->id,
            'course_id' => null,
            'title' => 'Welcome to Academic Year 2024-2025 at Villasis State Polytechnic University',
            'content' => 'We welcome all faculty, scholars, and staff to the new academic term. The university library, research laboratories, and academic counseling services are officially in full operation.',
            'is_pinned' => true,
        ]);

        Announcement::create([
            'author_id' => $teacher1->id,
            'course_id' => $course1->id,
            'title' => 'Midterm Examination Schedule & Review Pointers',
            'content' => 'The Midterm Examination for CS101 will be administered during regular class hours next week. Review topics include recursive algorithms, algorithm complexity analysis, and pointer mechanics.',
            'is_pinned' => true,
        ]);

        // 12. Course Materials
        CourseMaterial::create([
            'course_id' => $course1->id,
            'title' => 'Syllabus & Course Policy Handbook',
            'description' => 'Comprehensive outline of learning objectives, grading weights, and weekly lecture topics.',
            'file_path' => 'materials/cs101_syllabus.pdf',
            'file_type' => 'pdf',
            'file_size' => 1024000,
        ]);

        CourseMaterial::create([
            'course_id' => $course1->id,
            'title' => 'Lecture 01: Foundations of Computation Slides',
            'description' => 'Introductory lecture slides covering Church-Turing thesis and modern architecture.',
            'file_path' => 'materials/cs101_lecture_01.pdf',
            'file_type' => 'pdf',
            'file_size' => 2450000,
        ]);
    }
}
