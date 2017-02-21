<?php

use Illuminate\Database\Seeder;
use App\Permission;
use App\Role;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionMap = [
            'administrator' => ['create users', 'see users', 'edit users', 'delete users', 'edit roles'],
            'agent' => ['see users', 'edit own user'],
            'customer' => ['see own user', 'edit own user']
        ];

        foreach ($permissionMap as $role => $permissions) {
            $permissionIds = Permission::whereIn('name', $permissions)
                ->get()
                ->pluck('id');

            Role::where('name', $role)
                ->first()
                ->permissions()
                ->attach($permissionIds);
        }
    }
}
