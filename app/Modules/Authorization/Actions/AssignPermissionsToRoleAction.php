<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Models\Permission;
use App\Modules\Authorization\Models\Role;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Packages\Actions\Abstracts\Action;

class AssignPermissionsToRoleAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_ROLE);
    }

    public function rules()
    {
        return [
            'permissions' => 'required',
            'permissions.*' => ['string', function ($attribute, $permission, $fail) {
                if (! Permission::exists($permission)) {
                    $fail('Permission'.$permission.' does not exist.');
                }
            }],
            'role' => ['required', 'string', function ($attribute, $role, $fail) {
                if (! Role::exists($role)) {
                    $fail('Role'.$attribute.' does not exist.');
                }
            }],
        ];
    }

    public function handle()
    {
        return Role::findByName($this->role)->givePermissionTo(collect($this->permissions)->flatten());
    }
}
