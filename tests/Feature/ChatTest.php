<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo tài khoản Admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Tạo tài khoản User thường
        $this->user = User::factory()->create([
            'role' => 'user',
            'is_admin' => false,
        ]);
    }

    public function test_user_can_send_message_to_admin(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('user.chat.send'), [
                'message' => 'Xin chào Admin, tôi cần tư vấn lò vi sóng',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Xin chào Admin, tôi cần tư vấn lò vi sóng',
            'is_read' => false,
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->user->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Xin chào Admin, tôi cần tư vấn lò vi sóng',
        ]);
    }

    public function test_user_cannot_send_empty_message(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('user.chat.send'), [
                'message' => '   ',
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Nội dung tin nhắn không được để trống',
        ]);
    }

    public function test_user_can_get_chat_history_with_admin(): void
    {
        // Tạo hội thoại qua lại
        Message::create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Câu hỏi của khách',
            'is_read' => false,
        ]);

        Message::create([
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->user->id,
            'content' => 'Admin phản hồi',
            'is_read' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('user.chat.messages'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertCount(2, $data);
        $this->assertEquals('Câu hỏi của khách', $data[0]['content']);
        $this->assertEquals('Admin phản hồi', $data[1]['content']);
    }

    public function test_admin_can_get_list_of_chat_users(): void
    {
        $otherUser = User::factory()->create();

        // User 1 chat với admin
        Message::create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Tin nhắn từ User 1',
            'is_read' => false,
        ]);

        // User 2 chat với admin
        Message::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Tin nhắn từ User 2',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.chat.users'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertCount(2, $data);
        $returnedUserIds = array_column($data, 'id');
        $this->assertContains($this->user->id, $returnedUserIds);
        $this->assertContains($otherUser->id, $returnedUserIds);
    }

    public function test_admin_can_get_messages_of_specific_user(): void
    {
        Message::create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->admin->id,
            'content' => 'Cần bảo hành sản phẩm',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.chat.messages', $this->user->id));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertCount(1, $data);
        $this->assertEquals('Cần bảo hành sản phẩm', $data[0]['content']);
        $this->assertEquals($this->user->name, $data[0]['sender']['name']);
    }

    public function test_admin_can_send_message_to_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.chat.send'), [
                'user_id' => $this->user->id,
                'message' => 'Chào bạn, bên mình sẵn sàng hỗ trợ bảo hành nhé!',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->user->id,
            'content' => 'Chào bạn, bên mình sẵn sàng hỗ trợ bảo hành nhé!',
            'is_read' => true,
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->admin->id,
            'receiver_id' => $this->user->id,
            'content' => 'Chào bạn, bên mình sẵn sàng hỗ trợ bảo hành nhé!',
            'is_read' => 1,
        ]);
    }

    public function test_guest_cannot_access_chat_routes(): void
    {
        $this->post(route('user.chat.send'), ['message' => 'test'])
            ->assertRedirect(route('login'));

        $this->get(route('user.chat.messages'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.chat.users'))
            ->assertRedirect(route('login'));
    }
}
