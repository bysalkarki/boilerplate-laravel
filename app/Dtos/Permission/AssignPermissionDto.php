<?php

declare(strict_types=1);

namespace App\Dtos\Permission;

use App\Models\Role;

final class AssignPermissionDto
{
    public function __construct(public Role $role, public array $permissions) {}
}
