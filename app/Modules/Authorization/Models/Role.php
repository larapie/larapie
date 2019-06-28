<?php


namespace App\Modules\Authorization\Models;


use App\Modules\Authorization\Contracts\Roles;
use Spatie\Permission\Guard;

class Role extends \Spatie\Permission\Models\Role implements Roles
{
    /**
     * Check if a role exists
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return bool
     */
    public static function exists(string $name, $guardName = null) :bool
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('name', $name)->where('guard_name', $guardName)->first();

        if (! $role) {
           return false;
        }

        return true;
    }
}
