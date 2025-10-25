<?php

declare(strict_types=1);

namespace App\Dtos\Roles;

final class DeleteRoleDto
{
    public function __construct(
        public int $id,
    ) {}
}
