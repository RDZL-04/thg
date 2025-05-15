<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UtilityRoleAuthSeeder extends Seeder
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
                'role_id' => 1,
                'permission_name' => 'utility',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_auth')->insertOrIgnore($data);
    }
}
