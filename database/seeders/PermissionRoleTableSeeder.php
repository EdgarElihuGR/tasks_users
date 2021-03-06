<?php

namespace Database\Seeders;

use \App\Models\{Permission, Role};
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        Role::findOrFail(2)->permissions()->attach([2]);
    }
}
