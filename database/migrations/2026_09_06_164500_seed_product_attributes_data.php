<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $products = Product::all();
        foreach ($products as $p) {
            $name = mb_strtolower($p->name);

            // Brand
            if (str_contains($name, 'sharp')) {
                $p->brand = 'Sharp';
                $p->color = 'Bạc Inox, Xám';
                $p->capacity = '20 Lít';
                $p->power = '800W';
                $p->stock = 18;
                $p->original_price = $p->price * 1.25;
            } elseif (str_contains($name, 'retro')) {
                $p->brand = 'Retro Classic';
                $p->color = 'Xanh Ngọc Bích, Trắng Kem';
                $p->capacity = '20 Lít';
                $p->power = '700W';
                $p->stock = 7;
                $p->original_price = $p->price * 1.2;
            } elseif (str_contains($name, 'panasonic')) {
                $p->brand = 'Panasonic';
                $p->color = 'Đen Bóng, Bạc Kim Loại';
                $p->capacity = '25 Lít';
                $p->power = '900W';
                $p->stock = 12;
                $p->original_price = $p->price * 1.18;
            } elseif (str_contains($name, 'electrolux')) {
                $p->brand = 'Electrolux';
                $p->color = 'Đen Gương, Thép Không Gỉ';
                $p->capacity = '23 Lít';
                $p->power = '1000W Nướng / 800W';
                $p->stock = 15;
                $p->original_price = $p->price * 1.22;
            } elseif (str_contains($name, 'công nghiệp') || str_contains($name, 'pro')) {
                $p->brand = 'ProMaster';
                $p->color = 'Inox Bạc 304';
                $p->capacity = '34 Lít';
                $p->power = '1800W';
                $p->stock = 3;
                $p->original_price = $p->price * 1.15;
            } elseif (str_contains($name, 'roler')) {
                $p->brand = 'Roler';
                $p->color = 'Đen Nhám, Trắng';
                $p->capacity = '20 Lít';
                $p->power = '800W';
                $p->stock = 9;
                $p->original_price = $p->price * 1.2;
            } else {
                $p->brand = 'Chính Hãng';
                $p->color = 'Đen, Bạc';
                $p->capacity = '23 Lít';
                $p->power = '900W';
                $p->stock = 10;
                $p->original_price = $p->price * 1.2;
            }
            $p->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
