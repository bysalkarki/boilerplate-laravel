<?php

declare(strict_types=1);

namespace App\Dtos\Users;

use App\Models\User;

final class DeleteUserDto
{
    public function __construct(
        public User $user,
    ) {}
}
