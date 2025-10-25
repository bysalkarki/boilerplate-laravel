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
        $permission = collect($this->permissions())->map(function ($permission) {
            $items = [];
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $items[] = "{$action}-{$permission}";
            }

            return $items;
        })
            ->flatten()
            ->each(function ($item) {
                Permission::query()->create(
                    [
                        'name' => $item,
                    ]
                );
            });

        collect($this->roles())->each(function ($role) use ($permission) {
            $newRole = Role::query()->create([
                'name' => $role,
                'guard_name' => 'web',
                'default' => true,
            ]);

            $newRole->givePermissionTo($permission);
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
