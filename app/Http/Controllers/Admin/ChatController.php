<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Lấy danh sách những User đã từng nhắn tin với Admin
     */
    public function getUsers(): JsonResponse
    {
        $adminId = (int) Auth::id();

        // Tìm tất cả ID của người dùng có tương tác với admin
        $userIds = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($msg) use ($adminId) {
                return (int) $msg->sender_id === $adminId ? (int) $msg->receiver_id : (int) $msg->sender_id;
            })
            ->unique()
            ->toArray();

        // Lấy thông tin chi tiết các User đó kèm thông tin tin nhắn mới nhất
        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', $adminId)
            ->select('id', 'name')
            ->get()
            ->map(function ($user) use ($adminId) {
                $lastMsg = Message::where(function ($q) use ($user, $adminId) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $adminId);
                })->orWhere(function ($q) use ($user, $adminId) {
                    $q->where('sender_id', $adminId)->where('receiver_id', $user->id);
                })->latest('created_at')->first();

                $userData = $user->toArray();
                $userData['last_message'] = $lastMsg ? $lastMsg->content : null;
                $userData['last_time'] = $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m') : '';

                return $userData;
            });

        return response()->json($users);
    }

    /**
     * Lấy lịch sử tin nhắn của một User cụ thể
     */
    public function getMessages(int $userId): JsonResponse
    {
        $adminId = (int) Auth::id();

        // Đánh dấu các tin nhắn của user gửi cho admin là đã đọc
        Message::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('sender')
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($adminId) {
                // Thêm is_mine: true nếu Admin là người gửi
                $data = $msg->toArray();
                $data['is_mine'] = (int) $msg->sender_id === $adminId;
                $data['time_formatted'] = $msg->created_at ? $msg->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') : '';
                $data['time_short'] = $msg->created_at ? $msg->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') : '';

                return $data;
            });

        return response()->json($messages);
    }

    /**
     * Admin gửi tin nhắn phản hồi
     */
    public function send(Request $request): JsonResponse
    {
        // Kiểm tra dữ liệu đầu vào — dùng input() thay vì magic property
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->integer('user_id'),
            'content' => $request->input('message'),
            'is_read' => true,
        ]);

        // Trả về kèm is_mine và thời gian để FE có thể render ngay nếu cần
        return response()->json(array_merge($message->toArray(), [
            'is_mine' => true,
            'time_formatted' => $message->created_at ? $message->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') : '',
            'time_short' => $message->created_at ? $message->created_at->timezone('Asia/Ho_Chi_Minh')->format('H:i') : '',
        ]));
    }
}
