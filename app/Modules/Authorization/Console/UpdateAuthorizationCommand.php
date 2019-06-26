<?php

namespace App\Modules\Authorization\Console;

use App\Modules\Authorization\Manager\AuthorizationManager;
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
    protected $name = 'authorization:update';

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
        $newPermissions = AuthorizationManager::getPermissions();
        $newRoles = AuthorizationManager::getRoles();

        //SYNCHRONIZE PERMISSIONS
        $this->synchronizePermissions($permissions, $newPermissions);

        //SYNCHRONIZE ROLES & ROLEPERMISSIONS
        $this->synchronizeRoles($roles, $newRoles);
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
            $this->warn("Removing Permission: $oldPermisson does not exist anymore.");
            Permission::findByName($oldPermisson)->delete();
        }
    }

    /**
     * @param Permission[] | Collection $roles
     * @param Collection $newRoles
     */
    protected function synchronizeRoles($roles, $newRoles)
    {
        foreach ($newRoles as $newRole => $newPermissions) {
            if ($newPermissions === '*') {
                $newPermissions = AuthorizationManager::getPermissions();
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
            $this->warn("Removing Role: $oldRole does not exist anymore.");
            Role::findByName($oldRole)->delete();
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
        if(is_string($permissions)){
            $permissions = [$permissions];
        }
        foreach($permissions as $permission){
            try {
                Permission::findByName($permission);
            } catch (PermissionDoesNotExist $exception) {
                Permission::create(['name' => $permission]);
            }
            $role->givePermissionTo($permission);
        }
    }
}
