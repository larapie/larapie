<?php

namespace App\Modules\Authorization\Tests;

use App\Modules\Authorization\Contracts\Roles;
use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Config;
use Larapie\Core\Base\Test;
use Larapie\Core\Traits\ResetDatabase;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthorizationTest extends Test
{
    use ResetDatabase;

    public function testUpdateAuthorization()
    {
        $roles = Role::all()->pluck('name')->toArray();
        $this->assertEquals($roles, array_keys(config('authorization.roles')));
    }

    public function testAdminAssignmentRole()
    {
        $user = factory(User::class)->create();
        $this->assertFalse($user->hasRole(Roles::ADMIN));
        $user->assignRole(Roles::ADMIN);
        $this->assertTrue($user->hasRole(Roles::ADMIN));
    }

    public function testAssignDefaultRole()
    {
        $user = factory(User::class)->create();
        event(new UserRegisteredEvent($user));
        $this->assertTrue($user->hasRole(config('authorization.default_role')));
    }

    public function testUpdatePermissions()
    {
        Config::set('authorization.roles', [
            "somenewrole" => [
                "somepermission",
                "somepermission2"
            ]
        ]);
        $this->artisan('authorization:update', ['--delete' => true]);

        $this->assertNotNull(\Spatie\Permission\Models\Role::findByName('somenewrole'));
        $this->assertNotNull(\Spatie\Permission\Models\Permission::findByName('somepermission'));

        Config::set('authorization.permissions', [
            "somepermission",
            "somepermission2"
        ]);

        Config::set('authorization.roles', [
            "somenewrole" => [
                "somepermission2"
            ]
        ]);

        $this->artisan('authorization:update', ['--delete' => true]);

        $this->assertFalse(Role::findByName('somenewrole')->hasPermissionTo('somepermission'));

        Config::set('authorization.permissions', []);

        Config::set('authorization.roles', []);
        $this->artisan('authorization:update');

        $this->assertNotNull($role = Role::findByName('somenewrole'));
        $this->assertEmpty($role->getAllPermissions());

        $this->artisan('authorization:update', ['--delete' => true]);

        $this->expectException(PermissionDoesNotExist::class);
        Permission::findByName('somepermission');
    }

    public function testNonArrayRolePermission(){
        Config::set('authorization.roles', [
            "somenewrole" => "somepermission",
        ]);
        $this->artisan('authorization:update', ['--delete' => true]);

        $this->assertTrue(Role::findByName('somenewrole')->hasPermissionTo('somepermission'));
    }
}
