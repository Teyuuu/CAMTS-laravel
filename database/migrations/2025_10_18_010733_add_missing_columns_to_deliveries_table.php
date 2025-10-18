<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveries', 'company')) {
                $table->string('company')->after('id');
            }
            if (!Schema::hasColumn('deliveries', 'contact_person')) {
                $table->string('contact_person')->after('company');
            }
            if (!Schema::hasColumn('deliveries', 'phone')) {
                $table->string('phone')->after('contact_person');
            }
            if (!Schema::hasColumn('deliveries', 'product')) {
                $table->string('product')->after('phone');
            }
            if (!Schema::hasColumn('deliveries', 'qty')) {
                $table->integer('qty')->after('product');
            }
            if (!Schema::hasColumn('deliveries', 'priority')) {
                $table->string('priority')->after('qty');
            }
            if (!Schema::hasColumn('deliveries', 'delivery_date')) {
                $table->date('delivery_date')->after('priority');
            }
            if (!Schema::hasColumn('deliveries', 'delivery_time')) {
                $table->time('delivery_time')->after('delivery_date');
            }
            if (!Schema::hasColumn('deliveries', 'driver')) {
                $table->string('driver')->after('delivery_time');
            }
            if (!Schema::hasColumn('deliveries', 'address')) {
                $table->text('address')->after('driver');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'company',
                'contact_person',
                'phone',
                'product',
                'qty',
                'priority',
                'delivery_date',
                'delivery_time',
                'driver',
                'address',
            ]);
        });
    }
};
