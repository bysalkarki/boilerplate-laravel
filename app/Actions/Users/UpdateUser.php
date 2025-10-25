<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Dtos\Users\UpdateUserDto;
use App\Models\User;

final class UpdateUser
{
    public function execute(User $user, UpdateUserDto $updateUserDto): User
    {
        $user->update([
            'name' => $updateUserDto->name,
            'email' => $updateUserDto->email,
        ]);

        if ($updateUserDto->password) {
            $user->update([
                'password' => bcrypt($updateUserDto->password),
            ]);
        }

        return $user;
    }
}
