<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MiceHallPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mice Hall Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'mice-hall-create',
                'description' => 'Create Mice Hall data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'mice-hall-edit',
                'description' => 'Update Mice Hall data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'mice-hall-delete',
                'description' => 'Delete Mice Hall data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
