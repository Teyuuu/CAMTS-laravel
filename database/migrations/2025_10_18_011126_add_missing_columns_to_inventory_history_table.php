<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_history', 'product')) {
                $table->string('product')->after('id');
            }
            if (!Schema::hasColumn('inventory_history', 'action')) {
                $table->string('action')->after('product'); // e.g., 'Added', 'Removed', 'Updated'
            }
            if (!Schema::hasColumn('inventory_history', 'quantity')) {
                $table->decimal('quantity', 10, 2)->after('action');
            }
            if (!Schema::hasColumn('inventory_history', 'remaining_stock')) {
                $table->decimal('remaining_stock', 10, 2)->after('quantity');
            }
            if (!Schema::hasColumn('inventory_history', 'notes')) {
                $table->text('notes')->nullable()->after('remaining_stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_history', function (Blueprint $table) {
            $table->dropColumn([
                'product',
                'action',
                'quantity',
                'remaining_stock',
                'notes',
            ]);
        });
    }
};
