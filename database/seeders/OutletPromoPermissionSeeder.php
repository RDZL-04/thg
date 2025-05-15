<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class OutletPromoPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Outlet Promo Permission insert into role_permission table
        // Prepare Data
        $data = [
            [
                'permission_name' => 'outlet-promo-create',
                'description' => 'Create Promo data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-promo-edit',
                'description' => 'Update Promo data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ],
            [
                'permission_name' => 'outlet-promo-delete',
                'description' => 'Delete Promo data',
                'created_at' =>  now(),
                'updated_at' =>  now()
            ]
        ];
        // Execute insert using DB
        DB::table('role_permission')->insertOrIgnore($data);
    }
}
