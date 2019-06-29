<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Events\PermissionDeletedEvent;
use App\Modules\Authorization\Models\Permission;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Packages\Actions\Abstracts\Action;

class DeletePermissionAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::DELETE_PERMISSION);
    }

    public function rules()
    {
        return [
            'permission' => 'required|string',
        ];
    }

    public function handle()
    {
        return tap(Permission::findByName($this->permission), function (Permission $permission) {
            $permission->delete();
        });
    }

    protected function onSuccess(Permission $permission)
    {
        event(new PermissionDeletedEvent($permission));
    }
}
