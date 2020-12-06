<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_2_15_297 extends GM_Migration {

	/**
	 * @return bool
	 */
	function migrate() {

		$this->db_version = '1.2.15.297';


		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {

			delete_option( GroovyMenuStyle::OPTION_NAME . '_screenshot_' . $preset->id );

		}


		$this->success();

		return true;

	}

}
