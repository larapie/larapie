<?php

namespace App\Modules\Authorization\Models;

use Spatie\Permission\Guard;

class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * Check if a permission exists.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return bool
     */
    public static function exists(string $name, $guardName = null): bool
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName])->first();
        if (! $permission) {
            return false;
        }

        return true;
    }
}
