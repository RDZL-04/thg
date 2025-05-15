<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MiceCategoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mice Category Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'mice-category-create',
                'description' => 'Create Mice Category data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'mice-category-edit',
                'description' => 'Update Mice Category data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'mice-category-delete',
                'description' => 'Delete Mice Category data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
