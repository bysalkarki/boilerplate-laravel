<?php

declare(strict_types=1);
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it can create role', function () {
    $data = new App\Dtos\Roles\CreateRoleDto('Laravel');
    $action = new App\Actions\Roles\CreateRole();
    $action->execute($data);

    $this->assertDatabaseHas('roles', ['name' => 'Laravel']);
});

test('it can update role', function () {
    $role = App\Models\Role::factory()->create();
    $data = new App\Dtos\Roles\UpdateRoleDto($role->id, 'chinaman');
    $action = new App\Actions\Roles\UpdateRole();
    $action->execute($data);
    $this->assertDatabaseHas('roles', ['name' => 'chinaman']);
});

test('it can delete role', function () {
    $role = App\Models\Role::factory()->create();

    $action = new App\Actions\Roles\DeleteRole();
    $data = new App\Dtos\Roles\DeleteRoleDto($role->id);
    $action->execute($data);

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

test('it can assign permissions to role', function () {
    $role = App\Models\Role::factory()->create();
    $permission = App\Models\Permission::factory()->create();

    $action = new App\Actions\Permissions\AssignPermissions();
    $data = new App\Dtos\Permission\AssignPermissionDto($role, [$permission->id]);
    $action->execute($data);
    $this->assertDatabaseHas('role_has_permissions', ['role_id' => $role->id, 'permission_id' => $permission->id]);
});
