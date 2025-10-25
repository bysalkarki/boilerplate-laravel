<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Roles\CreateRole;
use App\Actions\Roles\DeleteRole;
use App\Actions\Roles\GetAllRoles;
use App\Actions\Roles\UpdateRole;
use App\Dtos\Roles\CreateRoleDto;
use App\Dtos\Roles\DeleteRoleDto;
use App\Dtos\Roles\UpdateRoleDto;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final class RoleController extends Controller
{
    public function index(GetAllRoles $getAllRoles): Response
    {
        $search = request()->query('search');
        $perPage = request()->query('perPage', 15);

        return Inertia::render('Roles/Index', [
            'roles' => $getAllRoles->execute($search, $perPage),
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
            session()->flash('success', $role->name.' deleted successfully.');
        } catch (Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }

        return redirect()->route('roles.index');
    }
}
