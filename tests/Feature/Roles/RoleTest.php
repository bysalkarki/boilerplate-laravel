<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed permissions and roles
    $this->seed(Database\Seeders\PermissionSeeder::class);

    // Create an admin user and log them in
    $this->adminUser = User::factory()->create();
    $this->adminUser->assignRole('admin');
    $this->actingAs($this->adminUser);
});

test('guests are redirected to the login page', function () {
    Auth::logout(); // Ensure no user is logged in
    $this->get(route('roles.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the roles index page', function () {
    $this->get(route('roles.index'))->assertOk();
});

test('authenticated users can view the create role page', function () {
    $this->get(route('roles.create'))->assertOk();
});

test('authenticated users can create a role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->post(route('roles.store'), ['name' => 'New Role'])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('roles', ['name' => 'New Role']);
});

test('authenticated users cannot create a role with an empty name', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $this->post(route('roles.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseMissing('roles', ['name' => '']);
});

test('authenticated users cannot create a role with a duplicate name', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    Role::create(['name' => 'Existing Role']);

    $this->post(route('roles.store'), ['name' => 'Existing Role'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('roles', 3); // super-admin, admin, Existing Role
});

test('authenticated users can view the edit role page', function () {
    $role = Role::create(['name' => 'Editable Role']);

    $this->get(route('roles.edit', $role))
        ->assertOk()
        ->assertSee('Editable Role');
});

test('authenticated users can update a role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $role = Role::create(['name' => 'Old Role Name']);

    $this->put(route('roles.update', $role), ['name' => 'Updated Role Name'])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('roles', ['name' => 'Updated Role Name']);
    $this->assertDatabaseMissing('roles', ['name' => 'Old Role Name']);
});

test('authenticated users cannot update a role with an empty name', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $role = Role::create(['name' => 'Role to Update']);

    $this->put(route('roles.update', $role), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('roles', ['name' => 'Role to Update']);
});

test('authenticated users cannot update a role with a duplicate name', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    Role::create(['name' => 'Another Role']);
    $role = Role::create(['name' => 'Role to Be Updated']);

    $this->put(route('roles.update', $role), ['name' => 'Another Role'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('roles', ['name' => 'Role to Be Updated']);
    $this->assertDatabaseHas('roles', ['name' => 'Another Role']);
});

test('authenticated users can delete a role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $role = Role::create(['name' => 'Deletable Role']);

    $this->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('roles', ['name' => 'Deletable Role']);
});

test('authenticated users cannot delete a default role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $superAdminRole = Role::where('name', 'super-admin')->first();

    $this->delete(route('roles.destroy', $superAdminRole))
        ->assertSessionHas('error'); // The controller should catch the InvalidArgumentException and add an error to the session

    $this->assertDatabaseHas('roles', ['name' => 'super-admin']);
});

// Permission Tests

test('a basic user cannot access roles index', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('roles.index'))->assertForbidden();
});

test('users without read-role permission cannot visit the roles index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('roles.index'))->assertForbidden();
});

test('users without create-role permission cannot view the create role page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('roles.create'))->assertForbidden();
});

test('users without create-role permission cannot create a role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('roles.store'), ['name' => 'Unauthorized Role'])
        ->assertForbidden();

    $this->assertDatabaseMissing('roles', ['name' => 'Unauthorized Role']);
});

test('users without update-role permission cannot view the edit role page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $role = Role::create(['name' => 'Role to Edit']);

    $this->get(route('roles.edit', $role))->assertForbidden();
});

test('users without update-role permission cannot update a role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();
    $this->actingAs($user);
    $role = Role::create(['name' => 'Role to Update']);

    $this->put(route('roles.update', $role), ['name' => 'Attempted Update'])
        ->assertForbidden();

    $this->assertDatabaseHas('roles', ['name' => 'Role to Update']);
    $this->assertDatabaseMissing('roles', ['name' => 'Attempted Update']);
});

test('users without delete-role permission cannot delete a role', function () {
    $this->withoutMiddleware(App\Http\Middleware\VerifyCsrfToken::class);
    $user = User::factory()->create();
    $this->actingAs($user);
    $role = Role::create(['name' => 'Role to Delete']);

    $this->delete(route('roles.destroy', $role))
        ->assertForbidden();

    $this->assertDatabaseHas('roles', ['name' => 'Role to Delete']);
});
