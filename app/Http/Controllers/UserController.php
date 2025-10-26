<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Users\CreateUser;
use App\Actions\Users\DeleteUser;
use App\Actions\Users\GetAllUsers;
use App\Actions\Users\UpdateUser;
use App\Actions\Users\UpdateUserPassword;
use App\Dtos\Users\CreateUserDto;
use App\Dtos\Users\DeleteUserDto;
use App\Dtos\Users\UpdateUserDto;
use App\Dtos\Users\UpdateUserPasswordDto;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserPasswordRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

final class UserController extends Controller
{
    public function index(GetAllUsers $getAllUsers, Request $request): Response
    {
        return Inertia::render('Users/Index', [
            'users' => $getAllUsers->execute(
                $request->query('search'),
                (int) $request->per_page,
            ),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create', [
            'roles' => \App\Models\Role::all(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUser $createUser): RedirectResponse
    {
        $createUser->execute(new CreateUserDto(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
            $request->validated('role_id'),
        ));

        return redirect()->route('users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => \App\Models\Role::all(['id', 'name']),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser): RedirectResponse
    {
        $updateUser->execute($user, new UpdateUserDto(
            $request->validated('name'),
            $request->validated('email'),
            (int) $request->validated('role_id'),
        ));
        session()->flash('success', 'User updated successfully.');

        return redirect()->route('users.index');
    }

    public function destroy(User $user, DeleteUser $deleteUser): RedirectResponse
    {
        try {
            $deleteUser->execute(new DeleteUserDto($user));
            session()->flash('success', 'User deleted successfully.');
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }

        return redirect()->route('users.index');
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user, UpdateUserPassword $updateUserPassword): RedirectResponse
    {
        $updateUserPassword->execute($user, new UpdateUserPasswordDto(
            $request->validated('password')
        ));

        session()->flash('success', 'User password updated successfully.');

        return redirect()->route('users.edit', $user);
    }
}
