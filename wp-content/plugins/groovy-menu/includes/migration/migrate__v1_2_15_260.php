<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_2_15_260 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.2.15.260';


		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {
			$style         = new GroovyMenuStyle( $preset->id );
			$sticky_header = $style->get( 'general', 'sticky_header' );
			$sticky_offset = $style->get( 'general', 'sticky_offset' );

			$style->set( 'sticky_header_mobile', $sticky_header );
			$style->set( 'sticky_offset_mobile', $sticky_offset );
			$style->update();
		}


		$this->success();

		return true;

	}

}
