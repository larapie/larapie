<?php

namespace App\Modules\User\Models;

use App\Modules\User\Observers\UserObserver;
use App\Modules\User\Policies\UserPolicy;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use Larapie\Core\Contracts\Observers;
use Larapie\Core\Contracts\Policy;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticable implements Observers, Policy
{
    use Notifiable, HasRoles;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function observers(): array
    {
        return [
            UserObserver::class
        ];
    }

    public function policy(): string
    {
        return UserPolicy::class;
    }
}
