<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_2_15_289 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.2.15.289';


		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {
			$style = new GroovyMenuStyle( $preset->id );

			$mega_menu_show_links_bottom_border = $style->get( 'general', 'mega_menu_show_links_bottom_border' );


			if ( 'true' === $mega_menu_show_links_bottom_border ) {
				$mega_menu_show_links_bottom_border = 1;
			} elseif ( 'false' === $mega_menu_show_links_bottom_border ) {
				$mega_menu_show_links_bottom_border = 0;
			}

			$mega_menu_show_links_bottom_border = $mega_menu_show_links_bottom_border ? false : true;


			$style->set( 'mega_menu_show_links_bottom_border', $mega_menu_show_links_bottom_border );
			$style->update();
		}


		$this->success();

		return true;

	}

}
