<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags المصادقة
 */
class AuthController extends Controller
{
    use ApiResponse;

    /**
     * تسجيل الدخول
     *
     * يعيد توكن يُرسل في الطلبات التالية كـ `Authorization: Bearer {token}`.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request, LoginAction $login): JsonResponse
    {
        $result = $login->execute($request->validated('username'), $request->validated('password'));

        return $this->successResponse([
            'token' => $result['token'],
            'user' => UserResource::make($result['user']),
        ], 'تم تسجيل الدخول بنجاح');
    }

    /**
     * تسجيل الخروج
     *
     * يلغي التوكن الحالي.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(message: 'تم تسجيل الخروج بنجاح');
    }

    /**
     * المستخدم الحالي
     *
     * بيانات المستخدم مع دوره وصلاحياته.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(UserResource::make($request->user()), 'تم جلب البيانات بنجاح');
    }
}
