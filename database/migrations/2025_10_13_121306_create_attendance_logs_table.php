<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->string('action'); // 'Time In' or 'Time Out'
            $table->dateTime('timestamp');
            $table->timestamps();
            
            $table->index('employee_id');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};

