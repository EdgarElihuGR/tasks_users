<?php

namespace Database\Seeders;

use \App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_access',
            ],
            [
                'id'    => 2,
                'title' => 'task_access',
            ],
            [
                'id'    => 3,
                'title' => 'assigned_user_access',
            ],
            [
                'id'    => 4,
                'title' => 'view_all_tasks_access',
            ],
            [
                'id'    => 5,
                'title' => 'alter_tasks_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
