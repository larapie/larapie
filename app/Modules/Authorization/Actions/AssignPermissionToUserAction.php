<?php

namespace App\Modules\Authorization\Actions;

use App\Modules\Authorization\Models\Permission;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Modules\User\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Larapie\Actions\Action;

class AssignPermissionToUserAction extends Action
{
    public function authorize()
    {
        return $this->user()->hasPermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_USER);
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
            'user_id' => 'exists:users,id',
        ];
    }

    protected function withValidator(Validator $validator)
    {
        return $validator->sometimes('user', 'required', function ($input) {
            return $input->user_id === null;
        });
    }

    public function handle()
    {
        return User::find($this->user_id)->givePermissionTo(collect($this->permissions)->flatten());
    }
}
