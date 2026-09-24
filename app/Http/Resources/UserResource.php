<?php

namespace App\Http\Resources;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'role' => [
                'value' => $this->role_type->value,
                'label' => $this->role_type->label(),
            ],
            'agency_id' => $this->agency_id,
            'is_active' => $this->is_active,
            /** @var list<string> */
            'permissions' => array_map(fn (Permission $permission): string => $permission->value, $this->permissions()),
        ];
    }
}
