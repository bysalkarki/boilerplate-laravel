<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Dtos\Users\UpdateUserPasswordDto;
use App\Models\User;

final class UpdateUserPassword
{
    public function execute(User $user, UpdateUserPasswordDto $updateUserPasswordDto): User
    {
        $user->update([
            'password' => bcrypt($updateUserPasswordDto->password),
        ]);

        return $user;
    }
}
