<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Dtos\Roles\AssignPermissionsToRoleDto;
use App\Models\Role;

final class AssignPermissionsToRole
{
    public function execute(AssignPermissionsToRoleDto $data): void
    {
        $role = Role::query()->findOrFail($data->roleId);
        $role->syncPermissions($data->permissionIds);
    }
}
