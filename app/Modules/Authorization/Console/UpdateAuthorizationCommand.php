<?php

namespace App\Modules\Authorization\Console;

use App\Modules\Authorization\Models\Permission;
use App\Modules\Authorization\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdateAuthorizationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'authorization:update {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all permission and roles.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //FLUSH CACHE
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //SYNCHRONIZE PERMISSIONS
        $this->synchronizePermissions();

        //SYNCHRONIZE ROLE_PERMISSIONS
        $this->synchronizeRoles();

        //SYNCHRONIZE WILDCARD ROLES
        $this->syncWildcardRoles();
    }

    /**
     * @param Permission[] | Collection $permissions
     * @param array $newPermissions
     */
    protected function synchronizePermissions()
    {
        $existingPermissions = Permission::all()->pluck('name');

        $newPermissions = $this->getNewPermissions();

        $newPermissions
            ->filter(function ($permissionName) {
                return ! Permission::exists($permissionName);
            })
            ->each(function ($newPermissionName) {
                $this->createPermission($newPermissionName);
            });

        if ($this->option('delete')) {
            $existingPermissions->diff($newPermissions)->each(function ($permissionName) {
                $this->removePermission($permissionName);
            });
        }
    }

    /**
     * @param Collection $roles
     * @param array $newRoles
     */
    protected function synchronizeRoles()
    {
        $existingRoles = Role::all()->pluck('name');
        $roles = collect(config('authorization.roles'));

        //CREATE NEW ROLES
        $roles
            ->filter(function ($permissionNames) {
                return ! $this->permissionsHasWildcard($permissionNames);
            })
            ->filter(function ($permissionNames, $roleName) {
                return ! Role::exists($roleName);
            })
            ->each(function ($permissionNames, $roleName) {
                $role = $this->createRole($roleName);
                $this->givePermissionsToRole($role, $permissionNames);
            });

        //SYNCHRONIZE EXISTING ROLES
        $roles
            ->filter(function ($permissionNames) {
                return ! $this->permissionsHasWildcard($permissionNames);
            })
            ->filter(function ($permissionNames, $roleName) {
                return Role::exists($roleName);
            })
            ->each(function ($permissionNames, $roleName) {
                $this->synchronizeRolePermissions($roleName, $permissionNames);
            });

        //SYNCHRONIZE INACTIVE ROLES
        $existingRoles->diff($roles->keys())->each(function ($roleName) {
            $this->option('delete') ? $this->removeRole($roleName) : $this->removePermissionsFromInactiveRole($roleName);
        });
    }

    protected function synchronizeRolePermissions($newRole, $newPermissions)
    {
        $role = Role::findByName($newRole);

        collect($newPermissions)
            ->flatten()
            ->filter(function (string $permissionName) use ($role) {
                return ! $role->hasPermissionTo($permissionName);
            })
            ->each(function (string $permissionName) use ($role) {
                $this->info("Adding Permission: $permissionName to Role ".strtoupper($role->name).'.');
                $role->givePermissionTo($permissionName);
            });

        $role->getAllPermissions()
            ->pluck('name')
            ->diff(collect($newPermissions))
            ->each(function (string $permissionName) use ($role) {
                $this->warn("Removing Permission $permissionName from Role ".strtoupper($role->name).'.');
                $role->revokePermissionTo($permissionName);
            });
    }

    protected function syncWildcardRoles()
    {
        collect(config('authorization.roles'))
            ->filter(function ($permissionNames) {
                return $this->permissionsHasWildcard($permissionNames);
            })->each(function ($wildcard, $roleName) {
                $role = Role::exists($roleName) ? Role::findByName($roleName) : $this->createRole($roleName);
                tap(Permission::all()->pluck('name'), function (Collection $permissionNames) use ($role) {
                    $permissionNames
                        ->filter(function ($permissionName) use ($role) {
                            return ! $role->hasPermissionTo($permissionName);
                        })
                        ->each(function ($permissionName) use ($role) {
                            $this->info("Adding permission $permissionName to ".strtoupper($role->name).'. Role has a wildcard');
                        });
                    $role->syncPermissions($permissionNames);
                });
            });
    }

    protected function getNewPermissions(): Collection
    {
        return collect(config('authorization.permissions'))
            ->merge(
                collect(config('authorization.roles'))
                    ->flatten()
                    ->filter(function ($permissionName) {
                        return ! $this->permissionsHasWildcard($permissionName);
                    }))
            ->flatten()
            ->unique();
    }

    protected function givePermissionsToRole(Role $role, $permissions)
    {
        collect($permissions)
            ->flatten()
            ->filter(function ($permissionName) {
                return ! $this->permissionsHasWildcard($permissionName);
            })
            ->filter(function ($permissionName) use ($role) {
                return ! $role->hasPermissionTo($permissionName);
            })
            ->each(function (string $permissionName) use ($role) {
                $this->info("Adding permission $permissionName to ".strtoupper($role->name).'. Role is has a wildcard');
                $role->givePermissionTo($permissionName);
            });
    }

    protected function createRole(string $roleName): Role
    {
        $this->info('Role '.strtoupper($roleName).' does not exist. Creating role..');
        return Role::create(['name' => $roleName]);
    }

    protected function removeRole(string $roleName)
    {
        $this->warn('Removing Role: '.strtoupper($roleName).' does not exist anymore.');
        Role::findByName($roleName)->delete();
    }

    protected function removePermission(string $permissionName)
    {
        $this->warn("Removing Permission: $permissionName does not exist anymore.");
        Permission::findByName($permissionName)->delete();
    }

    protected function createPermission(string $permissionName)
    {
        $this->info("Permission $permissionName does not exist. Creating Permission..");
        return Permission::create(['name' => $permissionName]);
    }

    protected function removePermissionFromRole(Role $role, string $permissionName)
    {
        $this->warn("Removing Permission $permissionName from Role $role->name.");
        $role->revokePermissionTo($permissionName);
    }

    protected function removePermissionsFromInactiveRole(string $roleName)
    {
        $role = Role::findByName($roleName);
        $permissions = $role->getAllPermissions();

        if ($permissions->isNotEmpty()) {
            $this->warn('Removing permissions from '.strtoupper($roleName).' it does not actively exist anymore.');
        }

        $permissions->each(function ($permission) use ($role) {
            $this->removePermissionFromRole($role, $permission->name);
        });
    }

    protected function permissionsHasWildcard($permissions)
    {
        return $permissions === '*' || collect($permissions)->flatten()->contains('*');
    }
}
