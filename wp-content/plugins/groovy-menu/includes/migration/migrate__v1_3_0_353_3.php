<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_3_0_353_3 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.3.0.353.3';

		// Clear store compiled settings in cache.
		GroovyMenuStyleStorage::getInstance()->remove_preset_settings();
		GroovyMenuStyleStorage::getInstance()->remove_global_settings();

		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {

			$style      = new GroovyMenuStyle( $preset->id );
			$preset_obj = new GroovyMenuPreset( $preset->id );
			$settings   = get_option( GroovyMenuStyle::getOptionName( $preset_obj ) );

			$mobile_type =
				empty( $settings['general']['fields']['mobile_type']['value'] )
					?
					'offcanvasSlideLeft'
					:
					$settings['general']['fields']['mobile_type']['value'];


			// SET new params.
			$style->set( 'minimalistic_menu_open_type', $mobile_type );


			// Save new params.
			$style->update();
		}


		$this->success();

		return true;

	}

}
