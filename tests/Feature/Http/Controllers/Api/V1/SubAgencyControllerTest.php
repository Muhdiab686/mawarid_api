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

class SubAgencyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->seniorAdmin()->create());
    }

    public function test_forbids_role_without_permission_with_403(): void
    {
        Sanctum::actingAs(User::factory()->role(UserRole::SuperAdmin)->create());

        $this->deleteJson(route('v1.sub-agencies.destroy', SubAgency::factory()->create()))->assertForbidden();
    }

    public function test_lists_only_sub_agencies_of_the_given_agency(): void
    {
        $agency = Agency::factory()->create();
        $subAgency = SubAgency::factory()->for($agency)->create();
        SubAgency::factory()->create();

        $this->getJson(route('v1.agencies.sub-agencies.index', $agency))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $subAgency->id);
    }

    public function test_creates_sub_agency_under_agency_and_returns_201(): void
    {
        $agency = Agency::factory()->create();

        $this->postJson(route('v1.agencies.sub-agencies.store', $agency), ['name' => 'الديوان'])
            ->assertCreated()
            ->assertJsonPath('data.agency_id', $agency->id);

        $this->assertDatabaseHas('sub_agencies', ['agency_id' => $agency->id, 'name' => 'الديوان']);
    }

    public function test_rejects_duplicate_name_within_the_same_agency_with_422(): void
    {
        $subAgency = SubAgency::factory()->create(['name' => 'الديوان']);

        $this->postJson(route('v1.agencies.sub-agencies.store', $subAgency->agency_id), ['name' => 'الديوان'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name' => 'The name has already been taken.']);
    }

    public function test_allows_the_same_name_in_a_different_agency(): void
    {
        SubAgency::factory()->create(['name' => 'الديوان']);

        $this->postJson(route('v1.agencies.sub-agencies.store', Agency::factory()->create()), ['name' => 'الديوان'])
            ->assertCreated();
    }

    public function test_moving_to_another_agency_moves_its_elements(): void
    {
        $subAgency = SubAgency::factory()->create();
        $element = Element::factory()->create(['agency_id' => $subAgency->agency_id, 'sub_agency_id' => $subAgency->id]);
        $targetAgency = Agency::factory()->create();

        $this->patchJson(route('v1.sub-agencies.update', $subAgency), ['agency_id' => $targetAgency->id])
            ->assertOk()
            ->assertJsonPath('data.agency_id', $targetAgency->id);

        $this->assertSame($targetAgency->id, $element->fresh()->agency_id);
    }

    public function test_rejects_moving_into_agency_that_has_the_same_name_with_422(): void
    {
        $subAgency = SubAgency::factory()->create(['name' => 'الديوان']);
        $targetAgency = Agency::factory()->create();
        SubAgency::factory()->for($targetAgency)->create(['name' => 'الديوان']);

        $this->patchJson(route('v1.sub-agencies.update', $subAgency), ['agency_id' => $targetAgency->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_deletes_sub_agency(): void
    {
        $subAgency = SubAgency::factory()->create();

        $this->deleteJson(route('v1.sub-agencies.destroy', $subAgency))->assertNoContent();

        $this->assertSoftDeleted($subAgency);
    }

    public function test_rejects_deleting_sub_agency_with_elements_with_422(): void
    {
        $subAgency = SubAgency::factory()->create();
        Element::factory()->create(['sub_agency_id' => $subAgency->id]);

        $this->deleteJson(route('v1.sub-agencies.destroy', $subAgency))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sub_agency' => 'لا يمكن حذف الجهة الفرعية لوجود عناصر مرتبطة بها.']);

        $this->assertNotSoftDeleted($subAgency);
    }
}
