<?php

namespace App\Modules\User\Policies;

use App\Modules\Authorization\Contracts\Role;
use App\Modules\User\Models\User;

class UserPolicy
{
    const PERMISSION_CREATE = 'create-user';
    const PERMISSION_CREATEs = 'create-userqsgdsgd';
    const PERMISSION_UPDATE = 'update-user';
    const PERMISSION_DELETE = 'delete-user';

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
        return $user->hasPermissionTo(self::PERMISSION_CREATE);
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
        return $this->access($user, $model) && $user->hasPermissionTo(self::PERMISSION_UPDATE);
    }

    /**
     * @param User $user
     * @param $model
     *
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return $this->access($user, $model) && $user->hasPermissionTo(self::PERMISSION_DELETE);
    }

    /**
     * @param User $user
     * @param $ability
     *
     * @return bool|null|void
     */
    public function before($user, $ability)
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }
    }
}
