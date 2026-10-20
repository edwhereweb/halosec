<?php

declare(strict_types=1);

namespace HaloSec\Security;

final class RolePolicy
{
    public const ROLES = ['super_admin', 'admin', 'editor', 'viewer'];

    /** @var array<string, list<string>> */
    private const PERMISSIONS = [
        'super_admin' => ['dashboard', 'submissions.read', 'submissions.manage', 'submissions.assign', 'settings.manage', 'security.read', 'audit.read', 'users.manage'],
        'admin' => ['dashboard', 'submissions.read', 'submissions.manage', 'submissions.assign', 'settings.manage', 'security.read', 'audit.read'],
        'editor' => ['dashboard', 'submissions.read', 'submissions.manage', 'security.read'],
        'viewer' => ['dashboard', 'submissions.read', 'security.read'],
    ];

    public static function allows(string $role, string $permission): bool
    {
        return isset(self::PERMISSIONS[$role])
            && in_array($permission, self::PERMISSIONS[$role], true);
    }
}
