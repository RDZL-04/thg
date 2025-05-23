<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
		
		 $this->call([
			RolePermissionSeeder::class,
            MiceCategoryPermissionSeeder::class,
            MiceHallPermissionSeeder::class,
            OutletMenuCategoryPermissionSeeder::class,
            OutletMenuPermissionSeeder::class,
            OutletPermissionSeeder::class,
            OutletPromoPermissionSeeder::class,
            OutletTablePermissionSeeder::class,
            UtilityRoleAuthSeeder::class,
            UtilityPermissionSeeder::class,
            
            //seeder untuk mroles saat pertama kali untuk set default role admin
            // MrolesSeeder::class
		]);
    }
}
