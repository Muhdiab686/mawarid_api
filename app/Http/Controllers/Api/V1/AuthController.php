<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags المصادقة
 */
class AuthController extends Controller
{
    /**
     * تسجيل الدخول
     *
     * يعيد توكن يُرسل في الطلبات التالية كـ `Authorization: Bearer {token}`.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request, LoginAction $login): array
    {
        $result = $login->execute($request->validated('username'), $request->validated('password'));

        return [
            'token' => $result['token'],
            'user' => UserResource::make($result['user']),
        ];
    }

    /**
     * تسجيل الخروج
     *
     * يلغي التوكن الحالي.
     */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /**
     * المستخدم الحالي
     *
     * بيانات المستخدم مع دوره وصلاحياته.
     */
    public function me(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }
}
