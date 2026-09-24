<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Models\Agency;
use App\Models\Element;
use App\Models\SubAgency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AgencyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->seniorAdmin()->create());
    }

    public function test_forbids_role_without_permission_with_403(): void
    {
        Sanctum::actingAs(User::factory()->role(UserRole::AgencyAdmin)->create());

        $this->getJson(route('v1.agencies.index'))->assertForbidden();
    }

    public function test_forbids_inactive_senior_admin_with_403(): void
    {
        Sanctum::actingAs(User::factory()->seniorAdmin()->inactive()->create());

        $this->postJson(route('v1.agencies.store'), ['name' => 'مركزي'])->assertForbidden();
    }

    public function test_lists_agencies_with_counts_filtered_by_name(): void
    {
        $matchingAgency = Agency::factory()->create(['name' => 'مديرية أمن درعا']);
        SubAgency::factory()->count(2)->for($matchingAgency)->create();
        Element::factory()->for($matchingAgency)->create();
        Agency::factory()->create(['name' => 'فرع المرور']);

        $this->getJson(route('v1.agencies.index', ['search' => 'درعا']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingAgency->id)
            ->assertJsonPath('data.0.sub_agencies_count', 2)
            ->assertJsonPath('data.0.elements_count', 1)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_creates_agency_and_returns_201(): void
    {
        $this->postJson(route('v1.agencies.store'), ['name' => 'مركزي'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'مركزي');

        $this->assertDatabaseHas('agencies', ['name' => 'مركزي']);
    }

    public function test_rejects_duplicate_agency_name_with_422(): void
    {
        Agency::factory()->create(['name' => 'مركزي']);

        $this->postJson(route('v1.agencies.store'), ['name' => 'مركزي'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name has already been taken.']);
    }

    public function test_allows_reusing_name_of_deleted_agency(): void
    {
        Agency::factory()->create(['name' => 'مركزي'])->delete();

        $this->postJson(route('v1.agencies.store'), ['name' => 'مركزي'])->assertCreated();
    }

    public function test_shows_agency_with_its_sub_agencies(): void
    {
        $agency = Agency::factory()->create();
        $subAgency = SubAgency::factory()->for($agency)->create();

        $this->getJson(route('v1.agencies.show', $agency))
            ->assertOk()
            ->assertJsonPath('data.id', $agency->id)
            ->assertJsonPath('data.sub_agencies.0.id', $subAgency->id);
    }

    public function test_updates_agency_name(): void
    {
        $agency = Agency::factory()->create(['name' => 'فرع التسليح والمهامات']);

        $this->patchJson(route('v1.agencies.update', $agency), ['name' => 'فرع التسليح والمهمات'])
            ->assertOk()
            ->assertJsonPath('data.name', 'فرع التسليح والمهمات');

        $this->assertSame('فرع التسليح والمهمات', $agency->fresh()->name);
    }

    public function test_deletes_agency_together_with_its_sub_agencies(): void
    {
        $agency = Agency::factory()->create();
        $subAgency = SubAgency::factory()->for($agency)->create();

        $this->deleteJson(route('v1.agencies.destroy', $agency))->assertNoContent();

        $this->assertSoftDeleted($agency);
        $this->assertSoftDeleted($subAgency);
    }

    public function test_rejects_deleting_agency_with_elements_with_422(): void
    {
        $agency = Agency::factory()->create();
        Element::factory()->for($agency)->create();

        $this->deleteJson(route('v1.agencies.destroy', $agency))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['agency' => 'لا يمكن حذف الجهة لوجود عناصر مرتبطة بها أو بجهاتها الفرعية.']);

        $this->assertNotSoftDeleted($agency);
    }

    public function test_rejects_deleting_agency_whose_sub_agency_has_elements_with_422(): void
    {
        $agency = Agency::factory()->create();
        $subAgency = SubAgency::factory()->for($agency)->create();
        Element::factory()->create(['sub_agency_id' => $subAgency->id]);

        $this->deleteJson(route('v1.agencies.destroy', $agency))->assertUnprocessable();

        $this->assertNotSoftDeleted($subAgency);
    }
}
