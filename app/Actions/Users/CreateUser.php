<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Dtos\Users\CreateUserDto;
use App\Models\User;

final class CreateUser
{
    public function execute(CreateUserDto $data): User
    {
        $user = User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password),
        ]);

        $user->assignRole($data->roleId);

        return $user;
    }
}
