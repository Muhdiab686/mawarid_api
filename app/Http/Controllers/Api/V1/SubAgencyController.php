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
use App\Http\Resources\SubAgencyResource;
use App\Models\Agency;
use App\Models\SubAgency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * @tags الجهات الفرعية
 */
class SubAgencyController extends Controller implements HasMiddleware
{
    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.Permission::ViewSubAgencies->value, only: ['index', 'show']),
            new Middleware('can:'.Permission::CreateSubAgencies->value, only: ['store']),
            new Middleware('can:'.Permission::UpdateSubAgencies->value, only: ['update']),
            new Middleware('can:'.Permission::DeleteSubAgencies->value, only: ['destroy']),
        ];
    }

    /**
     * الجهات الفرعية لجهة
     */
    public function index(PaginatedIndexRequest $request, Agency $agency): AnonymousResourceCollection
    {
        $subAgencies = $agency->subAgencies()
            ->withCount('elements')
            ->when($request->search(), fn (Builder $query, string $search) => $query->whereLike('name', "%{$search}%"))
            ->orderBy('id')
            ->paginate($request->perPage());

        return SubAgencyResource::collection($subAgencies);
    }

    /**
     * إضافة جهة فرعية
     */
    public function store(StoreSubAgencyRequest $request, Agency $agency, CreateSubAgencyAction $createSubAgency): SubAgencyResource
    {
        return SubAgencyResource::make($createSubAgency->execute($agency, $request->validated()));
    }

    /**
     * عرض جهة فرعية
     *
     * مع جهتها الرئيسية.
     */
    public function show(SubAgency $subAgency): SubAgencyResource
    {
        return SubAgencyResource::make($subAgency->loadCount('elements')->load('agency'));
    }

    /**
     * تعديل جهة فرعية
     *
     * يمكن نقلها لجهة رئيسية أخرى بإرسال `agency_id`، وتنتقل عناصرها معها.
     */
    public function update(UpdateSubAgencyRequest $request, SubAgency $subAgency, UpdateSubAgencyAction $updateSubAgency): SubAgencyResource
    {
        return SubAgencyResource::make($updateSubAgency->execute($subAgency, $request->validated()));
    }

    /**
     * حذف جهة فرعية
     *
     * حذف ناعم. يُرفض (422) إذا كانت مرتبطة بعناصر.
     */
    public function destroy(SubAgency $subAgency, DeleteSubAgencyAction $deleteSubAgency): Response
    {
        $deleteSubAgency->execute($subAgency);

        return response()->noContent();
    }
}
