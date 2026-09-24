<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SubAgency\CreateSubAgencyAction;
use App\Actions\SubAgency\DeleteSubAgencyAction;
use App\Actions\SubAgency\UpdateSubAgencyAction;
use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginatedIndexRequest;
use App\Http\Requests\SubAgency\StoreSubAgencyRequest;
use App\Http\Requests\SubAgency\UpdateSubAgencyRequest;
use App\Http\Resources\DropdownResource;
use App\Http\Resources\SubAgencyResource;
use App\Models\Agency;
use App\Models\SubAgency;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags الجهات الفرعية
 */
class SubAgencyController extends Controller implements HasMiddleware
{
    use ApiResponse;

    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.Permission::ViewSubAgencies->value, only: ['index', 'dropdown', 'show']),
            new Middleware('can:'.Permission::CreateSubAgencies->value, only: ['store']),
            new Middleware('can:'.Permission::UpdateSubAgencies->value, only: ['update']),
            new Middleware('can:'.Permission::DeleteSubAgencies->value, only: ['destroy']),
        ];
    }

    /**
     * الجهات الفرعية لجهة (مع تقسيم لصفحات)
     */
    public function index(PaginatedIndexRequest $request, Agency $agency): JsonResponse
    {
        $subAgencies = $agency->subAgencies()
            ->withCount('elements')
            ->when($request->search(), fn (Builder $query, string $search) => $query->whereLike('name', "%{$search}%"))
            ->orderBy('id')
            ->paginate($request->perPage());

        return $this->paginatedResponse(SubAgencyResource::collection($subAgencies));
    }

    /**
     * الجهات الفرعية لجهة للقائمة المنسدلة
     *
     * كل الجهات الفرعية للجهة بدون تقسيم لصفحات، `id` و `name` فقط، مرتبة بالاسم.
     */
    public function dropdown(Agency $agency): JsonResponse
    {
        $subAgencies = $agency->subAgencies()->orderBy('name')->get(['id', 'name']);

        return $this->successResponse(DropdownResource::collection($subAgencies), 'تم جلب البيانات بنجاح');
    }

    /**
     * إضافة جهة فرعية
     */
    public function store(StoreSubAgencyRequest $request, Agency $agency, CreateSubAgencyAction $createSubAgency): JsonResponse
    {
        $subAgency = $createSubAgency->execute($agency, $request->validated());

        return $this->successResponse(SubAgencyResource::make($subAgency), 'تمت إضافة الجهة الفرعية بنجاح', Response::HTTP_CREATED);
    }

    /**
     * عرض جهة فرعية
     *
     * مع جهتها الرئيسية.
     */
    public function show(SubAgency $subAgency): JsonResponse
    {
        return $this->successResponse(SubAgencyResource::make($subAgency->loadCount('elements')->load('agency')), 'تم جلب البيانات بنجاح');
    }

    /**
     * تعديل جهة فرعية
     *
     * يمكن نقلها لجهة رئيسية أخرى بإرسال `agency_id`، وتنتقل عناصرها معها.
     */
    public function update(UpdateSubAgencyRequest $request, SubAgency $subAgency, UpdateSubAgencyAction $updateSubAgency): JsonResponse
    {
        $subAgency = $updateSubAgency->execute($subAgency, $request->validated());

        return $this->successResponse(SubAgencyResource::make($subAgency), 'تم تعديل الجهة الفرعية بنجاح');
    }

    /**
     * حذف جهة فرعية
     *
     * حذف ناعم. يُرفض (422) إذا كانت مرتبطة بعناصر.
     */
    public function destroy(SubAgency $subAgency, DeleteSubAgencyAction $deleteSubAgency): JsonResponse
    {
        $deleteSubAgency->execute($subAgency);

        return $this->successResponse(message: 'تم حذف الجهة الفرعية بنجاح');
    }
}
