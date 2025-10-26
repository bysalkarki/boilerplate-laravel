<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests cannot update a user', function () {
    Auth::logout();
    $user = User::factory()->create();

    $this->put(route('users.update', $user))
        ->assertRedirect(route('login'));
});

test('authenticated users without update-user permission cannot update a user', function () {
    $user = User::factory()->create();

    $this->put(route('users.update', $user))
        ->assertForbidden();
});

test('authenticated users with update-user permission can update a user\'s role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    Permission::create(['name' => 'update-user']);
    $role1 = Role::create(['name' => 'role1']);
    $role2 = Role::create(['name' => 'role2']);
    $this->user->givePermissionTo('update-user');

    $userToUpdate = User::factory()->create();
    $userToUpdate->assignRole($role1);

    $this->put(route('users.update', $userToUpdate), [
        'name' => 'Updated User',
        'email' => 'updated@example.com',
        'role_id' => $role2->id,
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'Updated User',
        'email' => 'updated@example.com',
    ]);

    $updatedUser = User::find($userToUpdate->id);
    $this->assertTrue($updatedUser->hasRole('role2'));
    $this->assertFalse($updatedUser->hasRole('role1'));
});
