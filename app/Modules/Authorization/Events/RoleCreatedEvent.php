<?php

namespace App\Modules\Authorization\Events;

use App\Modules\Authorization\Models\Role;

class RoleCreatedEvent
{
    public $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }
}
