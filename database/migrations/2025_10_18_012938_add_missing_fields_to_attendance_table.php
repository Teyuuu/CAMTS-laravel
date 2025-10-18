<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->after('id');
            }

            if (!Schema::hasColumn('attendance', 'time_in')) {
                $table->timestamp('time_in')->nullable()->after('employee_id');
            }

            if (!Schema::hasColumn('attendance', 'time_out')) {
                $table->timestamp('time_out')->nullable()->after('time_in');
            }

            if (!Schema::hasColumn('attendance', 'hours_worked')) {
                $table->decimal('hours_worked', 5, 2)->nullable()->after('time_out');
            }

            if (!Schema::hasColumn('attendance', 'notes')) {
                $table->string('notes')->nullable()->after('hours_worked');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'time_in', 'time_out', 'hours_worked', 'notes']);
        });
    }
};
