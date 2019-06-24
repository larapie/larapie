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
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | These are the permissions that will assigned to a role.
    | Execute the authorization:update command to synchronize these roles.
    |
    */
    'roles' => [
        Role::MEMBER => [
            UserPolicy::PERMISSION_CREATE,
            UserPolicy::PERMISSION_UPDATE,
            UserPolicy::PERMISSION_DELETE,
        ],
        Role::ADMIN => '*',
    ],
];
