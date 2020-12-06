<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_3_0_353_2 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.3.0.353.2';

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

			$header = $style->get( 'general', 'header' );

			if ( empty( $header ) || ! is_array( $header ) ) {
				$header = array(
					'align'   => 'left',
					'style'   => 1,
					'toolbar' => 'false',
				);
			}


			// Conditions.
			if ( isset( $header['style'] ) && $header['style'] === 2 ) {

				if ( $header['align'] === 'left' && $mobile_type === 'offcanvasSlideLeft' ) {
					$mobile_type = 'offcanvasSlideRight';
				} elseif ( $header['align'] === 'center' && $mobile_type === 'offcanvasSlideLeft' ) {
					$mobile_type = 'offcanvasSlideRight';
				} elseif ( $header['align'] === 'right' && $mobile_type === 'offcanvasSlideLeft' ) {
					$mobile_type = 'offcanvasSlideLeft';
				} elseif ( $header['align'] === 'left' && $mobile_type === 'offcanvasSlideSlide' ) {
					$mobile_type = 'offcanvasSlideRight';
				} elseif ( $header['align'] === 'center' && $mobile_type === 'offcanvasSlideSlide' ) {
					$mobile_type = 'offcanvasSlideSlideRight';
				} elseif ( $header['align'] === 'right' && $mobile_type === 'offcanvasSlideSlide' ) {
					$mobile_type = 'offcanvasSlideSlide';
				}

			}


			// SET new params.
			$style->set( 'mobile_type', $mobile_type );
			$style->set( 'minimalistic_menu_open_type', $mobile_type );


			// Save new params.
			$style->update();

		}


		$this->success();

		return true;

	}

}
