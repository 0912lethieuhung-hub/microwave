<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->user = User::factory()->create([
            'role' => 'customer',
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Lò Vi Sóng Âm Tủ',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Lò Vi Sóng Âm Tủ',
            'slug' => 'lo-vi-song-am-tu',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Lò Cũ',
            'slug' => 'lo-cu',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Lò Mới Cập Nhật',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Lò Mới Cập Nhật',
            'slug' => 'lo-moi-cap-nhat',
        ]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $category = Category::create([
            'name' => 'Lò Vi Sóng Cơ',
            'slug' => 'lo-vi-song-co',
        ]);

        Product::create([
            'name' => 'Lò Sharp Cơ 20L',
            'price' => 1200000,
            'stock' => 5,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_regular_user_cannot_access_admin_categories(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.categories.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_creating_category_with_duplicate_slug_is_rejected(): void
    {
        Category::create([
            'name' => 'Lò Vi Sóng Điện Tử',
            'slug' => 'lo-vi-song-dien-tu',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Lò vi sóng điện tử', // Tạo ra slug trùng 'lo-vi-song-dien-tu'
        ]);

        $response->assertSessionHasErrors('name');
    }
}
