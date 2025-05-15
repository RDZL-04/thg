<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class OutletPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Outlet Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'outlet-create',
                'description' => 'Create Outlet data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-edit',
                'description' => 'Update Outlet data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-delete',
                'description' => 'Delete Outlet data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
