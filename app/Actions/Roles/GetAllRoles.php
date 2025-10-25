<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class GetAllRoles
{
    public function execute(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Role::query()
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->paginate($perPage);
    }
}
