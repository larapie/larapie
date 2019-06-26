<?php

use App\Modules\Authorization\Contracts\Role;
use App\Modules\User\Policies\UserPolicy;

return [

    /*
    |--------------------------------------------------------------------------
    | Default User Role
    |--------------------------------------------------------------------------
    |
    | This is the role that will be assigned to a newly created user.
    |
    */

    'default_role' => Role::MEMBER,


    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | These are the permissions that will be registered.
    | Execute the authorization:update command to synchronize these roles.
    | Permissions declared on a Policy class as a constant that starts with PERMISSION_
    | will automatically be registered.
    |
    */
    'permissions' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | These are the permissions that will assigned to a role.
    | Execute the authorization:update command to synchronize these roles.
    | Roles declared as constants on the Authorization\Contracts\Role Interfaces
    | will automatically be registered aswell
    |
    */
    'roles' => [
        Role::MEMBER => [
            UserPolicy::PERMISSION_CREATE,
            UserPolicy::PERMISSION_UPDATE,
            UserPolicy::PERMISSION_DELETE
        ],
        Role::ADMIN => '*',
    ],
];
