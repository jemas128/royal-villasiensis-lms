<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add enrollment_code column to courses table
        if (!Schema::hasColumn('courses', 'enrollment_code')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('enrollment_code', 32)->nullable()->unique()->after('code');
            });
        }

        // 2. Modify status enum on enrollments table to include pending and rejected
        // MySQL direct statement ensures enum alteration without doctrine/dbal issues
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'enrolled', 'dropped', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");
        }

        // 3. Populate existing courses with unique enrollment codes
        $courses = DB::table('courses')->whereNull('enrollment_code')->get();
        foreach ($courses as $course) {
            $prefix = preg_replace('/[^A-Za-z0-9]/', '', $course->code);
            $prefix = strtoupper(substr($prefix, 0, 6));
            do {
                $code = $prefix . '-' . strtoupper(Str::random(4));
            } while (DB::table('courses')->where('enrollment_code', $code)->exists());

            DB::table('courses')->where('id', $course->id)->update([
                'enrollment_code' => $code,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'enrollment_code')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('enrollment_code');
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('enrolled', 'dropped', 'completed') NOT NULL DEFAULT 'enrolled'");
        }
    }
};
