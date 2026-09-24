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
use App\Models\Agency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * @tags الجهات
 */
class AgencyController extends Controller implements HasMiddleware
{
    /**
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.Permission::ViewAgencies->value, only: ['index', 'show']),
            new Middleware('can:'.Permission::CreateAgencies->value, only: ['store']),
            new Middleware('can:'.Permission::UpdateAgencies->value, only: ['update']),
            new Middleware('can:'.Permission::DeleteAgencies->value, only: ['destroy']),
        ];
    }

    /**
     * قائمة الجهات
     */
    public function index(PaginatedIndexRequest $request): AnonymousResourceCollection
    {
        $agencies = Agency::query()
            ->withCount(['subAgencies', 'elements'])
            ->when($request->search(), fn (Builder $query, string $search) => $query->whereLike('name', "%{$search}%"))
            ->orderBy('id')
            ->paginate($request->perPage());

        return AgencyResource::collection($agencies);
    }

    /**
     * إضافة جهة
     */
    public function store(StoreAgencyRequest $request, CreateAgencyAction $createAgency): AgencyResource
    {
        return AgencyResource::make($createAgency->execute($request->validated()));
    }

    /**
     * عرض جهة
     *
     * مع جهاتها الفرعية.
     */
    public function show(Agency $agency): AgencyResource
    {
        $agency->loadCount(['subAgencies', 'elements'])
            ->load(['subAgencies' => fn ($query) => $query->orderBy('id')]);

        return AgencyResource::make($agency);
    }

    /**
     * تعديل جهة
     */
    public function update(UpdateAgencyRequest $request, Agency $agency, UpdateAgencyAction $updateAgency): AgencyResource
    {
        return AgencyResource::make($updateAgency->execute($agency, $request->validated()));
    }

    /**
     * حذف جهة
     *
     * حذف ناعم للجهة مع جهاتها الفرعية. يُرفض (422) إذا كانت مرتبطة بعناصر.
     */
    public function destroy(Agency $agency, DeleteAgencyAction $deleteAgency): Response
    {
        $deleteAgency->execute($agency);

        return response()->noContent();
    }
}
