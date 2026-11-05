<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
            $table->string('student_id', 20)->nullable()->unique()->after('role');
            $table->string('employee_id', 20)->nullable()->unique()->after('student_id');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->after('employee_id');
            $table->string('phone', 20)->nullable()->after('department_id');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['role', 'student_id', 'employee_id', 'department_id', 'phone', 'avatar', 'is_active']);
        });
    }
};
