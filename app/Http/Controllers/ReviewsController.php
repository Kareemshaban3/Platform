<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

class ReviewsController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Reviews::with('user')->get();
        return $this->successResponse($reviews, 'تم جلب كل المراجعات بنجاح');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        if (Reviews::where('user_id', $validated['user_id'])->exists()) {
            return $this->errorResponse('المستخدم لديه تعليق بالفعل', 422);
        }

        $review = Reviews::create($validated);

        return $this->successResponse($review, 'تم إضافة المراجعة بنجاح', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'rating'  => 'integer|min:1|max:5',
            'comment' => 'string|max:255',
        ]);

        $review = Reviews::where('user_id', Auth::id())->firstOrFail();
        $review->update($validated);

        return $this->successResponse($review, 'تم تعديل المراجعة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $review = Reviews::where('user_id', Auth::id())->firstOrFail();
        $review->delete();

        return $this->successResponse(null, 'تم حذف المراجعة بنجاح');
    }
}
