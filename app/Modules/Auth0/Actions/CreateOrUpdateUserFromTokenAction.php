<?php

namespace App\Modules\Auth0\Actions;

use App\Modules\Auth0\Exceptions\EmailNotVerifiedException;
use App\Modules\User\Actions\CreateUserAction;
use App\Modules\User\Models\User;
use Auth0\Login\Auth0Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Larapie\Actions\Action;

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
        $token = $service->decodeJWT($this->token);

        // DO NOT REMOVE LINE! Security measure.
        // Identification is based on email.
        // If the email is not verified we cannot verify that the account is from the mail owner.
        // This also means that a user will always have 1 account per mail.
        // Regardless of what identity provider he's using.
        if (! $token->email_verified) {
            throw new EmailNotVerifiedException('Access Denied: email not verified.');
        }
        $user = User::where('email', $token->email)->first();

        if ($user === null) {
            return (new CreateUserAction([
                'name' => $token->name,
                'email' => $token->email,
                'email_verified_at' => $token->email_verified ? Carbon::now() : null,
                'password' => Hash::make(Str::random()),
            ]))->run();
        }

        if ($token->name !== $user->name) {
            $user->name = $token->name;
            $user->save();
        }

        return $user;
    }
}
