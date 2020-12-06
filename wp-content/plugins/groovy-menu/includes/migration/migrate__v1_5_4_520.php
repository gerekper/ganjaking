<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_5_4_520 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.5.4.520';

		GroovyMenuStyleStorage::getInstance()->set_disable_storage();

		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {
			$style = new GroovyMenuStyle( $preset->id );
			$style->update();
			update_post_meta( intval( $preset->id ), 'gm_version', '1.5.4.520' );
		}

		$this->success();

		return true;

	}

}
