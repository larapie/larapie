<?php


namespace App\Modules\Authorization\Tests;

use App\Modules\Authorization\Contracts\Role;
use App\Modules\Authorization\Manager\AuthorizationManager;
use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Larapie\Core\Base\Test;
use Larapie\Core\Traits\ResetDatabase;
use Spatie\Permission\Models\Permission;

class AuthorizationTest extends Test
{
    Use ResetDatabase;

    public function testUpdateAuthorization()
    {
        $permissions = Permission::all()->pluck('name')->toArray();
        $this->assertEquals($permissions, AuthorizationManager::getPermissions()->toArray());
    }

    public function testAdminAssignmentRole()
    {
        $user = factory(User::class)->create();
        $this->assertFalse($user->hasRole(Role::ADMIN));
        $user->assignRole(Role::ADMIN);
        $this->assertTrue($user->hasRole(Role::ADMIN));
    }

    public function testAssignDefaultRole(){
        $user = factory(User::class)->create();
        event(new UserRegisteredEvent($user));
        $this->assertTrue($user->hasRole(config('authorization.default_role')));
    }
}
