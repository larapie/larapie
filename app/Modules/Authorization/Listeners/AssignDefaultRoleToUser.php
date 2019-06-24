<?php

namespace App\Modules\Authorization\Listeners;

use App\Modules\User\Events\UserRegisteredEvent;

class AssignDefaultRoleToUser
{
    /**
     * @param UserRegisteredEvent $event
     */
    public function handle(UserRegisteredEvent $event): void
    {
        $event->getUser()->syncRoles(config('authorization.default_role'));
    }

    /**
     * @param UserRegisteredEvent $event
     * @param $exception
     */
    public function failed(UserRegisteredEvent $event, $exception): void
    {
    }
}
