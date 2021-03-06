<?php

namespace App\Modules\Authorization\Events;

use App\Modules\Authorization\Models\Permission;

class PermissionDeletedEvent
{
    public $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
}
