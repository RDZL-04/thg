<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class AttractionPermissionSeeder extends Seeder
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
                'permission_name' => 'hotel-attraction-add',
                'description' => 'Create hotel attraction data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'hotel-attraction-update',
                'description' => 'Update hotel attraction data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'hotel-attraction-delete',
                'description' => 'Delete hotel attraction data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
