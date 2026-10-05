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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(15)->after('price');
            $table->string('brand')->nullable()->after('name');
            $table->string('color')->nullable()->after('stock');
            $table->string('capacity')->nullable()->after('color');
            $table->string('power')->nullable()->after('capacity');
            $table->decimal('original_price', 12, 2)->nullable()->after('power');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'brand', 'color', 'capacity', 'power', 'original_price']);
        });
    }
};
