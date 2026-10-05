<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Login surveyor, kembalikan token Sanctum.
     *
     * POST /api/v1/auth/login
     * Body: { email, password }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with('roles')
            ->where('email', $request->email)
            ->first();

        // Cek kredensial
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Cek user aktif
        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ], 403);
        }

        // Hanya role surveyor atau admin yang boleh login via API
        if (! $user->hasAnyRole(['surveyor', 'admin'])) {
            return response()->json([
                'message' => 'Akun Anda tidak memiliki akses ke aplikasi ini.',
            ], 403);
        }

        // Hapus token lama dengan nama yang sama (biar tidak numpuk)
        $user->tokens()->where('name', 'rtlh-mobile')->delete();

        $token = $user->createToken('rtlh-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('slug'),
            ],
        ]);
    }

    /**
     * Logout — cabut token yang sedang dipakai.
     *
     * POST /api/v1/auth/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Info user yang sedang login.
     *
     * GET /api/v1/auth/me
     * Header: Authorization: Bearer {token}
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles');

        return response()->json([
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('slug'),
            ],
        ]);
    }
}
