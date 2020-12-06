<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_6_3_548 extends GM_Migration {

	/**
	 * Main migrate job
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.6.3.548';

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

			$woo_cart_dropdown_product_name_color =
				empty( $settings['woo_cart_dropdown_product_name_color'] )
					?
					'#ffffff'
					:
					$settings['woo_cart_dropdown_product_name_color'];

			$sub_level_background_color = $style->get( 'general', 'sub_level_background_color' );


			// SET new params.
			$style->set( 'woo_cart_dropdown_bg_color', $sub_level_background_color );
			$style->set( 'woo_cart_dropdown_text_color', $woo_cart_dropdown_product_name_color );


			// Save new params.
			$style->update();

			// Update version.
			update_post_meta( $preset->id, 'gm_version', '1.6.3.548' );

		}


		$this->success();

		return true;

	}

}
