<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_9_2_753 extends GM_Migration {

	/**
	 * Main migrate job
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.9.2.753';

		// Clear store compiled settings in cache.
		GroovyMenuStyleStorage::getInstance()->remove_preset_settings();
		GroovyMenuStyleStorage::getInstance()->remove_global_settings();
		GroovyMenuStyleStorage::getInstance()->set_disable_storage();

		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {

			$style      = new GroovyMenuStyle( $preset->id );
			$preset_obj = new GroovyMenuPreset( $preset->id );


			$settings = get_post_meta( $preset->id, 'gm_preset_settings', true );
			$settings = json_decode( $settings, true );
			if ( empty( $settings ) || ! is_array( $settings ) ) {
				$settings = array();
			}

			$menu_z_index =
				empty( $settings['menu_z_index'] )
					?
					'9999'
					:
					$settings['menu_z_index'];

			// SET new params.
			$style->set( 'menu_z_index', $menu_z_index );

			// Save new params.
			$style->update();

			// Update version.
			update_post_meta( $preset->id, 'gm_version', '1.9.2.753' );
			delete_post_meta( $preset->id, 'gm_compiled_css' );

		}


		$this->success();

		return true;

	}

}
