<?php

namespace App\Modules\User\Events;

use App\Modules\Authorization\Listeners\AssignDefaultRoleToUser;
use App\Modules\User\Listeners\SendWelcomeNotification;
use App\Modules\User\Models\User;
use Illuminate\Auth\Events\Registered;
use Larapie\Core\Contracts\Listeners;

class UserRegisteredEvent extends Registered implements Listeners
{
    /**
     *
     * The authenticated user.
     *
     * @var User
     */
    public $user;

    public function listeners(): array
    {
        return [
            AssignDefaultRoleToUser::class,
            SendWelcomeNotification::class,
        ];
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
