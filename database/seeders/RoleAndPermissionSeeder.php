<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        $rolesPermissions = [
            'client' => [
                'create.ad',
                'update.ad',
                'delete.ad',
                'view.ad',
            ],
            'admin' => [
                'moderate.ad',
            ],
        ];

        $allPermissions = collect($rolesPermissions)->flatten()->unique()->values();

        Permission::whereNotIn('name', $allPermissions)->delete();

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $allRoles = array_keys($rolesPermissions);
        Role::whereNotIn('name', $allRoles)->delete();

        foreach ($rolesPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
