<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá!',
            'rating.min' => 'Số sao đánh giá tối thiểu là 1 sao!',
            'rating.max' => 'Số sao đánh giá tối đa là 5 sao!',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá của bạn!',
            'comment.min' => 'Nội dung đánh giá cần tối thiểu 5 ký tự!',
            'comment.max' => 'Nội dung đánh giá không được quá 1000 ký tự!',
        ]);

        $product = Product::findOrFail($productId);

        $existing = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $msg = 'Bạn đã gửi đánh giá cho sản phẩm này rồi! Cảm ơn phản hồi của bạn.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $review = ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => (int) $request->rating,
            'comment' => trim($request->comment),
            'is_approved' => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!',
                'review' => [
                    'id' => $review->id,
                    'user_name' => Auth::user()->name,
                    'user_avatar' => Auth::user()->avatar_url,
                    'rating' => $review->rating,
                    'comment' => e($review->comment),
                    'created_at' => $review->created_at->diffForHumans(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!');
    }
}
