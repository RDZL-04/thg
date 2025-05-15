<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class OutletMenuCategoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Outlet Menu Category Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'outlet-menu-category-create',
                'description' => 'Create Outlet Menu Category data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-menu-category-edit',
                'description' => 'Update Outlet Menu Category data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-menu-category-delete',
                'description' => 'Delete Outlet Menu Category data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
