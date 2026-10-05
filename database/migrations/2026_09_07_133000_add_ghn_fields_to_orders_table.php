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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_status')->default('ready_to_pick')->after('status');
            $table->string('ghn_order_code')->nullable()->after('shipping_status');
            $table->integer('ghn_total_fee')->default(0)->after('ghn_order_code');
            $table->integer('to_district_id')->nullable()->after('ghn_total_fee');
            $table->string('to_ward_code')->nullable()->after('to_district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_status',
                'ghn_order_code',
                'ghn_total_fee',
                'to_district_id',
                'to_ward_code',
            ]);
        });
    }
};
