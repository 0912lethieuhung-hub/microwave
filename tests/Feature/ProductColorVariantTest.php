<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductColorVariantTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $admin;

    protected Category $category;

    protected Product $product;

    protected ProductColor $colorBlack;

    protected ProductColor $colorSilver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->category = Category::create([
            'name' => 'Lò vi sóng điện tử',
            'slug' => 'lo-vi-song-dien-tu',
        ]);

        $this->product = Product::create([
            'name' => 'Lò vi sóng Sharp R-G222VN',
            'brand' => 'Sharp',
            'price' => 1500000,
            'stock' => 15,
            'color' => 'Đen Nhám, Bạc Inox',
            'category_id' => $this->category->id,
        ]);

        // Biến thể Đen Nhám: giá mặc định 1.500.000đ, kho 10
        $this->colorBlack = ProductColor::create([
            'product_id' => $this->product->id,
            'color' => 'Đen Nhám',
            'stock' => 10,
            'price' => null, // Dùng giá chung 1.500.000đ
        ]);

        // Biến thể Bạc Inox: giá cao hơn 1.800.000đ, kho 5
        $this->colorSilver = ProductColor::create([
            'product_id' => $this->product->id,
            'color' => 'Bạc Inox',
            'stock' => 5,
            'price' => 1800000,
        ]);
    }

    public function test_product_model_correctly_calculates_color_stock_and_price(): void
    {
        $this->assertEquals(10, $this->product->getColorStock('Đen Nhám'));
        $this->assertEquals(1500000, $this->product->getColorPrice('Đen Nhám'));

        $this->assertEquals(5, $this->product->getColorStock('Bạc Inox'));
        $this->assertEquals(1800000, $this->product->getColorPrice('Bạc Inox'));
    }

    public function test_user_can_add_specific_color_to_cart_with_variant_price(): void
    {
        $response = $this->actingAs($this->user)->post(route('cart.add', $this->product->id), [
            'color' => 'Bạc Inox',
            'quantity' => 2,
        ]);

        $response->assertSessionHas('success');

        $cart = Cart::where('user_id', $this->user->id)->first();
        $this->assertNotNull($cart);

        $cartItem = CartItem::where('cart_id', $cart->id)->first();
        $this->assertEquals('Bạc Inox', $cartItem->color);
        $this->assertEquals(2, $cartItem->quantity);
        $this->assertEquals(1800000, $cartItem->price);
        $this->assertEquals(3600000, $cartItem->total_price);
    }

    public function test_adding_quantity_exceeding_color_variant_stock_is_rejected(): void
    {
        // Màu Bạc Inox chỉ có 5 chiếc
        $response = $this->actingAs($this->user)->post(route('cart.add', $this->product->id), [
            'color' => 'Bạc Inox',
            'quantity' => 6,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', [
            'color' => 'Bạc Inox',
        ]);
    }

    public function test_checkout_deducts_specific_color_variant_stock(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'color' => 'Bạc Inox',
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'shipping_address' => '123 Phố Huế, Hà Nội',
            'phone' => '0988123456',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('checkout.success'));

        // Kiểm tra OrderItem có đúng màu và giá biến thể 1.800.000đ
        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'color' => 'Bạc Inox',
            'price' => 1800000,
            'quantity' => 3,
        ]);

        // Tồn kho màu Bạc Inox giảm từ 5 xuống 2
        $this->assertEquals(2, $this->colorSilver->fresh()->stock);

        // Tổng tồn kho của sản phẩm giảm từ 15 xuống 12
        $this->assertEquals(12, $this->product->fresh()->stock);
    }

    public function test_cancelling_order_restores_color_variant_stock(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total_price' => 5400000,
            'status' => 'pending',
            'shipping_status' => 'ready_to_pick',
            'shipping_address' => '123 Phố Huế, Hà Nội',
            'phone' => '0988123456',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price' => 1800000,
            'color' => 'Bạc Inox',
        ]);

        // Trừ trước để giả lập sau khi mua hàng
        $this->colorSilver->decrement('stock', 3);
        $this->product->decrement('stock', 3);
        $this->assertEquals(2, $this->colorSilver->fresh()->stock);
        $this->assertEquals(12, $this->product->fresh()->stock);

        // Khách hàng hủy đơn
        $response = $this->actingAs($this->user)->post(route('user.orders.cancel', $order->id));
        $response->assertSessionHas('success');

        // Tồn kho màu Bạc Inox phải được hoàn trả lại thành 5
        $this->assertEquals(5, $this->colorSilver->fresh()->stock);
        $this->assertEquals(15, $this->product->fresh()->stock);
    }

    public function test_admin_can_create_product_with_custom_color_variants(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Lò vi sóng Toshiba Nướng',
            'category_id' => $this->category->id,
            'price' => 2000000,
            'stock' => 30,
            'colors' => [
                ['color' => 'Trắng Tinh Tế', 'stock' => 12, 'price' => 2000000],
                ['color' => 'Xám Titan', 'stock' => 18, 'price' => 2300000],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $created = Product::where('name', 'Lò vi sóng Toshiba Nướng')->first();
        $this->assertNotNull($created);
        $this->assertEquals(30, $created->stock); // 12 + 18
        $this->assertStringContainsString('Trắng Tinh Tế', $created->color);
        $this->assertStringContainsString('Xám Titan', $created->color);

        $this->assertDatabaseHas('product_colors', [
            'product_id' => $created->id,
            'color' => 'Trắng Tinh Tế',
            'stock' => 12,
            'price' => 2000000,
        ]);

        $this->assertDatabaseHas('product_colors', [
            'product_id' => $created->id,
            'color' => 'Xám Titan',
            'stock' => 18,
            'price' => 2300000,
        ]);
    }

    public function test_adding_non_existent_color_variant_to_cart_is_rejected(): void
    {
        $this->assertEquals(0, $this->product->getColorStock('Màu Không Tồn Tại'));

        $response = $this->actingAs($this->user)->post(route('cart.add', $this->product->id), [
            'color' => 'Màu Không Tồn Tại',
            'quantity' => 1,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', [
            'color' => 'Màu Không Tồn Tại',
        ]);
    }

    public function test_checkout_with_negative_shipping_fee_is_rejected_or_sanitized(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'color' => 'Đen Nhám',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'shipping_address' => '123 Phố Huế, Hà Nội',
            'phone' => '0988123456',
            'payment_method' => 'cod',
            'shipping_fee' => -500000,
        ]);

        // Validation nên chặn shipping_fee < 0
        $response->assertSessionHasErrors('shipping_fee');
    }
}
