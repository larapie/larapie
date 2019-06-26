<?php

namespace App\Modules\Auth0\Actions;

use App\Modules\User\Actions\CreateUserAction;
use App\Modules\Auth0\Services\Auth0Service;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Action;

class CreateOrUpdateUserFromTokenAction extends Action
{
    public function rules()
    {
        return [
            'token' => 'required|string',
        ];
    }

    public function handle(Auth0Service $service)
    {
        $token = (object) $service->decodeJWT($this->token);

        $user = User::where('email', $token->email)->first();

        if ($user === null)
            return (new CreateUserAction([
                "name" => $token->name,
                "email" => $token->email,
                "email_verified_at" => $token->email_verified ? Carbon::now() : null,
                "password" => Hash::make(Str::random())
            ]))->run();

        if ($token->name === $user->name) {
            $user->name = $token->name;
            $user->save();
        }
        return $user;
    }
}
