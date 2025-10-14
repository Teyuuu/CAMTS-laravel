<?php
// database/migrations/xxxx_create_inventory_history_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_history', function (Blueprint $table) {
            $table->id();
            $table->string('product');
            $table->enum('action', ['IN', 'OUT']);
            $table->integer('quantity');
            $table->integer('remaining_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('product');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_history');
    }
};