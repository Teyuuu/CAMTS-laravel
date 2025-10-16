<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('category')->nullable();
            $table->string('priority')->nullable();
            $table->decimal('payable', 12, 2)->default(0);
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_payable');
    }
};
