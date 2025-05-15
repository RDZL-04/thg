<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class OutletTablePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Outlet Table Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'outlet-table-create',
                'description' => 'Create Outlet Table data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-table-edit',
                'description' => 'Update Outlet Table data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-table-delete',
                'description' => 'Delete Outlet Table data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
