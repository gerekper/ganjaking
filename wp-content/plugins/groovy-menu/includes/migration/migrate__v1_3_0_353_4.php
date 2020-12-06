<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_3_0_353_4 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.3.0.353.4';

		// Clear store compiled settings in cache.
		GroovyMenuStyleStorage::getInstance()->remove_preset_settings();
		GroovyMenuStyleStorage::getInstance()->remove_global_settings();

		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {

			$style       = new GroovyMenuStyle( $preset->id );
			$header      = $style->get( 'general', 'header' );
			$mobile_type = $style->get( 'general', 'minimalistic_menu_open_type' );

			if ( empty( $header ) || ! is_array( $header ) ) {
				$header = array(
					'align'   => 'left',
					'style'   => 1,
					'toolbar' => 'false',
				);
			}

			// Conditions.
			if ( isset( $header['style'] ) && 2 === $header['style'] ) {
				if ( 'left' === $header['align'] && 'offcanvasSlideLeft' === $mobile_type ) {
					$mobile_type = 'offcanvasSlideSlideRight';
				}
			}

			// SET new params.
			$style->set( 'minimalistic_menu_open_type', $mobile_type );

			// Save new params.
			$style->update();
		}


		$this->success();

		return true;

	}

}
