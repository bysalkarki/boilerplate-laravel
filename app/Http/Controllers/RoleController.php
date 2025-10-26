<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Roles\AssignPermissionsToRole;
use App\Actions\Roles\CreateRole;
use App\Actions\Roles\DeleteRole;
use App\Actions\Roles\GetAllRoles;
use App\Actions\Roles\UpdateRole;
use App\Dtos\Roles\AssignPermissionsToRoleDto;
use App\Dtos\Roles\CreateRoleDto;
use App\Dtos\Roles\DeleteRoleDto;
use App\Dtos\Roles\UpdateRoleDto;
use App\Http\Requests\Roles\AssignPermissionsToRoleRequest;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;

final class RoleController extends Controller
{
    public function index(Request $request, GetAllRoles $getAllRoles): Response
    {
        return Inertia::render('Roles/Index', [
            'roles' => $getAllRoles->execute(
                $request->query('search'),
                (int) $request->query('per_page', 15),
            ),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }

    public function store(StoreRoleRequest $request, CreateRole $createRole): RedirectResponse
    {
        $createRole->execute(new CreateRoleDto($request->validated('name')));

        return redirect()->route('roles.index');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => $role,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRole $updateRole): RedirectResponse
    {
        $updateRole->execute(new UpdateRoleDto($role->id, $request->validated('name')));
        session()->flash('success', 'Role updated successfully.');

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role, DeleteRole $deleteRole): RedirectResponse
    {
        try {
            $deleteRole->execute(new DeleteRoleDto($role->id));
            session()->flash('success', 'Role deleted successfully.');
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }

        return redirect()->route('roles.index');
    }

    public function assignPermissions(Role $role): Response
    {
        $allPermissions = Permission::all()->groupBy('module');
        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Roles/AssignPermissions', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'assignedPermissions' => $assignedPermissions,
        ]);
    }

    public function storePermissions(AssignPermissionsToRoleRequest $request, Role $role, AssignPermissionsToRole $assignPermissionsToRole): RedirectResponse
    {
        $assignPermissionsToRole->execute(new AssignPermissionsToRoleDto(
            $role->id,
            $request->validated('permission_ids', []),
        ));

        session()->flash('success', 'Permissions assigned successfully.');

        return redirect()->route('roles.index');
    }
}
