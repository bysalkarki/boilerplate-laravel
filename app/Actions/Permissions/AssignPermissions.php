<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Dtos\Permission\AssignPermissionDto;

final class AssignPermissions
{
    public function execute(AssignPermissionDto $data): void
    {
        $data->role->givePermissionTo($data->permissions);
    }
}
