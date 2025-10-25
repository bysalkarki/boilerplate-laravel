<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Dtos\Users\AssignRoleToUserDto;

final class AssignRoleToUser
{
    public function execute(AssignRoleToUserDto $dto): void
    {
        $dto->user->assignRole($dto->role);
    }
}
