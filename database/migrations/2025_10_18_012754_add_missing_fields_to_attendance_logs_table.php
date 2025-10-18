<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_logs', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->after('id');
            }

            if (!Schema::hasColumn('attendance_logs', 'action')) {
                $table->string('action')->after('employee_id');
            }

            if (!Schema::hasColumn('attendance_logs', 'timestamp')) {
                $table->timestamp('timestamp')->after('action');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'action', 'timestamp']);
        });
    }
};
