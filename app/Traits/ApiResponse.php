<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unified API response shape: {success, message, data[, meta]}.
 *
 * Error responses use the same shape ({success: false, message[, errors]})
 * and are rendered in bootstrap/app.php.
 */
trait ApiResponse
{
    protected function successResponse(mixed $data = null, string $message = 'تمت العملية بنجاح', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function paginatedResponse(ResourceCollection $collection, string $message = 'تم جلب البيانات بنجاح'): JsonResponse
    {
        /** @var LengthAwarePaginator<int, mixed> $paginator */
        $paginator = $collection->resource;

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $collection,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }
}
