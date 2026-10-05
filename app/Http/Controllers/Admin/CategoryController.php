<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $slug = Str::slug($value);
                    if (Category::where('slug', $slug)->exists()) {
                        $fail('Tên danh mục này tạo ra đường dẫn (slug) đã tồn tại. Vui lòng chọn tên khác!');
                    }
                },
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);

        $data['slug'] = Str::slug($request->name);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công.');
    }

    public function show(Category $category)
    {
        return redirect()->route('admin.categories.edit', $category);
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($category) {
                    $slug = Str::slug($value);
                    if (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                        $fail('Tên danh mục này tạo ra đường dẫn (slug) đã trùng với một danh mục khác!');
                    }
                },
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);

        $data['slug'] = Str::slug($request->name);

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được cập nhật thành công.');
    }

    public function destroy(Category $category)
    {
        $productCount = $category->products()->count();
        if ($productCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Không thể xóa danh mục '{$category->name}' vì đang có {$productCount} sản phẩm thuộc danh mục này! Vui lòng chuyển các sản phẩm sang danh mục khác trước khi xóa.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục thành công.');
    }
}
