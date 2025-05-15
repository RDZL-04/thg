<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
		INSERT IGNORE INTO `role_permission` (`permission_name`, `description`, `created_at`, `updated_at`) VALUES
			('hotel-list', 'Access to hotel menu list', NULL, NULL),
			('outlet-list', 'Access to outlet menu list', NULL, NULL),
			('mice-category-list', 'Access to mice category list', NULL, NULL),
			('mice-hall-list', 'Access to mice hall list', NULL, NULL),
			('mice-list', 'Access to mice menu list', NULL, NULL),
			('outlet-menu-category-list', 'Access to outlet menu category list', NULL, NULL),
			('outlet-promo-list', 'Access to outlet promo list', NULL, NULL),
			('outlet-table-list', 'Access outlet table list', NULL, NULL),
			('utility-list', 'Access to utility menu list', NULL, NULL),
			('utility-role-list', 'Access to utility role list', NULL, NULL),
			('utility-system-list', 'Access to utility system list', NULL, NULL),
			('utility-user-list', 'Access to utility user list', NULL, NULL),
			('utility-permission-list', 'Access to utility permission list', NULL, NULL),
			('utility-permission-create', 'Set permission access', NULL, NULL),
			('hotel-create', 'Create a new hotel', NULL, NULL),
			('hotel-update', 'Update hotel informations', NULL, NULL),
			('hotel-delete', 'Delete hotel', NULL, NULL),
			('hotel-apikey-update', 'Update hotel APIKEY informations', NULL, NULL),
			('hotel-user-add', 'Add user hotel', NULL, NULL),
			('hotel-user-delete', 'Delete user hotel', NULL, NULL),
			('hotel-facility-add', 'Add hotel facilities', NULL, NULL),
			('hotel-facility-delete', 'Delete hotel facilities', NULL, NULL),
			('hotel-facility-update', 'Update hotel facilities', NULL, NULL),
			('hotel-image-add', 'Add hotel images', NULL, NULL),
			('hotel-image-delete', 'Delete hotel images', NULL, NULL),
			('hotel-image-update', 'Update hotel images', NULL, NULL);
		";
		DB::statement($sql);
    }
}
