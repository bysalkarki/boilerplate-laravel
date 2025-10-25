<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;

final class GetRoleById
{
    public function execute(int $id): ?Role
    {
        return Role::query()->find($id);
    }
}
