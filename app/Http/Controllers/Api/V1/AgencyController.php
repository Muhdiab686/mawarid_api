<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Agency\CreateAgencyAction;
use App\Actions\Agency\DeleteAgencyAction;
use App\Actions\Agency\UpdateAgencyAction;
use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\StoreAgencyRequest;
use App\Http\Requests\Agency\UpdateAgencyRequest;
use App\Http\Requests\PaginatedIndexRequest;
use App\Http\Resources\AgencyResource;
use App\Http\Resources\DropdownResource;
use App\Models\Agency;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags الجهات
 */
class AgencyController extends Controller implements HasMiddleware
{
    use ApiResponse;

    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.Permission::ViewAgencies->value, only: ['index', 'dropdown', 'show']),
            new Middleware('can:'.Permission::CreateAgencies->value, only: ['store']),
            new Middleware('can:'.Permission::UpdateAgencies->value, only: ['update']),
            new Middleware('can:'.Permission::DeleteAgencies->value, only: ['destroy']),
        ];
    }

    /**
     * قائمة الجهات (مع تقسيم لصفحات)
     */
    public function index(PaginatedIndexRequest $request): JsonResponse
    {
        $agencies = Agency::query()
            ->withCount(['subAgencies', 'elements'])
            ->when($request->search(), fn (Builder $query, string $search) => $query->whereLike('name', "%{$search}%"))
            ->orderBy('id')
            ->paginate($request->perPage());

        return $this->paginatedResponse(AgencyResource::collection($agencies));
    }

    /**
     * الجهات للقائمة المنسدلة
     *
     * كل الجهات بدون تقسيم لصفحات، `id` و `name` فقط، مرتبة بالاسم.
     */
    public function dropdown(): JsonResponse
    {
        $agencies = Agency::query()->orderBy('name')->get(['id', 'name']);

        return $this->successResponse(DropdownResource::collection($agencies), 'تم جلب البيانات بنجاح');
    }

    /**
     * إضافة جهة
     */
    public function store(StoreAgencyRequest $request, CreateAgencyAction $createAgency): JsonResponse
    {
        $agency = $createAgency->execute($request->validated());

        return $this->successResponse(AgencyResource::make($agency), 'تمت إضافة الجهة بنجاح', Response::HTTP_CREATED);
    }

    /**
     * عرض جهة
     *
     * مع جهاتها الفرعية.
     */
    public function show(Agency $agency): JsonResponse
    {
        $agency->loadCount(['subAgencies', 'elements'])
            ->load(['subAgencies' => fn ($query) => $query->orderBy('id')]);

        return $this->successResponse(AgencyResource::make($agency), 'تم جلب البيانات بنجاح');
    }

    /**
     * تعديل جهة
     */
    public function update(UpdateAgencyRequest $request, Agency $agency, UpdateAgencyAction $updateAgency): JsonResponse
    {
        $agency = $updateAgency->execute($agency, $request->validated());

        return $this->successResponse(AgencyResource::make($agency), 'تم تعديل الجهة بنجاح');
    }

    /**
     * حذف جهة
     *
     * حذف ناعم للجهة مع جهاتها الفرعية. يُرفض (422) إذا كانت مرتبطة بعناصر.
     */
    public function destroy(Agency $agency, DeleteAgencyAction $deleteAgency): JsonResponse
    {
        $deleteAgency->execute($agency);

        return $this->successResponse(message: 'تم حذف الجهة بنجاح');
    }
}
