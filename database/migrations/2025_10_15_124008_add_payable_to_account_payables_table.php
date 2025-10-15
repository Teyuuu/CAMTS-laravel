<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_payables', function (Blueprint $table) {
            $table->decimal('payable', 10, 2)->default(0)->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('account_payables', function (Blueprint $table) {
            $table->dropColumn('payable');
        });
    }
};
