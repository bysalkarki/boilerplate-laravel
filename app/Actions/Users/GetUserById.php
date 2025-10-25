<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final class GetUserById
{
    public function execute(int $id): ?User
    {
        return User::query()->find($id);
    }
}
