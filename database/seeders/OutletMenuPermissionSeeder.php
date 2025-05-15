<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class OutletMenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Outlet Menu Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'outlet-menu-list',
                'description' => 'View Outlet Menu data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ], 
            [
                'permission_name' => 'outlet-menu-create',
                'description' => 'Create Outlet Menu data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-menu-edit',
                'description' => 'Update Outlet Menu data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-menu-delete',
                'description' => 'Delete Outlet Menu data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
