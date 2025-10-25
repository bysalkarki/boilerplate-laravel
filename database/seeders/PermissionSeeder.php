<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class PermissionSeeder extends Seeder
{
    public function permissions(): array
    {
        return [
            'permission',
            'role',
            'user',
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsToCreate = collect($this->permissions())->map(function ($permissionModule) {
            $items = [];
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $permissionName = "{$action}-{$permissionModule}";
                $items[] = [
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'module' => $permissionModule,
                ];
            }

            return $items;
        })
            ->flatten(1)
            ->each(function ($item) {
                Permission::query()->create($item);
            });

        collect($this->roles())->each(function ($roleName) {
            $newRole = Role::query()->create([
                'name' => $roleName,
                'guard_name' => 'web',
                'default' => true,
            ]);
            $newRole->givePermissionTo(Permission::all());
        });
    }

    private function roles(): array
    {
        return [
            'super-admin',
            'admin',
        ];
    }
}
