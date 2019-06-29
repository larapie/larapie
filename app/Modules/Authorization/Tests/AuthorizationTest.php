<?php

namespace App\Modules\Authorization\Tests;

use App\Modules\Authorization\Actions\AssignPermissionsToRoleAction;
use App\Modules\Authorization\Actions\AssignPermissionToUserAction;
use App\Modules\Authorization\Actions\GetRolesAction;
use App\Modules\Authorization\Contracts\Roles;
use App\Modules\Authorization\Models\Permission;
use App\Modules\Authorization\Models\Role;
use App\Modules\Authorization\Permissions\AuthorizationPermission;
use App\Modules\Authorization\Traits\ActAuthorized;
use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use App\Packages\Actions\Traits\ApiActionRunner;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Config;
use Larapie\Core\Base\Test;
use Larapie\Core\Traits\ResetDatabase;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class AuthorizationTest extends Test
{
    use ResetDatabase, ActAuthorized, ApiActionRunner;

    protected $permissions = [];

    protected $roles = Roles::MEMBER;

    public function testAllRolesGenerated()
    {
        Role::all()->pluck('name')->each(function (string $roleName) {
            $this->assertContains($roleName, array_keys(config('authorization.roles')));
        });
    }

    public function testAllPermissionsGenerated()
    {
        $permissionNames = collect(config('authorization.roles'))
            ->flatten()
            ->merge(config('authorization.permissions'))
            ->filter(function (string $permissionName) {
                return $permissionName !== '*';
            });

        $this->assertFalse(Permission::exists('*'));

        Permission::all()
            ->pluck('name')
            ->each(function (string $permissionName) use ($permissionNames) {
                $this->assertContains($permissionName, $permissionNames);
            });
    }

    public function testRolesHaveCorrectPermissions()
    {
        $roles = collect(config('authorization.roles'));

        //TEST NORMAL ROLE-PERMISSIONS
        $roles->filter(function ($permissionNames) {
            return $permissionNames !== '*';
        })->each(function ($permissionNames, $roleName) {
            $this->assertTrue(Role::exists($roleName));
            foreach ($permissionNames as $permissionName) {
                $this->assertTrue(Role::findByName($roleName)->hasPermissionTo($permissionName));
            }
        });

        //TEST WILDCARD ROLE-PERMISSIONS
        $roles->filter(function ($permissionName) {
            return $permissionName === '*';
        })->each(function ($wildcard, $roleName) {
            Permission::all()->pluck('name')->each(function (string $permissionName) use ($roleName) {
                $this->assertTrue($role = Role::findByName($roleName)->hasPermissionTo($permissionName));
            });
        });
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

    public function testUpdateAdjustedPermissions()
    {
        Config::set('authorization.roles', [
            'somenewrole' => [
                'somepermission',
                'somepermission2',
            ],
        ]);
        $this->artisan('authorization:update', ['--delete' => true]);

        $this->assertNotNull(\Spatie\Permission\Models\Role::findByName('somenewrole'));
        $this->assertNotNull(\Spatie\Permission\Models\Permission::findByName('somepermission'));

        Config::set('authorization.permissions', [
            'somepermission',
            'somepermission2',
        ]);

        Config::set('authorization.roles', [
            'somenewrole' => [
                'somepermission2',
                'somepermission3',
            ],
        ]);

        $this->artisan('authorization:update', ['--delete' => true]);

        $this->assertFalse(Role::findByName('somenewrole')->hasPermissionTo('somepermission'));
        $this->assertTrue(Role::findByName('somenewrole')->hasPermissionTo('somepermission2'));
        $this->assertTrue(Role::findByName('somenewrole')->hasPermissionTo('somepermission3'));
        Config::set('authorization.permissions', []);

        Config::set('authorization.roles', []);
        $this->artisan('authorization:update');

        $this->assertNotNull($role = Role::findByName('somenewrole'));
        $this->assertEmpty($role->getAllPermissions());

        $this->artisan('authorization:update', ['--delete' => true]);

        $this->expectException(PermissionDoesNotExist::class);
        Permission::findByName('somepermission');
    }

    public function testNonArrayRolePermission()
    {
        Config::set('authorization.roles', [
            'somenewrole' => 'somepermission',
        ]);
        $this->artisan('authorization:update', ['--delete' => true]);

        $this->assertTrue(Role::findByName('somenewrole')->hasPermissionTo('somepermission'));
    }

    public function testAssignPermissionToUser()
    {
        $this->user()->givePermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_USER);

        $permissionName = 'somerandompermission';
        Permission::create(['name' => $permissionName]);

        $action = new AssignPermissionToUserAction([
            'user_id' => $this->user()->id,
            'permissions' => $permissionName,
        ]);
        $action->run();

        $this->assertTrue($this->user()->hasPermissionTo($permissionName));

        $this->user()->revokePermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_USER);
        $this->assertFalse($this->user()->hasPermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_USER));

        $this->expectException(AuthorizationException::class);
        $action->actingAs($this->user());
        $action->run();
    }

    public function testAssignPermissionToRole()
    {
        $this->user()->givePermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_ROLE);

        $permissionNames = ['somerandompermission', 'somerandompermission2'];
        $roleName = 'somenewrole';

        Role::create(['name' => $roleName]);
        Permission::create(['name' => $permissionNames[0]]);
        Permission::create(['name' => $permissionNames[1]]);

        $action = new AssignPermissionsToRoleAction([
            'role' => $roleName,
            'permissions' => $permissionNames,
        ]);
        $action->run();

        $this->assertTrue(Role::findByName($roleName)->hasPermissionTo($permissionNames[0]));
        $this->assertTrue(Role::findByName($roleName)->hasPermissionTo($permissionNames[1]));

        $this->user()->revokePermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_ROLE);
        $this->assertFalse($this->user()->hasPermissionTo(AuthorizationPermission::ASSIGN_PERMISSION_TO_ROLE));

        $this->expectException(AuthorizationException::class);
        $action = new AssignPermissionsToRoleAction([
            'role' => $roleName,
            'permissions' => $permissionNames[0],
        ]);
        $action->actingAs($this->user());
        $action->run();
    }

    public function testIndexRoles()
    {
        $this->user(function (User $user) {
            $user->assignRole(Role::ADMIN);
        });
        $this->assertTrue($this->user()->hasPermissionTo(AuthorizationPermission::INDEX_ROLES));

        $roles = $this->runActionFromApi(new GetRolesAction());

        collect($roles)->each(function ($role) {
            $this->assertArrayHasKeys(['name', 'permissions'], $role);
        });

        $roleNames = collect($roles)->pluck('name');
        $this->assertContains(Role::ADMIN, $roleNames);
        $this->assertContains(Role::MEMBER, $roleNames);
        $this->assertContains(Role::GUEST, $roleNames);
    }
}
