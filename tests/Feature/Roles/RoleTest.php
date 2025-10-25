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
    Auth::logout();
    $this->get(route('roles.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the roles index page', function () {
    $this->get(route('roles.index'))->assertOk();
});

test('authenticated users can view the create role page', function () {
    $this->get(route('roles.create'))->assertOk();
});

test('authenticated users can create a role', function () {
    $this->post(route('roles.store'), ['name' => 'New Role'])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('roles', ['name' => 'New Role']);
});

test('authenticated users cannot create a role with an empty name', function () {
    $this->post(route('roles.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseMissing('roles', ['name' => '']);
});

test('authenticated users cannot create a role with a duplicate name', function () {
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
    $role = Role::create(['name' => 'Old Role Name']);

    $this->put(route('roles.update', $role), ['name' => 'Updated Role Name'])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('roles', ['name' => 'Updated Role Name']);
    $this->assertDatabaseMissing('roles', ['name' => 'Old Role Name']);
});

test('authenticated users cannot update a role with an empty name', function () {
    $role = Role::create(['name' => 'Role to Update']);

    $this->put(route('roles.update', $role), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('roles', ['name' => 'Role to Update']);
});

test('authenticated users cannot update a role with a duplicate name', function () {
    Role::create(['name' => 'Another Role']);
    $role = Role::create(['name' => 'Role to Be Updated']);

    $this->put(route('roles.update', $role), ['name' => 'Another Role'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('roles', ['name' => 'Role to Be Updated']);
    $this->assertDatabaseHas('roles', ['name' => 'Another Role']);
});

test('authenticated users can delete a role', function () {
    $role = Role::create(['name' => 'Deletable Role']);

    $this->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('roles', ['name' => 'Deletable Role']);
});

test('authenticated users cannot delete a default role', function () {
    $superAdminRole = Role::where('name', 'super-admin')->first();

    $this->delete(route('roles.destroy', $superAdminRole))
        ->assertSessionHas('error'); // The controller should catch the InvalidArgumentException and add an error to the session

    $this->assertDatabaseHas('roles', ['name' => 'super-admin']);
});

test('authenticated users can search role by values', function () {
    $user = User::factory()->create();

    Role::factory()->create(['name' => 'editor']);

    $response = $this->actingAs($user)
        ->get(route('roles.index', ['search' => 'editor']));

    $response->assertStatus(200);

    $response->assertSee('editor');
    $response->assertDontSee('admin');
});
