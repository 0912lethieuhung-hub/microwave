<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_2m':
                    $query->where('price', '<', 2000000);
                    break;
                case '2m_5m':
                    $query->whereBetween('price', [2000000, 5000000]);
                    break;
                case 'over_5m':
                    $query->where('price', '>', 5000000);
                    break;
            }
        }

        if ($request->filled('brand')) {
            $brand = $request->brand;
            $query->where('brand', $brand);
        }

        // Sort
        switch ($request->get('sort', 'default')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $brands = Product::whereNotNull('brand')->distinct()->pluck('brand')->sort()->values();

        // Danh sách product_id đã yêu thích của user (tránh N+1 query)
        $wishlistProductIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->toArray()
            : [];

        return view('products.index', compact('products', 'categories', 'brands', 'wishlistProductIds'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'reviews.user', 'colors'])->findOrFail($id);
        $product->increment('views'); // Increase view count

        // Lưu và lấy danh sách sản phẩm đã xem gần đây từ Session
        $recentIds = session()->get('recently_viewed', []);
        $recentlyViewed = collect();
        if (! empty($recentIds)) {
            $otherIds = array_values(array_diff($recentIds, [$product->id]));
            if (! empty($otherIds)) {
                $recentlyViewed = Product::whereIn('id', $otherIds)->take(4)->get();
            }
        }

        // Cập nhật session recently_viewed (đưa id hiện tại lên đầu, tối đa 8 sản phẩm)
        $recentIds = array_values(array_unique(array_merge([$product->id], $recentIds)));
        if (count($recentIds) > 8) {
            $recentIds = array_slice($recentIds, 0, 8);
        }
        session()->put('recently_viewed', $recentIds);

        // Sản phẩm liên quan (cùng danh mục, trừ sản phẩm hiện tại)
        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'recentlyViewed'));
    }
}
