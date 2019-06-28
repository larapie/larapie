<?php

use App\Modules\Authorization\Contracts\Roles;

return [

    /*
    |--------------------------------------------------------------------------
    | Default User Role
    |--------------------------------------------------------------------------
    |
    | This is the role that will be assigned to a newly created user.
    |
    */

    'default_role' => Roles::MEMBER,

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
        \App\Modules\Authorization\Permissions\AuthorizationPermission::INDEX_ROLES,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::DELETE_PERMISSION,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::CREATE_PERMISSION,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::CREATE_ROLE,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::DELETE_ROLE,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::ASSIGN_PERMISSION_TO_ROLE,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::ASSIGN_PERMISSION_TO_USER,
        \App\Modules\Authorization\Permissions\AuthorizationPermission::ASSIGN_ROLE_TO_USER,

        \App\Modules\User\Permissions\UserPermission::CREATE,
        \App\Modules\User\Permissions\UserPermission::UPDATE,
        \App\Modules\User\Permissions\UserPermission::DELETE,

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
        Roles::MEMBER => [
            \App\Modules\User\Permissions\UserPermission::CREATE,
            \App\Modules\User\Permissions\UserPermission::UPDATE,
            \App\Modules\User\Permissions\UserPermission::DELETE,
        ],
        Roles::ADMIN => '*',
        Roles::GUEST => [],
    ],
];
