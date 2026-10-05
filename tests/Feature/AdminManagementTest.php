<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
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

    public function test_admin_can_view_reports_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Báo Cáo Doanh Thu');
        $response->assertSee('Doanh thu theo danh mục');
    }

    public function test_admin_can_view_reports_charts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.charts'));

        $response->assertStatus(200);
        $response->assertSee('Biểu Đồ Báo Cáo Doanh Thu');
        $response->assertSee('categoryRevenueChart');
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@example.com',
            'password' => 'password123',
            'role' => 'user',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'nguyenvana@example.com',
            'name' => 'Nguyễn Văn A',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->user->id), [
            'name' => 'Tên Mới Sau Sửa',
            'email' => $this->user->email,
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Tên Mới Sau Sửa',
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $targetUser->id));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_guest_cannot_access_admin_reports_or_users(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->post(route('admin.users.store'), [])->assertRedirect(route('login'));
        $this->get(route('admin.transactions.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_transactions(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.transactions.index'));

        $response->assertStatus(200);
        $response->assertSee('Giao Dịch Thanh Toán');
    }

    public function test_admin_can_export_orders_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['export' => 1]));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }

    public function test_admin_can_bulk_update_order_status(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total_price' => 500000,
            'status' => 'pending',
            'shipping_address' => 'Hà Nội',
            'phone' => '0912345678',
            'payment_method' => 'cod',
            'shipping_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.bulk-status'), [
            'order_ids' => [$order->id],
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }
}
