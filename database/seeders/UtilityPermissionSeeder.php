<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UtilityPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            [
                'permission_name' => 'utility',
                'description' => 'Access Utility',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
