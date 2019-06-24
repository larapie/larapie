<?php

namespace App\Modules\Authorization\Contracts;

interface Role
{
    const GUEST = 'guest';
    const ADMIN = 'admin';
    const MEMBER = 'member';
}
