<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Models\Role;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Modules\Authorization\Transformers\RoleTransformer;
use App\Packages\Actions\Abstracts\Action;

class GetRolesAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::INDEX_ROLES);
    }

    public function handle()
    {
        return Role::get();
    }

    public function response($result, $request)
    {
        return RoleTransformer::collection($result)->include('permissions');
    }
}
