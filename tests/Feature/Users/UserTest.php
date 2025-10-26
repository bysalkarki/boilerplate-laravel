<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    // Seed permissions and roles
    $this->seed(Database\Seeders\PermissionSeeder::class);

    // Create an admin user and log them in
    $this->adminUser = User::factory()->create();
    $this->adminUser->assignRole('admin');
    $this->actingAs($this->adminUser);
});

test('guests are redirected to the login page', function () {
    Auth::logout(); // Ensure no user is logged in
    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the users index page', function () {
    $this->get(route('users.index'))->assertOk();
});

test('authenticated users can view the create user page', function () {
    $this->get(route('users.create'))->assertOk();
});

test('authenticated users can create a user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role_id' => Role::factory()->create()->id,
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
    ]);
});

test('authenticated users cannot create a user with invalid data', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->post(route('users.store'), [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'password',
        'password_confirmation' => 'wrong',
    ])
        ->assertSessionHasErrors(['name', 'email', 'password']);

    $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
});

test('authenticated users cannot create a user with a duplicate email', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    User::factory()->create(['email' => 'existing@example.com']);

    $this->post(route('users.store'), [
        'name' => 'Another User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertSessionHasErrors('email');

    $this->assertDatabaseCount('users', 2); // adminUser and existing@example.com
});

test('authenticated users can view the edit user page', function () {
    $user = User::factory()->create();

    $this->get(route('users.edit', $user))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->email);
});

test('authenticated users can update a user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => '',
        'password_confirmation' => '',
    ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
    $this->assertDatabaseMissing('users', ['email' => 'old@example.com']);
});

test('authenticated users cannot update a user with invalid data', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();

    $this->put(route('users.update', $user), [
        'name' => '',
        'email' => 'invalid-email',
    ])
        ->assertSessionHasErrors(['name', 'email']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email]);
});

test('authenticated users cannot update a user with a duplicate email', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    User::factory()->create(['email' => 'another@example.com']);
    $user = User::factory()->create(['email' => 'user-to-update@example.com']);

    $this->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => 'another@example.com',
        'password' => '',
        'password_confirmation' => '',
    ])
        ->assertSessionHasErrors('email');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'user-to-update@example.com']);
});

test('authenticated users can delete a user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();

    $this->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('authenticated users cannot delete the currently authenticated user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->delete(route('users.destroy', $this->adminUser))
        ->assertSessionHas('error'); // Assuming the controller catches the exception and adds an error to the session

    $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
});

test('authenticated users cannot delete a super-admin user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $superAdminRole = Role::where('name', 'super-admin')->first();
    $superAdminUser = User::factory()->create();
    $superAdminUser->assignRole($superAdminRole);

    $this->delete(route('users.destroy', $superAdminUser))
        ->assertSessionHas('error'); // Assuming the controller catches the exception and adds an error to the session

    $this->assertDatabaseHas('users', ['id' => $superAdminUser->id]);
});

// Permission Tests

test('users without read-user permission cannot visit the users index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.index'))->assertForbidden();
});

test('users without create-user permission cannot view the create user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.create'))->assertForbidden();
});

test('users without create-user permission cannot create a user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('users.store'), [
        'name' => 'Unauthorized User',
        'email' => 'unauthorized@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'unauthorized@example.com']);
});

test('users without update-user permission cannot view the edit user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $userToEdit = User::factory()->create();

    $this->get(route('users.edit', $userToEdit))->assertForbidden();
});

test('users without update-user permission cannot update a user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();
    $this->actingAs($user);
    $userToUpdate = User::factory()->create();

    $this->put(route('users.update', $userToUpdate), [
        'name' => 'Attempted Update',
        'email' => $userToUpdate->email,
        'password' => '',
        'password_confirmation' => '',
    ])
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['name' => $userToUpdate->name]);
    $this->assertDatabaseMissing('users', ['name' => 'Attempted Update']);
});

test('users without delete-user permission cannot delete a user', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();
    $this->actingAs($user);
    $userToDelete = User::factory()->create();

    $this->delete(route('users.destroy', $userToDelete))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);
});
