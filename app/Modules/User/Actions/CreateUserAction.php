<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use Hash;
use Lorisleiva\Actions\Action;

class CreateUserAction extends Action
{
    public function rules(){
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'email_verified_at' => '',
            'password' => 'required|string|min:8',
        ];
    }

    public function handle(){
        return tap(User::create($this->validated()),function ($user){
            event(new UserRegisteredEvent($user));
        });
    }
}
