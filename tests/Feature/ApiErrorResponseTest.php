<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use RuntimeException;
use Tests\TestCase;

/**
 * The unified API error shape rendered in bootstrap/app.php.
 */
class ApiErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_401_in_unified_shape_without_token(): void
    {
        $this->getJson(route('v1.auth.me'))
            ->assertUnauthorized()
            ->assertExactJson(['success' => false, 'message' => 'غير مصرح لك بالوصول، يرجى تسجيل الدخول']);
    }

    public function test_returns_403_not_500_for_missing_permission(): void
    {
        Sanctum::actingAs(User::factory()->role(UserRole::AgencyAdmin)->create());

        $this->getJson(route('v1.agencies.index'))
            ->assertForbidden()
            ->assertExactJson(['success' => false, 'message' => 'ليس لديك صلاحية لتنفيذ هذا الإجراء']);
    }

    public function test_returns_404_in_unified_shape_for_missing_record(): void
    {
        Sanctum::actingAs(User::factory()->seniorAdmin()->create());

        $this->getJson(route('v1.agencies.show', 999))
            ->assertNotFound()
            ->assertExactJson(['success' => false, 'message' => 'المورد أو الرابط المطلوب غير موجود']);
    }

    public function test_returns_405_not_500_for_unsupported_method(): void
    {
        $this->putJson(route('v1.auth.login'))
            ->assertMethodNotAllowed()
            ->assertJsonPath('success', false);
    }

    public function test_returns_422_with_first_error_as_message_and_all_errors(): void
    {
        $this->postJson(route('v1.auth.login'), [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'The username field is required.')
            ->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_returns_429_not_500_when_login_is_throttled(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson(route('v1.auth.login'), ['username' => 'x', 'password' => 'y']);
        }

        $this->postJson(route('v1.auth.login'), ['username' => 'x', 'password' => 'y'])
            ->assertTooManyRequests()
            ->assertHeader('Retry-After')
            ->assertExactJson(['success' => false, 'message' => 'محاولات كثيرة، يرجى المحاولة لاحقاً']);
    }

    public function test_hides_unexpected_error_details_when_debug_is_off(): void
    {
        config(['app.debug' => false]);
        Route::get('api/test-unexpected-error', fn () => throw new RuntimeException('secret details'));

        $this->getJson('api/test-unexpected-error')
            ->assertInternalServerError()
            ->assertExactJson(['success' => false, 'message' => 'حدث خطأ داخلي في الخادم']);
    }
}
