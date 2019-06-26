<?php

namespace App\Modules\User\Policies;

use App\Modules\Authorization\Contracts\Roles;
use App\Modules\User\Models\User;
use App\Modules\User\Permissions\UserPermission;

class UserPolicy
{

    /**
     * Determine if the given user can access the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function access(User $user, $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Determine if the given user can create a model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(UserPermission::CREATE);
    }

    /**
     * Determine if the given user can update the model.
     *
     * @param User $user
     *
     *
     * @return bool
     */
    public function update(User $user, $model): bool
    {
        return $this->access($user, $model) && $user->hasPermissionTo(UserPermission::UPDATE);
    }

    /**
     * @param User $user
     * @param $model
     *
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return $this->access($user, $model) && $user->hasPermissionTo(UserPermission::DELETE);
    }

    /**
     * @param User $user
     * @param $ability
     *
     * @return bool|null|void
     */
    public function before($user, $ability)
    {
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }
    }
}
