<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Dtos\Roles\CreateRoleDto;
use App\Models\Role;

final class CreateRole
{
    public function execute(CreateRoleDto $data): Role
    {
        return Role::create([
            'name' => $data->name,
        ]);
    }
}
