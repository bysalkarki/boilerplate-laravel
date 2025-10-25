<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Dtos\Roles\UpdateRoleDto;
use App\Models\Role;

final class UpdateRole
{
    public function execute(UpdateRoleDto $data): Role
    {
        $role = Role::query()->findOrFail($data->id);
        $role->update([
            'name' => $data->name,
        ]);

        return $role->fresh();
    }
}
