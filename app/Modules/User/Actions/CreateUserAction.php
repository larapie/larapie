<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use App\Packages\Actions\Abstracts\Action;

class CreateUserAction extends Action
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'email_verified_at' => '',
            'password' => 'required|string|min:8',
        ];
    }

    public function handle()
    {
        return User::create($this->validated());
    }

    protected function onSuccess(User $user)
    {
        event(new UserRegisteredEvent($user));
    }
}
