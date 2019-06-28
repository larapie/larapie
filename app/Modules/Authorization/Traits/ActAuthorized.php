<?php

namespace App\Modules\Authorization\Traits;

use App\Modules\User\Models\User;

trait ActAuthorized
{
    private $actingUser;

    private function initializeUser()
    {
        return tap(factory(User::class)->create(), function (User $user) {
            if (is_string($this->roles))
                $roles = [$this->roles];
            else
                $roles = $this->roles ?? [];

            if (!empty($roles))
                $user->assignRole($roles);

            if (is_string($this->permissions))
                $permissions = [$this->permissions];
            else
                $permissions = $this->permissions ?? [];

            if (!empty($permissions))
                $user->givePermissionTo($permissions);
        });
    }

    protected function user(?Callable $callback = null): User
    {
        if ($this->actingUser === null)
            return $this->actingUser = $this->initializeUser();
        if ($callback !== null)
            $callback($this->actingUser);
        return tap($this->actingUser->fresh(), function ($user) {
            \Auth::login($user);
        });
    }
}
