<?php


namespace App\Modules\Authorization\Permissions;


interface AuthorizationPermission
{
    const CREATE_PERMISSION = "create-permission";
    const DELETE_PERMISSION = "delete-permission";

    const INDEX_ROLES = "index-roles";
    const CREATE_ROLE = "create-role";
    const DELETE_ROLE = "delete-role";

    const ASSIGN_PERMISSION_TO_ROLE = "assign-permission-to-role";
    const ASSIGN_PERMISSION_TO_USER = "assign-permission-to-user";

    const ASSIGN_ROLE_TO_USER = "assign-role-to-user";
}
