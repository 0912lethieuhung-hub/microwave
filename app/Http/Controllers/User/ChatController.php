<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Gửi tin nhắn từ User tới Admin
     */
    public function send(Request $request): JsonResponse
    {
        // 1. Lấy nội dung từ request JSON
        $messageText = $request->input('message');

        // 2. Kiểm tra nội dung trống
        if (empty(trim((string) $messageText))) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        // 3. Xác định Admin nhận tin
        // Ưu tiên tìm user có role là admin, nếu không thấy thì mặc định lấy ID 1
        $admin = User::where('role', 'admin')->first();
        $receiverId = $admin ? $admin->id : 1;

        try {
            // 4. Lưu tin nhắn vào Database
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'content' => $messageText,
                'is_read' => false,
            ]);

            // Trả về dữ liệu tin nhắn vừa tạo để Frontend hiển thị ngay (nếu cần)
            return response()->json(array_merge($message->toArray(), [
                'time_formatted' => $message->created_at ? $message->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') : '',
                'time_short' => $message->created_at ? $message->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') : '',
            ]));
        } catch (Exception $e) {
            return response()->json(['error' => 'Không thể gửi tin nhắn: '.$e->getMessage()], 500);
        }
    }

    /**
     * Lấy lịch sử chat giữa User hiện tại và Admin
     */
    public function getMessages(): JsonResponse
    {
        $userId = Auth::id();

        // Tìm Admin để lọc tin nhắn qua lại
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        // Lấy toàn bộ hội thoại giữa 2 người
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $adminId) {
                // Tin nhắn User gửi cho Admin
                $q->where('sender_id', $userId)
                    ->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                // Tin nhắn Admin phản hồi cho User
                $q->where('sender_id', $adminId)
                    ->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc') // Sắp xếp theo thứ tự thời gian tăng dần
            ->get()
            ->map(function ($msg) {
                $data = $msg->toArray();
                $data['time_formatted'] = $msg->created_at ? $msg->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') : '';
                $data['time_short'] = $msg->created_at ? $msg->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') : '';

                return $data;
            });

        return response()->json($messages);
    }
}
