<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

final class Role extends BaseRole
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'default' => 'boolean',
        ];
    }
}
