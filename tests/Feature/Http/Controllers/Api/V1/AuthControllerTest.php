<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_with_role_and_permissions(): void
    {
        User::factory()->seniorAdmin()->create(['username' => 'admin']);

        $response = $this->postJson(route('v1.auth.login'), ['username' => 'admin', 'password' => 'password']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'تم تسجيل الدخول بنجاح')
            ->assertJsonPath('data.user.username', 'admin')
            ->assertJsonPath('data.user.role.value', 'senior_admin')
            ->assertJsonPath('data.user.role.label', 'إدارة عليا')
            ->assertJsonPath('data.user.permissions', array_column(Permission::cases(), 'value'));
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_rejects_wrong_password_with_422(): void
    {
        User::factory()->create(['username' => 'admin']);

        $this->postJson(route('v1.auth.login'), ['username' => 'admin', 'password' => 'wrong'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['username' => 'اسم المستخدم أو كلمة المرور غير صحيحة.']);
    }

    public function test_login_rejects_inactive_user_with_422(): void
    {
        User::factory()->seniorAdmin()->inactive()->create(['username' => 'admin']);

        $this->postJson(route('v1.auth.login'), ['username' => 'admin', 'password' => 'password'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['username' => 'اسم المستخدم أو كلمة المرور غير صحيحة.']);
    }

    public function test_returns_401_when_no_token_is_provided(): void
    {
        $this->getJson(route('v1.auth.me'))->assertUnauthorized();
    }

    public function test_me_returns_the_authenticated_user_permissions(): void
    {
        $user = User::factory()->seniorAdmin()->create();

        $this->withToken($user->createToken('api')->plainTextToken)
            ->getJson(route('v1.auth.me'))
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.permissions', array_column(Permission::cases(), 'value'));
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->seniorAdmin()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)->postJson(route('v1.auth.logout'))
            ->assertOk()
            ->assertJsonPath('message', 'تم تسجيل الخروج بنجاح');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
