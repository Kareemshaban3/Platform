<?php

namespace App\Http\Controllers;

use App\Models\SecurityAlert;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'device_id' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'device_id' => $validated['device_id'] ?? null,
            'role'      => 'client',
            'is_banned' => true,
        ]);

        return response()->json([
            'message' => 'تم التسجيل بنجاح. حسابك في انتظار مراجعة الإدارة لتفعيله.',
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'     => ['required', 'email'],
            'password'  => ['required', 'string'],
            'device_id' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => __('auth.failed')], 401);
        }

        if ($user->is_banned) {
            return response()->json(['message' => 'عذراً، هذا الحساب بانتظار مراجعة وتفعيل الإدارة.'], 403);
        }

        if (empty($user->device_id)) {
            $user->update([
                'device_id' => $validated['device_id'],
            ]);
        } elseif ($user->device_id !== $validated['device_id']) {
            SecurityAlert::create([
                'user_id' => $user->id,
                'attempted_device_id' => $validated['device_id'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'message' => ' محاولة دخول من جهاز مختلف عن الجهاز المربوط',
            ]);

            return response()->json([
                'message' => 'عذراً، هذا الحساب مفعّل على جهاز آخر ولا يمكن فتحه من هنا.',
            ], 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => __('auth.login_success'),
            'token'   => $token,
            'user'    => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
        }
        return response()->json(['message' => __('auth.logout_success')], 200);
    }
}
