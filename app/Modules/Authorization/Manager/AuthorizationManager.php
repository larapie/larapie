<?php

namespace App\Modules\Authorization\Manager;

use App\Modules\Authorization\Contracts\Role;
use App\Modules\Authorization\Exceptions\DuplicatePermissionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Larapie\Core\Support\Facades\Larapie;

class AuthorizationManager
{
    public static function getPermissions(): Collection
    {
        $permissions = new Collection();
        foreach (Larapie::getModules() as $module) {
            foreach ($module->getPolicies() as $policy) {
                $constants = get_class_constants($policy->getFQN());
                foreach ($constants as $key => $value) {
                    if (Str::startsWith($key, 'PERMISSION_')) {
                        if ($permissions->contains($value)) {
                            throw new DuplicatePermissionException("Permission $value is declared twice on policy: ".$policy->getFQN());
                        }
                        $permissions->add($value);
                    }
                }
            }
        }
        return $permissions->merge(config('authorization.permissions'));
    }

    public static function getRoles(): Collection
    {
        $roles = collect(get_class_constants(Role::class))->flatten();
        $rolePermissions = collect(config('authorization.roles'));

        foreach ($roles as $role) {
            if (! $rolePermissions->has($role)) {
                $rolePermissions->put($role, []);
            }
        }

        return $rolePermissions;
    }
}
