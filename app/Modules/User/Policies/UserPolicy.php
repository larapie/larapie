<?php

namespace App\Modules\User\Policies;

use App\Modules\User\Models\User;

class UserPolicy
{
    /**
     * Determine if the given user can access the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
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
        return true;
    }

    /**
     * @param User $user
     * @param $model
     *
     * @return bool
     */
    public function delete(User $user, $model): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @param $ability
     *
     * @return bool|null|void
     */
    public function before($user, $ability)
    {

    }
}
