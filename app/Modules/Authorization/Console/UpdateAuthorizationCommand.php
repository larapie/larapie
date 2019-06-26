<?php

namespace App\Modules\Authorization\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //FLUSH CACHE
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //GET CURRENT PERMISSIONS & ROLES
        $permissions = Permission::all()->pluck('name');
        $roles = Role::all()->pluck('name');

        //GET NEW PERMISSION & ROLES
        $newPermissions = config('authorization.permissions');
        $newRoles = config('authorization.roles');

        //SYNCHRONIZE PERMISSIONS
        $this->synchronizePermissions($permissions, $newPermissions);

        //SYNCHRONIZE ROLES & ROLEPERMISSIONS
        $this->synchronizeRoles($roles, $newRoles);

        $this->syncWildcardRoles();
    }

    protected function syncWildcardRoles()
    {
        $roles = new Collection();

        foreach (config('authorization.roles') as $role => $possibleWildcard) {
            if ($possibleWildcard === "*")
                $roles->add($role);
        }
        if ($roles->isNotEmpty()) {
            $permissions = Permission::all()->pluck('name');

            foreach ($roles as $role) {
                $role = Role::findByName($role);
                foreach ($permissions as $permission) {
                    if (!$role->hasPermissionTo($permission))
                        $this->info("Adding $permission permission to $role. Role is asssigned a wildcard");
                }
                $role->syncPermissions($permissions);
            }
        }

    }

    /**
     * @param Permission[] | Collection $permissions
     * @param Collection $newPermissions
     */
    protected function synchronizePermissions($permissions, $newPermissions)
    {
        foreach ($newPermissions as $newPermission) {
            if (is_numeric($key = $permissions->search($newPermission))) {
                $permissions->forget($key);
            } else {
                $this->info("Adding permission: $newPermission");
                Permission::create([
                    'name' => $newPermission,
                ]);
            }
        }

        foreach ($permissions as $oldPermisson) {
            if ($this->option('delete')) {
                $this->warn("Removing Permission: $oldPermisson does not exist anymore.");
                Permission::findByName($oldPermisson)->delete();
            }
        }
    }

    /**
     * @param Permission[] | Collection $roles
     * @param array $newRoles
     */
    protected function synchronizeRoles($roles, $newRoles)
    {
        foreach ($newRoles as $newRole => $newPermissions) {
            if ($newPermissions === '*') {
                $newPermissions = config('authorization.permissions');
            }
            if (is_numeric($key = $roles->search($newRole))) {
                $roles->forget($key);
                $this->synchronizeRolePermissions($newRole, $newPermissions);
            } else {
                $this->info("Adding role: $newRole");
                $role = Role::create([
                    'name' => $newRole,
                ]);
                $this->givePermission($role, $newPermissions);
            }
        }

        foreach ($roles as $oldRole) {
            if ($this->option('delete')) {
                $this->warn("Removing Role: $oldRole does not exist anymore.");
                Role::findByName($oldRole)->delete();
            } else {
                $this->warn("Removing permissions from $oldRole it does not actively exist anymore.");
                $aRole = Role::findByName($oldRole);
                foreach ($aRole->getAllPermissions() as $oldPermission) {
                    $this->warn("Removing Permission $oldPermission->name from Role $oldRole.");
                    $aRole->revokePermissionTo($oldPermission);
                }
            }
        }
    }

    protected function synchronizeRolePermissions($newRole, $newPermissions)
    {
        $role = Role::findByName($newRole);
        $oldPermissions = $role->permissions()->get()->pluck('name');

        foreach ($newPermissions as $newPermission) {
            if (is_numeric($key = $oldPermissions->search($newPermission))) {
                $oldPermissions->forget($key);
            } else {
                $this->info("Adding Permission: $newPermission to Role $role->name");
                $this->givePermission($role, $newPermission);
            }
        }
        foreach ($oldPermissions as $oldPermission) {
            $this->warn("Removing Permission $oldPermission from Role $newRole.");
            $role->revokePermissionTo($oldPermission);
        }
    }

    protected function givePermission(Role $role, $permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        foreach ($permissions as $permission) {
            try {
                Permission::findByName($permission);
            } catch (PermissionDoesNotExist $exception) {
                Permission::create(['name' => $permission]);
            }
            $role->givePermissionTo($permission);
        }
    }
}
