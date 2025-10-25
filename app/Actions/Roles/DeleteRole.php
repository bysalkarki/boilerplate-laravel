<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Dtos\Roles\DeleteRoleDto;
use App\Models\Role;
use InvalidArgumentException;

final class DeleteRole
{
    public function execute(DeleteRoleDto $data): void
    {
        $role = Role::query()->find($data->id);

        if (! $role) {
            throw new InvalidArgumentException('Role not found.');
        }

        // Prevent deletion of default roles
        if ($role->default) {
            throw new InvalidArgumentException('Cannot delete default roles.');
        }

        $role->delete();
    }
}
