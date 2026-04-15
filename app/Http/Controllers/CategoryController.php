<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $categories = Category::query()
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->paginate(15);

        return $this->successResponse($categories, __('category.retrieved_success'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return $this->successResponse($category, __('category.created_success'), 201);
    }

    public function show(Category $category)
    {
        $user = auth()->user();

        $category->load('attachments');

        $hasAccess = false;

        if ($user) {
            if ($user->role === 'admin' || $user->categories->contains($category->id)) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            $category->attachments->makeHidden(['full_path', 'path']);
        }

        return $this->successResponse([
            'category'   => $category,
            'has_access' => $hasAccess
        ], 'تم جلب بيانات القسم ومرفقاته');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return $this->successResponse($category, __('category.updated_success'));
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        return $this->successResponse(null, __('category.deleted_success'));
    }
}
