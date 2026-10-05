<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'color' => 'nullable|string|max:255',
            'colors' => 'nullable|array',
            'colors.*.color' => 'nullable|string|max:100',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|string|max:100',
            'power' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['image', 'gallery_images', 'colors']);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $data['image_url'] = 'uploads/products/'.$filename;
        }

        $product = Product::create($data);

        // Lưu danh sách biến thể màu sắc
        $validColors = [];
        if (is_array($request->colors)) {
            foreach ($request->colors as $c) {
                $cName = trim($c['color'] ?? '');
                if ($cName !== '') {
                    $validColors[] = [
                        'color' => $cName,
                        'stock' => max(0, (int) ($c['stock'] ?? 0)),
                        'price' => ! empty($c['price']) && (float) $c['price'] > 0 ? (float) $c['price'] : null,
                    ];
                }
            }
        }

        if (! empty($validColors)) {
            foreach ($validColors as $vc) {
                $product->colors()->create($vc);
            }
            $product->color = implode(', ', array_column($validColors, 'color'));
            $product->stock = array_sum(array_column($validColors, 'stock'));
            $product->save();
        }

        // Lưu album ảnh gallery
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $idx => $gFile) {
                if ($gFile->isValid()) {
                    $gName = time().'_'.$idx.'_'.Str::slug(pathinfo($gFile->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$gFile->getClientOriginalExtension();
                    $gFile->move(public_path('uploads/products'), $gName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => 'uploads/products/'.$gName,
                        'sort_order' => $idx,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function show(Product $product)
    {
        return redirect()->route('product.show', $product->id);
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load(['images', 'colors']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'color' => 'nullable|string|max:255',
            'colors' => 'nullable|array',
            'colors.*.color' => 'nullable|string|max:100',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|string|max:100',
            'power' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['image', 'gallery_images', 'colors']);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($product->image_url && str_starts_with($product->image_url, 'uploads/') && file_exists(public_path($product->image_url))) {
                @unlink(public_path($product->image_url));
            }
            $file = $request->file('image');
            $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $data['image_url'] = 'uploads/products/'.$filename;
        }

        $product->update($data);

        // Cập nhật danh sách biến thể màu sắc
        $validColors = [];
        if (is_array($request->colors)) {
            foreach ($request->colors as $c) {
                $cName = trim($c['color'] ?? '');
                if ($cName !== '') {
                    $validColors[] = [
                        'color' => $cName,
                        'stock' => max(0, (int) ($c['stock'] ?? 0)),
                        'price' => ! empty($c['price']) && (float) $c['price'] > 0 ? (float) $c['price'] : null,
                    ];
                }
            }
        }

        if (! empty($validColors)) {
            $submittedColorNames = array_column($validColors, 'color');

            foreach ($validColors as $vc) {
                // updateOrCreate để giữ lại tồn kho thực nếu màu đã tồn tại
                $existing = $product->colors()->where('color', $vc['color'])->first();
                if ($existing) {
                    $updateData = ['price' => $vc['price']];
                    // Chỉ cập nhật stock nếu admin nhập rõ ràng (không null)
                    if ($vc['stock'] !== null) {
                        $updateData['stock'] = $vc['stock'];
                    }
                    $existing->update($updateData);
                } else {
                    $product->colors()->create($vc);
                }
            }

            // Xóa các màu không còn trong danh sách mới
            $product->colors()->whereNotIn('color', $submittedColorNames)->delete();

            $product->color = implode(', ', $submittedColorNames);
            $product->stock = $product->colors()->sum('stock');
            $product->save();
        } elseif ($request->has('colors_submitted')) {
            $product->colors()->delete();
        }

        // Lưu ảnh bổ sung vào album gallery
        if ($request->hasFile('gallery_images')) {
            $lastOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery_images') as $idx => $gFile) {
                if ($gFile->isValid()) {
                    $gName = time().'_'.($lastOrder + $idx + 1).'_'.Str::slug(pathinfo($gFile->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$gFile->getClientOriginalExtension();
                    $gFile->move(public_path('uploads/products'), $gName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => 'uploads/products/'.$gName,
                        'sort_order' => $lastOrder + $idx + 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(Product $product)
    {
        // Kiểm tra xem sản phẩm đã có đơn hàng chưa, nếu có thì không được xóa để bảo vệ lịch sử đơn hàng
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', "Không thể xóa sản phẩm '{$product->name}' vì đã có đơn hàng của khách liên quan đến sản phẩm này!");
        }

        // Xóa ảnh đại diện chính nếu là file upload nội bộ
        if ($product->image_url && str_starts_with($product->image_url, 'uploads/') && file_exists(public_path($product->image_url))) {
            @unlink(public_path($product->image_url));
        }

        // Xóa ảnh album gallery trên đĩa
        foreach ($product->images as $img) {
            if ($img->image_url && str_starts_with($img->image_url, 'uploads/') && file_exists(public_path($img->image_url))) {
                @unlink(public_path($img->image_url));
            }
        }
        $product->images()->delete();

        // Xóa các wishlist & review liên quan
        $product->wishlists()->delete();
        // Xóa tất cả review (bao gồm cả chưa duyệt) - không dùng reviews() vì có scope is_approved
        ProductReview::where('product_id', $product->id)->delete();

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Đã xóa sản phẩm thành công.');
    }

    public function destroyImage($id)
    {
        $image = ProductImage::findOrFail($id);

        if ($image->image_url && file_exists(public_path($image->image_url))) {
            @unlink(public_path($image->image_url));
        }

        $image->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa ảnh khỏi album!']);
        }

        return redirect()->back()->with('success', 'Đã xóa ảnh khỏi album sản phẩm.');
    }
}
