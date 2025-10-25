<?php

declare(strict_types=1);

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-role');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:roles,name'],
        ];
    }
}
