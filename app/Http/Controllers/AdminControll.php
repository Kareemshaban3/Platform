<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminControll extends Controller
{
    use ApiResponse;

    public function index()
    {
        $users = User::paginate(10);
        return $this->successResponse($users, __('auth.users_retrieved'));
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->load('categories');

        return $this->successResponse($user, 'تم جلب بيانات المستخدم');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', Password::min(8)],
            'device_id' => ['nullable', 'string'],
            'role'      => ['required', 'in:client,admin'],
            'is_banned' => ['required', 'boolean'],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'device_id' => $validated['device_id'] ?? null,
            'role'      => $validated['role'],
            'is_banned' => $validated['is_banned'],
        ]);

        return $this->successResponse($user, 'تم إنشاء المستخدم بنجاح.', 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'email'     => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $id],
            'password'  => ['sometimes', Password::min(8)],
            'role'      => ['sometimes', 'in:client,admin'],
            'is_banned' => ['sometimes', 'boolean'],
            'device_id' => ['sometimes', 'string', 'nullable'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return $this->successResponse($user, 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return $this->errorResponse('لا يمكنك حذف حسابك الشخصي!', 400);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return $this->successResponse(null, 'تم حذف المستخدم بنجاح.');
    }





    public function toggleBan($id)
    {
        if (auth()->id() == $id) {
            return $this->errorResponse('لا يمكنك حظر حسابك الشخصي!', 400);
        }

        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->save();

        $message = $user->is_banned ? 'تم حظر المستخدم بنجاح.' : 'تم إلغاء حظر المستخدم بنجاح.';
        return $this->successResponse($user, $message);
    }


    public function changeUserRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:client,admin']);

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return $this->successResponse($user, 'تم تغيير رتبة المستخدم بنجاح');
    }
    public function toggleCategoryAccess(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'action'       => 'required|in:open,close',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($request->action === 'open') {
            $user->categories()->syncWithoutDetaching($request->category_ids);
            return $this->successResponse(null, 'تم فتح الأقسام المحددة للمستخدم بنجاح.');
        } else {
            $user->categories()->detach($request->category_ids);
            return $this->successResponse(null, 'تم قفل الأقسام المحددة عن المستخدم بنجاح.');
        }
    }


    public function resetDevice($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'device_id' => null,
            'is_device_verified' => false
        ]);

        return $this->successResponse($user, 'تم تصفير جهاز المستخدم، يمكنه الآن الدخول من جهاز جديد.');
    }
}
