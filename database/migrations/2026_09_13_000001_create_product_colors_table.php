<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('color');
            $table->integer('stock')->default(0);
            $table->decimal('price', 12, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'color']);
        });

        // Tự động chuyển đổi dữ liệu màu sắc từ các sản phẩm hiện có sang bảng product_colors
        $products = DB::table('products')->whereNotNull('color')->get();
        foreach ($products as $product) {
            $colors = array_filter(array_map('trim', explode(',', $product->color)));
            if (! empty($colors)) {
                $count = count($colors);
                $perColorStock = max(1, (int) round($product->stock / $count));
                foreach ($colors as $cName) {
                    DB::table('product_colors')->insert([
                        'product_id' => $product->id,
                        'color' => $cName,
                        'stock' => $perColorStock,
                        'price' => $product->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_colors');
    }
};
