<?php

declare(strict_types=1);

namespace App\Dtos\Roles;

final readonly class AssignPermissionsToRoleDto
{
    public function __construct(
        public int $roleId,
        public array $permissionIds,
    ) {}
}
