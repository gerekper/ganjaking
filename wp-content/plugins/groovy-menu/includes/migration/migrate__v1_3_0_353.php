<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_3_0_353 extends GM_Migration {

	/**
	 * Main migrate job
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.3.0.353';

		// Clear store compiled settings in cache.
		GroovyMenuStyleStorage::getInstance()->remove_preset_settings();
		GroovyMenuStyleStorage::getInstance()->remove_global_settings();

		$presets = GroovyMenuPreset::getAll();

		foreach ( $presets as $preset ) {

			$style      = new GroovyMenuStyle( $preset->id );
			$preset_obj = new GroovyMenuPreset( $preset->id );
			$settings   = get_option( GroovyMenuStyle::getOptionName( $preset_obj ) );

			$mobile_skin =
				empty( $settings['mobile']['fields']['mobile_skin']['value'] )
					?
					'mobileLight'
					:
					$settings['mobile']['fields']['mobile_skin']['value'];

			$header                                 = $style->get( 'general', 'header' );
			$show_divider                           = $style->get( 'general', 'show_divider' );
			$show_divider_between_menu_links        = $style->get( 'general', 'show_divider_between_menu_links' );
			$woocommerce_cart                       = $style->get( 'general', 'woocommerce_cart' );
			$search_form                            = $style->get( 'general', 'search_form' );
			$responsive_navigation_background_color = $style->get( 'mobile', 'responsive_navigation_background_color' );
			$responsive_navigation_text_color       = $style->get( 'mobile', 'responsive_navigation_text_color' );
			$responsive_navigation_hover_text_color = $style->get( 'mobile', 'responsive_navigation_hover_text_color' );
			$responsive_navigation_menu_title_color = $style->get( 'mobile', 'responsive_navigation_menu_title_color' );

			if ( empty( $header ) || ! is_array( $header ) ) {
				$header = array(
					'align'   => 'left',
					'style'   => 1,
					'toolbar' => 'false',
				);
			}

			// Conditions.
			if ( 1 !== $header['style'] ) {
				$show_divider                    = 0;
				$show_divider_between_menu_links = 0;
			} elseif ( 1 === $header['style'] && 'center' === $header['align'] ) {
				$show_divider     = 0;
				$woocommerce_cart = 0;
				$search_form      = 'disable';
			}

			if ( true === boolval( $show_divider ) && ( 'disable' === $search_form && false === boolval( $woocommerce_cart ) ) ) {
				$show_divider = 0;
			}

			if ( 'mobileLight' === $mobile_skin ) {
				$responsive_navigation_background_color = '#ffffff';
				$responsive_navigation_text_color       = '#5a5a5a';
				$responsive_navigation_hover_text_color = '#5a5a5a';
				$responsive_navigation_menu_title_color = '#5a5a5a';
			}

			if ( 'mobileDark' === $mobile_skin ) {
				$responsive_navigation_background_color = '#222323';
				$responsive_navigation_text_color       = '#6e6e6f';
				$responsive_navigation_hover_text_color = '#6e6e6f';
				$responsive_navigation_menu_title_color = '#6e6e6f';
			}

			// SET new params.
			$style->set( 'show_divider', $show_divider );
			$style->set( 'show_divider_between_menu_links', $show_divider_between_menu_links );
			$style->set( 'woocommerce_cart', $woocommerce_cart );
			$style->set( 'search_form', $search_form );
			$style->set( 'responsive_navigation_background_color', $responsive_navigation_background_color );
			$style->set( 'responsive_navigation_text_color', $responsive_navigation_text_color );
			$style->set( 'responsive_navigation_hover_text_color', $responsive_navigation_hover_text_color );
			$style->set( 'responsive_navigation_menu_title_color', $responsive_navigation_menu_title_color );


			// Save new params.
			$style->update();

		}


		$this->success();

		return true;

	}

}
