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

test('guests cannot create a user', function () {
    Auth::logout();

    $this->post(route('users.store'))
        ->assertRedirect(route('login'));
});

test('authenticated users without create-user permission cannot create a user', function () {
    $this->post(route('users.store'))
        ->assertForbidden();
});

test('authenticated users with create-user permission can create a user with a role', function () {
    Permission::create(['name' => 'create-user']);
    $role = Role::create(['name' => 'test-role']);
    $this->user->givePermissionTo('create-user');

    $this->post(route('users.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role_id' => $role->id,
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $user = User::where('email', 'test@example.com')->first();
    $this->assertTrue($user->hasRole('test-role'));
});
