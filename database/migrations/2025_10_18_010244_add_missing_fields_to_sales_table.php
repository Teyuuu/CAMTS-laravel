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
    Schema::table('sales', function (Blueprint $table) {
        if (!Schema::hasColumn('sales', 'product')) {
            $table->string('product')->after('company');
        }

        if (!Schema::hasColumn('sales', 'quantity')) {
            $table->integer('quantity')->after('product');
        }

        if (!Schema::hasColumn('sales', 'price_per_kg')) {
            $table->decimal('price_per_kg', 10, 2)->after('quantity');
        }

        if (!Schema::hasColumn('sales', 'payment_method')) {
            $table->string('payment_method')->after('amount');
        }
    });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['product', 'quantity', 'price_per_kg', 'payment_method']);
        });
    }
};
