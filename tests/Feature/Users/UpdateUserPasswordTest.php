<?php

declare(strict_types=1);

use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests cannot update a user\'s password', function () {
    Auth::logout();
    $user = User::factory()->create();

    $this->put(route('users.update-password', $user))
        ->assertRedirect(route('login'));
});

test('authenticated users without update-user permission cannot update a user\'s password', function () {
    $user = User::factory()->create();

    $this->put(route('users.update-password', $user))
        ->assertForbidden();
});

test('authenticated users with update-user permission can update a user\'s password', function () {
    $this->withoutMiddleware("App\Http\Middleware\VerifyCsrfToken::class");
    Permission::create(['name' => 'update-user']);
    $this->user->givePermissionTo('update-user');

    $userToUpdate = User::factory()->create();

    $this->put(route('users.update-password', $userToUpdate), [
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])
        ->assertRedirect(route('users.edit', $userToUpdate))
        ->assertSessionHasNoErrors();

    $updatedUser = User::find($userToUpdate->id);
    expect(Hash::check('new-password', $updatedUser->password))->toBeTrue();
});
