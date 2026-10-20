<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Security\RolePolicy;
use PHPUnit\Framework\TestCase;

final class RolePolicyTest extends TestCase
{
    public function testAdminRolesHaveExpectedMutationPermissions(): void
    {
        $this->assertTrue(RolePolicy::allows('super_admin', 'settings.manage'));
        $this->assertTrue(RolePolicy::allows('admin', 'submissions.assign'));
        $this->assertTrue(RolePolicy::allows('editor', 'submissions.manage'));
        $this->assertFalse(RolePolicy::allows('editor', 'settings.manage'));
        $this->assertTrue(RolePolicy::allows('viewer', 'submissions.read'));
        $this->assertFalse(RolePolicy::allows('viewer', 'submissions.manage'));
        $this->assertFalse(RolePolicy::allows('unknown', 'dashboard'));
    }
}
