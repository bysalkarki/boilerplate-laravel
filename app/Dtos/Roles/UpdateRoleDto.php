<?php

declare(strict_types=1);

namespace App\Dtos\Roles;

final class UpdateRoleDto
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
