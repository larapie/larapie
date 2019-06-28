<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Events\RoleCreatedEvent;
use App\Modules\Authorization\Models\Role;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Packages\Actions\Abstracts\Action;

class CreateRoleAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::CREATE_ROLE);
    }

    public function rules()
    {
        return [
            'role' => 'required|string',
            'guard' => 'string',
        ];
    }

    public function handle()
    {
        $role = [
            'name' => $this->role,
        ];

        if ($this->exists('guard')) {
            $role['guard'] = $this->guard;
        }

        return Role::create($role);
    }

    protected function onSuccess(Role $role)
    {
        event(new RoleCreatedEvent($role));
    }
}
