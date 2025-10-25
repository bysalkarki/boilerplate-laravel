<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class GetAllUsers
{
    public function execute(?string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->when($search, fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->with(['roles'])
            ->paginate($perPage);
    }
}
