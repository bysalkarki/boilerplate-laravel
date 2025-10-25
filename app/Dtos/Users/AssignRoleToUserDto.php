<?php

declare(strict_types=1);

namespace App\Dtos\Users;

use App\Models\User;
use Spatie\Permission\Models\Role;

final class AssignRoleToUserDto
{
    public function __construct(
        public User $user,
        public Role $role,
    ) {}
}
