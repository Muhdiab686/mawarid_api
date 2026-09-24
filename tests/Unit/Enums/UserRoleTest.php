<?php

namespace Tests\Unit\Enums;

use App\Enums\Permission;
use App\Enums\UserRole;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_senior_admin_has_every_permission(): void
    {
        $this->assertSame(Permission::cases(), UserRole::SeniorAdmin->permissions());
    }

    #[DataProvider('rolesWithoutPermissionsYet')]
    public function test_other_roles_have_no_permissions_yet(UserRole $role): void
    {
        $this->assertSame([], $role->permissions());
    }

    /**
     * @return iterable<string, array{UserRole}>
     */
    public static function rolesWithoutPermissionsYet(): iterable
    {
        foreach (UserRole::cases() as $role) {
            if ($role !== UserRole::SeniorAdmin) {
                yield $role->value => [$role];
            }
        }
    }
}
