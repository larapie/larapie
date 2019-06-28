<?php

namespace App\Modules\User\Permissions;

interface UserPermission
{
    const CREATE = 'create-user';
    const UPDATE = 'update-user';
    const DELETE = 'delete-user';
}
