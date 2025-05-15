<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MrolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $sql = "
		INSERT IGNORE INTO `mroles` (`id`, `role_nm`,`created_by`, `updated_by`, `description`) VALUES
            (1, 'Admin', 'ark.anike', 'Anike Jefany', 'Admin THG'),
            (2, 'Cashier', 'ark.anike', 'ark.ilham', 'Kasir Outlet'),
            (5, 'Waiter', 'ark.ilham', 'ark.ilham', 'Waiter Outlet'),
            (6, 'Admin Hotel', 'ark.ilham', 'Anike Jefany', 'Admin Hotel'),
            (13, 'Admin Outlet', 'ark.ilham', 'ark.ilham', 'Admin Outlet');
		";
		DB::statement($sql);
    }
}
