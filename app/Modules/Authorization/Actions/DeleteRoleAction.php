<?php


namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Events\PermissionCreatedEvent;
use App\Modules\Authorization\Events\RoleDeletedEvent;
use App\Modules\Authorization\Models\Role;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Packages\Actions\Abstracts\Action;

class DeleteRoleAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::DELETE_ROLE);
    }

    public function rules()
    {
        return [
            "role" => 'required|string'
        ];
    }

    public function handle()
    {
        return tap(Role::findByName($this->role), function (Role $role) {
            $role->delete();
        });
    }

    protected function onSuccess(Role $role)
    {
        event(new RoleDeletedEvent($role));
    }
}
