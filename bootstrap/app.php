<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * Unified API error shape: {success: false, message[, errors]}.
         * Each error keeps its real status code (401, 403, 404, 422, 429, ...);
         * only unexpected exceptions become 500. Reporting/logging is unaffected.
         */
        $exceptions->render(function (Throwable $exception, Request $request): ?JsonResponse {
            if (! $request->is('api/*') || $exception instanceof HttpResponseException) {
                return null;
            }

            [$status, $message] = match (true) {
                $exception instanceof ValidationException => [
                    $exception->status,
                    collect($exception->errors())->flatten()->first() ?? 'البيانات المدخلة غير صالحة',
                ],
                $exception instanceof AuthenticationException => [401, 'غير مصرح لك بالوصول، يرجى تسجيل الدخول'],
                $exception instanceof HttpExceptionInterface => [
                    $exception->getStatusCode(),
                    match ($exception->getStatusCode()) {
                        403 => 'ليس لديك صلاحية لتنفيذ هذا الإجراء',
                        404 => 'المورد أو الرابط المطلوب غير موجود',
                        405 => 'طريقة الطلب غير مدعومة لهذا الرابط',
                        429 => 'محاولات كثيرة، يرجى المحاولة لاحقاً',
                        default => 'تعذر تنفيذ الطلب',
                    },
                ],
                default => [500, config('app.debug') ? $exception->getMessage() : 'حدث خطأ داخلي في الخادم'],
            };

            $body = ['success' => false, 'message' => $message];

            if ($exception instanceof ValidationException) {
                $body['errors'] = $exception->errors();
            }

            $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

            return response()->json($body, $status, $headers);
        });
    })->create();
