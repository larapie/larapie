<?php


namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Events\PermissionCreatedEvent;
use App\Modules\Authorization\Models\Permission;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Packages\Actions\Abstracts\Action;

class CreatePermissionAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::CREATE_PERMISSION);
    }

    public function rules()
    {
        return [
            "permission" => 'required|string'
        ];
    }

    public function handle()
    {
        $permission = [
            "name" => $this->permission
        ];

        if ($this->exists('guard'))
            $permission['guard'] = $this->guard;

        return Permission::create($permission);
    }

    protected function onSuccess(Permission $permission){
        event(new PermissionCreatedEvent($permission));
    }
}
