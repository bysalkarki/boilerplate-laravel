<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Dtos\Users\DeleteUserDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

final class DeleteUser
{
    public function execute(DeleteUserDto $data): void
    {
        $user = User::query()->find($data->user->id);

        if (! $user) {
            throw new InvalidArgumentException('User not found.');
        }

        if (Auth::id() === $user->id) {
            throw new InvalidArgumentException('Cannot delete the currently authenticated user.');
        }

        if ($user->hasRole('super-admin')) {
            throw new InvalidArgumentException('Cannot delete a super-admin user.');
        }

        $user->delete();
    }
}
