<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Dtos\Users\RemoveRoleFromUserDto;

final class RemoveRoleFromUser
{
    public function execute(RemoveRoleFromUserDto $dto): void
    {
        $dto->user->removeRole($dto->role);
    }
}
