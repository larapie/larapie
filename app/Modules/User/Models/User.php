<?php

namespace App\Modules\User\Models;

use App\Modules\User\Observers\UserObserver;
use App\Modules\User\Policies\UserPolicy;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use Larapie\Core\Contracts\Observers;
use Larapie\Core\Contracts\Policy;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Modules\User\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\User\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
            UserObserver::class,
        ];
    }

    public function policy(): string
    {
        return UserPolicy::class;
    }
}
