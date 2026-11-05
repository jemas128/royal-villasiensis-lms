<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'enrollment_code',
        'name',
        'description',
        'credits',
        'department_id',
        'teacher_id',
        'semester_id',
        'max_students',
        'schedule',
        'room',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->enrollment_code)) {
                $course->enrollment_code = static::generateEnrollmentCode($course->code);
            }
        });
    }

    public static function generateEnrollmentCode(?string $prefix = null): string
    {
        $cleanPrefix = $prefix ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $prefix)) : 'CLASS';
        $cleanPrefix = substr($cleanPrefix, 0, 6);
        do {
            $code = $cleanPrefix . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        } while (static::where('enrollment_code', $code)->exists());

        return $code;
    }

    protected $casts = [
        'credits' => 'integer',
        'max_students' => 'integer',
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function pendingEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('status', 'pending');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->wherePivot('status', 'enrolled')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function pendingStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->wherePivot('status', 'pending')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
